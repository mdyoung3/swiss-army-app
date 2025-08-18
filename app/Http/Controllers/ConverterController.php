<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class ConverterController extends Controller
{
    public function converter(Request $request)
    {
        $url = $request->url;

        // Save directly to public directory
        $outputPath = public_path('downloads/mp3s');

        // Ensure directory exists
        if (!file_exists($outputPath)) {
            mkdir($outputPath, 0755, true);
        }

        $programPath = config('converter.ytdlp');
        $pathToFile = $outputPath . '/%(title)s.%(ext)s';

        $command = [
            $programPath,
            '--extract-audio',
            '--audio-format', 'mp3',
            '--output', $pathToFile,
            '--no-playlist',
            $url
        ];

        $result = Process::timeout(600)->run($command);

        if ($result->successful()) {
            $files = glob($outputPath . '/*.mp3');

            if (!empty($files)) {
                $latestFile = array_reduce($files, function($a, $b) {
                    return filemtime($a) > filemtime($b) ? $a : $b;
                });

                $fileName = basename($latestFile);

                return [
                    'success' => true,
                    'file_name' => $fileName,
                    'file_url' => asset('downloads/mp3s/' . $fileName), // Public URL
                    'file_size' => filesize($latestFile),
                ];
            }
        }

        return [
            'success' => false,
            'error' => $result->errorOutput(),
        ];
    }}
