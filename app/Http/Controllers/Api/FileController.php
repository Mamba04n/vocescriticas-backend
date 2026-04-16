<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    private function normalizedPath(Request $request): ?string
    {
        $path = trim((string) $request->query('path', ''));
        $path = rawurldecode($path);
        $path = ltrim($path, '/\\');

        if ($path === '' || str_contains($path, '..')) {
            return null;
        }

        return $path;
    }

    private function diskName(): string
    {
        return (string) config('filesystems.default', 'public');
    }

    public function open(Request $request): StreamedResponse
    {
        $path = $this->normalizedPath($request);
        abort_if(!$path, 404, 'Archivo no encontrado.');

        $disk = Storage::disk($this->diskName());
        abort_if(!$disk->exists($path), 404, 'Archivo no encontrado.');

        $mime = $disk->mimeType($path) ?: 'application/octet-stream';

        return $disk->response($path, basename($path), [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }

    public function download(Request $request): StreamedResponse
    {
        $path = $this->normalizedPath($request);
        abort_if(!$path, 404, 'Archivo no encontrado.');

        $disk = Storage::disk($this->diskName());
        abort_if(!$disk->exists($path), 404, 'Archivo no encontrado.');

        return $disk->download($path, basename($path));
    }
}
