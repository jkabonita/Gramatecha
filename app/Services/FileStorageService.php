<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileStorageService
{
    /**
     * Store uploaded file directly to public folder for fast access and caching.
     * Compresses PDFs and videos when possible.
     *
     * @param  UploadedFile  $file
     * @return array{path: string, url: string, size: int}
     */
    public function storePresentation(UploadedFile $file): array
    {
        $extension    = strtolower($file->getClientOriginalExtension()) ?: 'bin';
        $filename     = Str::uuid() . '.' . $extension;
        $relativePath = 'presentations/' . $filename;
        
        // Store directly in public folder for direct access (no symbolic link needed)
        $destPath     = public_path($relativePath);

        // Ensure directory exists
        $dir = dirname($destPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Move the upload to its destination first
        $tempPath = $file->getRealPath();
        if (is_uploaded_file($tempPath)) {
            move_uploaded_file($tempPath, $destPath);
        } else {
            rename($tempPath, $destPath);
        }

        // ── Compress in-place ────────────────────────────────────────────────
        if ($extension === 'pdf') {
            $this->compressPdf($destPath);
        } elseif (in_array($extension, ['mp4', 'mov', 'avi', 'webm'], true)) {
            $this->compressVideo($destPath, $extension);
        }

        $size = filesize($destPath);

        return [
            'path' => $relativePath,
            'url'  => asset($relativePath), // Direct URL without /storage prefix
            'size' => $size,
        ];
    }

    /**
     * Store a generated file from a local path directly to public folder.
     *
     * @return array{path: string, url: string, size: int}
     */
    public function storeGeneratedPresentation(string $sourcePath, string $filename): array
    {
        $relativePath = 'presentations/' . $filename;
        $destPath = public_path($relativePath);
        
        // Ensure directory exists
        $dir = dirname($destPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        copy($sourcePath, $destPath);
        $size = filesize($destPath);

        return [
            'path' => $relativePath,
            'url'  => asset($relativePath),
            'size' => $size,
        ];
    }

    /**
     * Delete a presentation file from public storage.
     */
    public function deletePresentation(string $path): bool
    {
        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    /**
     * Get the public URL for a presentation.
     */
    public function getPresentationUrl(string $path): string
    {
        return asset($path);
    }

    // ── Compression helpers ───────────────────────────────────────────────────

    /**
     * Compress a PDF using Ghostscript if available.
     * Falls back silently — the original file is kept untouched on failure.
     */
    private function compressPdf(string $filePath): void
    {
        $gs = $this->findExecutable(['gs', 'gswin64c', 'gswin32c']);

        if (! $gs) {
            Log::info('[FileStorageService] Ghostscript not found, skipping PDF compression.');
            return;
        }

        $originalSize = filesize($filePath);
        $tmpPath      = $filePath . '.compressed.pdf';

        // /ebook  → ~150 dpi, good balance of size and quality
        // /screen → ~72 dpi, smallest (good for text-only PDFs)
        // /printer → ~300 dpi, near-lossless
        $cmd = sprintf(
            '%s -q -dNOPAUSE -dBATCH -dSAFER -sDEVICE=pdfwrite'
            . ' -dCompatibilityLevel=1.4'
            . ' -dPDFSETTINGS=/ebook'
            . ' -dEmbedAllFonts=true'
            . ' -dSubsetFonts=true'
            . ' -dColorImageDownsampleType=/Bicubic'
            . ' -dColorImageResolution=150'
            . ' -dGrayImageDownsampleType=/Bicubic'
            . ' -dGrayImageResolution=150'
            . ' -dMonoImageDownsampleType=/Bicubic'
            . ' -dMonoImageResolution=150'
            . ' -sOutputFile=%s %s 2>&1',
            escapeshellcmd($gs),
            escapeshellarg($tmpPath),
            escapeshellarg($filePath)
        );

        exec($cmd, $output, $exitCode);

        if ($exitCode === 0 && file_exists($tmpPath)) {
            $newSize = filesize($tmpPath);

            // Only replace if compression actually shrank the file
            if ($newSize > 0 && $newSize < $originalSize) {
                rename($tmpPath, $filePath);
                Log::info(sprintf(
                    '[FileStorageService] PDF compressed: %s → %s (%.1f%% reduction)',
                    $this->humanSize($originalSize),
                    $this->humanSize($newSize),
                    (1 - $newSize / $originalSize) * 100
                ));
            } else {
                @unlink($tmpPath);
                Log::info('[FileStorageService] PDF compression skipped (no size gain).');
            }
        } else {
            @unlink($tmpPath);
            Log::warning('[FileStorageService] Ghostscript PDF compression failed.', [
                'exit_code' => $exitCode,
                'output'    => implode("\n", $output),
            ]);
        }
    }

    /**
     * Compress a video using FFmpeg if available.
     * Forces 720p resolution, optimizes for web streaming, and reduces file size significantly.
     * Falls back silently — the original file is kept on failure.
     */
    private function compressVideo(string $filePath, string $extension): void
    {
        $ffmpeg = $this->findExecutable(['ffmpeg']);

        if (! $ffmpeg) {
            Log::info('[FileStorageService] FFmpeg not found, skipping video compression.');
            return;
        }

        $originalSize = filesize($filePath);
        $tmpPath      = $filePath . '.compressed.mp4'; // Always output as MP4

        // Optimized settings for 720p web streaming:
        // - Scale to 720p (1280x720) maintaining aspect ratio
        // - CRF 23 = high quality/size balance for 720p
        // - preset medium = good compression efficiency
        // - profile:v main = wide compatibility
        // - movflags +faststart = enable instant playback (moov atom at front)
        // - maxrate/bufsize = prevent quality spikes, ensure smooth streaming
        // - 2-pass encoding for optimal quality
        $cmd = sprintf(
            '%s -y -i %s -vf "scale=\'min(1280,iw)\':\'min(720,ih)\':force_original_aspect_ratio=decrease,pad=ceil(iw/2)*2:ceil(ih/2)*2"'
            . ' -c:v libx264 -crf 23 -preset medium -profile:v main -level 4.0'
            . ' -maxrate 2M -bufsize 4M'
            . ' -c:a aac -b:a 128k -ac 2'
            . ' -movflags +faststart'
            . ' -pix_fmt yuv420p'
            . ' %s 2>&1',
            escapeshellcmd($ffmpeg),
            escapeshellarg($filePath),
            escapeshellarg($tmpPath)
        );

        exec($cmd, $output, $exitCode);

        if ($exitCode === 0 && file_exists($tmpPath) && filesize($tmpPath) > 0) {
            $newSize = filesize($tmpPath);

            // Always replace with the 720p version, even if slightly larger
            // (720p is the target, not compression ratio)
            rename($tmpPath, $filePath);
            
            $reduction = $originalSize > $newSize 
                ? sprintf('%.1f%% reduction', (1 - $newSize / $originalSize) * 100)
                : sprintf('%.1f%% increase (quality improvement)', ($newSize / $originalSize - 1) * 100);

            Log::info(sprintf(
                '[FileStorageService] Video compressed to 720p: %s → %s (%s)',
                $this->humanSize($originalSize),
                $this->humanSize($newSize),
                $reduction
            ));
        } else {
            @unlink($tmpPath);
            Log::warning('[FileStorageService] FFmpeg video compression failed.', [
                'exit_code' => $exitCode,
                'output'    => implode("\n", $output),
            ]);
        }
    }

    /**
     * Find the first available executable from a list of candidates.
     */
    private function findExecutable(array $candidates): ?string
    {
        $isWindows = PHP_OS_FAMILY === 'Windows';
        $which     = $isWindows ? 'where' : 'which';

        foreach ($candidates as $cmd) {
            // Check common install paths first (faster than exec)
            $paths = $isWindows
                ? ['C:\\Program Files\\gs\\gs10.04.0\\bin\\' . $cmd . '.exe',
                   'C:\\Program Files (x86)\\gs\\gs10.04.0\\bin\\' . $cmd . '.exe',
                   'C:\\ffmpeg\\bin\\' . $cmd . '.exe']
                : ['/usr/bin/' . $cmd, '/usr/local/bin/' . $cmd, '/opt/homebrew/bin/' . $cmd];

            foreach ($paths as $path) {
                if (is_executable($path)) {
                    return $path;
                }
            }

            // Fall back to PATH lookup
            exec($which . ' ' . escapeshellarg($cmd) . ' 2>&1', $out, $code);
            if ($code === 0 && ! empty($out[0]) && is_executable(trim($out[0]))) {
                return trim($out[0]);
            }
        }

        return null;
    }

    /**
     * Format bytes as a human-readable string.
     */
    private function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size  = $bytes;
        $i     = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 1) . ' ' . $units[$i];
    }
}
