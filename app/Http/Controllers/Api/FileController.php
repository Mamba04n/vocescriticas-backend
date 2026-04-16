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

    private function resolveDiskForPath(string $path): string
    {
        $candidates = array_values(array_unique([
            $this->diskName(),
            'public',
            'local',
            's3',
        ]));

        foreach ($candidates as $diskName) {
            try {
                if (Storage::disk($diskName)->exists($path)) {
                    return $diskName;
                }
            } catch (\Throwable $e) {
                // Ignore unavailable disk drivers and continue trying fallbacks.
            }
        }

        return $this->diskName();
    }

    public function open(Request $request): StreamedResponse
    {
        $path = $this->normalizedPath($request);
        abort_if(!$path, 404, 'Archivo no encontrado.');

        $disk = Storage::disk($this->resolveDiskForPath($path));
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

        $disk = Storage::disk($this->resolveDiskForPath($path));
        abort_if(!$disk->exists($path), 404, 'Archivo no encontrado.');

        return $disk->download($path, basename($path));
    }
}
