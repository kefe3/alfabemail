<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ZipController extends Controller
{
    private const MAX_FILE_SIZE = 50 * 1024 * 1024;

    public function extract(Request $request): JsonResponse
    {
        if (!Auth::check() || !Auth::user()->hasRole('ogrenci')) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 401);
        }

        $request->validate([
            'zipfile' => 'required|file|mimes:zip|max:' . (self::MAX_FILE_SIZE / 1024),
        ]);

        $file = $request->file('zipfile');

        $sessionId = bin2hex(random_bytes(8)) . '_' . time();
        $extractDir = 'zip-extracted/' . $sessionId . '/';

        $zip = new \ZipArchive();
        $uploadPath = $file->getPathname();

        if ($zip->open($uploadPath) !== true) {
            return response()->json([
                'success' => false,
                'message' => 'ZIP dosyası açılamadı. Dosya bozuk veya şifreli olabilir.',
            ], 400);
        }

        $zip->extractTo(storage_path('app/public/' . $extractDir));
        $zip->close();

        $files = [];
        $totalSize = 0;
        $fileCount = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                storage_path('app/public/' . $extractDir),
                \RecursiveDirectoryIterator::SKIP_DOTS
            )
        );

        foreach ($iterator as $f) {
            $relativePath = str_replace(storage_path('app/public/' . $extractDir), '', $f->getPathname());
            $relativePath = ltrim($relativePath, '\\/');
            $isDir = $f->isDir();
            $size = $f->isFile() ? $f->getSize() : 0;

            $files[] = [
                'path' => $relativePath,
                'size' => $size,
                'isDir' => $isDir,
                'url' => $isDir ? null : asset('storage/' . $extractDir . str_replace('\\', '/', $relativePath)),
            ];

            if (!$isDir) {
                $fileCount++;
                $totalSize += $size;
            }
        }

        return response()->json([
            'success' => true,
            'session_id' => $sessionId,
            'files' => $files,
            'file_count' => $fileCount,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
        ]);
    }

    public function extractFromUrl(Request $request): JsonResponse
    {
        if (!Auth::check() || !Auth::user()->hasRole('ogrenci')) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 401);
        }

        $request->validate([
            'url' => 'required|string',
        ]);

        $url = $request->url;
        $localPath = $this->resolveStoragePath($url);

        if (!$localPath || !file_exists($localPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Dosya bulunamadı.',
            ], 404);
        }

        if (strtolower(pathinfo($localPath, PATHINFO_EXTENSION)) !== 'zip') {
            return response()->json([
                'success' => false,
                'message' => 'Dosya ZIP formatında değil.',
            ], 400);
        }

        $sessionId = bin2hex(random_bytes(8)) . '_' . time();
        $extractDir = 'zip-extracted/' . $sessionId . '/';
        $fullExtractDir = storage_path('app/public/' . $extractDir);

        $zip = new \ZipArchive();
        if ($zip->open($localPath) !== true) {
            return response()->json([
                'success' => false,
                'message' => 'ZIP dosyası açılamadı.',
            ], 400);
        }

        $zip->extractTo($fullExtractDir);
        $zip->close();

        $files = [];
        $fileCount = 0;
        $totalSize = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($fullExtractDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $f) {
            $relativePath = ltrim(str_replace($fullExtractDir, '', $f->getPathname()), '\\/');
            $isDir = $f->isDir();
            $size = $f->isFile() ? $f->getSize() : 0;

            $files[] = [
                'path' => $relativePath,
                'size' => $size,
                'isDir' => $isDir,
                'url' => $isDir ? null : asset('storage/' . $extractDir . str_replace('\\', '/', $relativePath)),
            ];

            if (!$isDir) {
                $fileCount++;
                $totalSize += $size;
            }
        }

        return response()->json([
            'success' => true,
            'session_id' => $sessionId,
            'files' => $files,
            'file_count' => $fileCount,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
        ]);
    }

    private function resolveStoragePath(string $url): ?string
    {
        $storageUrl = rtrim(config('app.url'), '/') . '/storage/';
        $relativePath = str_replace($storageUrl, '', $url);
        $relativePath = str_replace('/storage/', '', $relativePath);
        $relativePath = ltrim($relativePath, '/');

        $candidates = [
            storage_path('app/public/' . $relativePath),
            storage_path('app/public/attachments/' . basename($relativePath)),
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
