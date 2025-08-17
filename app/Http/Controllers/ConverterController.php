<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class ConverterController extends Controller
{
    public function converter(Request $request)
    {
        $url = $request->url;
//        return response()->json($url);

        $outputPath = storage_path('app/downloads/mp3s');
        $pathToFile = $outputPath . '/%(title)s.%(ext)s';

        $command = [
            '/home/mdyoung3/.local/bin/yt-dlp',
            '--extract-audio',
            '--audio-format', 'mp3',
            '--output', $pathToFile,
            '--no-playlist',
            $url
        ];

        $result = Process::run($command);

        return [
            'success' => $result->successful(),
            'file_path' => $pathToFile,     // Template path
            'output' => $result->output(),
            'error' => $result->errorOutput(),
        ];
    }
}
