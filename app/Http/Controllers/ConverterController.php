<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConverterController extends Controller
{
    public function converter(Request $request)
    {
        // Create a unique temp directory
        $tempDir = 'temp/' . Str::uuid();
        Storage::disk('mp3_downloads')->makeDirectory($tempDir);

        try {
            // Download to temp directory
            $tempFileName = $tempDir . '/%(title)s.%(ext)s';
            $outputPath = Storage::disk('mp3_downloads')->path($tempFileName);

            $result = $this->ytDlp($request, $outputPath);

            if ($result->successful()) {
                // Get the actual filename that was created in temp
                $tempFiles = Storage::disk('mp3_downloads')->files($tempDir);
                $tempFile = collect($tempFiles)->first();

                if (!$tempFile) {
                    throw new \Exception('No file was downloaded');
                }

                // Extract just the filename (without temp path)
                $actualFileName = basename($tempFile);

                // Handle hash removal if needed
                if (str_contains($actualFileName, '#')) {
                    $cleanFileName = $this->hashRemovalMachine($tempFile, $tempDir);
                    $actualFileName = basename($cleanFileName);
                    $tempFile = $cleanFileName;
                }

                // Check if file already exists in parent directory
                $existingFile = $this->findExistingFile($actualFileName);

                if ($existingFile) {
                    // File already exists, clean up temp and return existing
                    Storage::disk('mp3_downloads')->deleteDirectory($tempDir);

                    return [
                        'success' => true,
                        'file_name' => $actualFileName,
                        'file_url' => route('mp3.download', ['filename' => $actualFileName]), // Use route instead
                        'file_size' => Storage::disk('mp3_downloads')->size($actualFileName),
                        'duplicate' => false,
                    ];
                } else {
                    // Move from temp to parent directory
                    Storage::disk('mp3_downloads')->move($tempFile, $actualFileName);
                    Storage::disk('mp3_downloads')->deleteDirectory($tempDir);

                    return [
                        'success' => true,
                        'file_name' => $actualFileName,
                        'file_url' => Storage::disk('mp3_downloads')->url($actualFileName),
                        'file_size' => Storage::disk('mp3_downloads')->size($actualFileName),
                        'duplicate' => false,
                    ];
                }
            }

            return [
                'success' => false,
                'error' => $result->errorOutput(),
            ];

        } finally {
            // Cleanup temp directory if it still exists
            if (Storage::disk('mp3_downloads')->exists($tempDir)) {
                Storage::disk('mp3_downloads')->deleteDirectory($tempDir);
            }
        }
    }

    private function findExistingFile($fileName)
    {
        // Get all files in the parent directory (root of mp3_downloads disk)
        $existingFiles = Storage::disk('mp3_downloads')->files();

        // Filter out temp directories and find exact match
        $existingFiles = collect($existingFiles)
            ->filter(function ($file) {
                return !str_starts_with($file, 'temp/');
            });

        // Check for exact filename match
        if ($existingFiles->contains($fileName)) {
            return $fileName;
        }

        // Optional: Check for similar files (same name, different extension, etc.)
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $similarFile = $existingFiles->first(function ($file) use ($baseName) {
            $existingBaseName = pathinfo($file, PATHINFO_FILENAME);
            return $existingBaseName === $baseName;
        });

        return $similarFile;
    }

    private function ytDlp($request, $outputPath)
    {
        $url = $request->url;
        $programPath = config('converter.ytdlp');

        $command = [
            $programPath,
            '--extract-audio',
            '--audio-format', 'mp3',
            '--output', $outputPath,
            '--no-playlist',
            $url
        ];

        return Process::timeout(1200)->run($command);
    }

    private function hashRemovalMachine($tempFilePath, $tempDir)
    {
        $originalFileName = basename($tempFilePath);
        $fileNameWithNoHashes = preg_replace('/#\S+/', '', $originalFileName);

        if (!str_ends_with($fileNameWithNoHashes, '.mp3')) {
            $fileNameWithNoHashes .= '.mp3';
        }

        $newTempPath = $tempDir . '/' . $fileNameWithNoHashes;

        if (Storage::disk('mp3_downloads')->move($tempFilePath, $newTempPath)) {
            return $newTempPath;
        } else {
            throw new \Exception("Failed to rename file");
        }
    }
}
