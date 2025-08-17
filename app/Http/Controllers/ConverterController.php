<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class ConverterController extends Controller
{
    public function converter(Request $request)
    {
        $url = $request->url;
        $outputPath = storage_path('app/downloads/mp3s');
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

        $result = Process::run($command);

        return [
            'success' => $result->successful(),
            'file_path' => $pathToFile,
            'output' => $result->output(),
            'error' => $result->errorOutput(),
        ];
    }
}
