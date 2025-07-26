<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageS3
{
    public static function handle($path, $id)
    {
        $url = "$path/$id";
        $pathExists = Storage::disk('s3')->exists($url);

        if (!$pathExists) {
            abort(404);
        }

        $content = Storage::disk('s3')->get($url);
        $mime = Storage::disk('s3')->mimeType($url);

        return response($content)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . basename($id) . '"');
    }
}
