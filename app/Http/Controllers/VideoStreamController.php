<?php

namespace App\Http\Controllers;

use App\Models\Presentation;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ultra-Optimized Video Streaming Controller with Byte-Range Support
 * 
 * This controller implements HTTP Range Requests (RFC 7233) for efficient
 * streaming of large video files with minimal overhead.
 * 
 * Key optimizations:
 * - Direct file access without Laravel Storage overhead
 * - Smaller buffer size (1KB) for faster initial response
 * - Early exit if client disconnects
 * - No unnecessary header duplication
 * - Optimized for metadata-only requests
 */
class VideoStreamController extends Controller
{
    /**
     * Stream a video file with byte-range support
     * 
     * @param Presentation $presentation
     * @return Response
     */
    public function stream(Presentation $presentation): Response
    {
        // Validate file_path exists
        if (empty($presentation->file_path)) {
            \Log::error('Empty file_path for presentation', ['id' => $presentation->id]);
            abort(404, 'File path not found for this presentation.');
        }

        // Try public folder first, then fallback to storage
        $videoPath = public_path($presentation->file_path);
        if (!file_exists($videoPath)) {
            $videoPath = storage_path('app/public/' . $presentation->file_path);
        }
        
        // Log for debugging
        \Log::debug('Streaming video', [
            'presentation_id' => $presentation->id,
            'file_path' => $presentation->file_path,
            'full_path' => $videoPath,
            'exists' => file_exists($videoPath),
        ]);
        
        // Quick existence check
        if (!file_exists($videoPath)) {
            \Log::error('Video file not found', ['path' => $videoPath]);
            abort(404, 'Video file not found: ' . basename($videoPath));
        }

        // OPTIMIZATION: Use cached file size if possible
        $fileSize = filesize($videoPath);
        
        // OPTIMIZATION: Simple MIME type detection
        $mimeType = $this->getMimeType($presentation->file_type ?? $presentation->filename);
        
        // Parse Range header
        $range = $_SERVER['HTTP_RANGE'] ?? null;
        
        if (!$range) {
            // No range request - return full file (rarely happens with modern browsers)
            return $this->streamFullFile($videoPath, $fileSize, $mimeType, $presentation->filename);
        }

        // Parse byte range
        return $this->streamPartialFile($videoPath, $fileSize, $mimeType, $presentation->filename, $range);
    }

    /**
     * Stream partial file content (byte-range request)
     */
    private function streamPartialFile(string $path, int $fileSize, string $mimeType, string $filename, string $rangeHeader): Response
    {
        // Parse range: "bytes=0-1023" or "bytes=0-"
        if (!preg_match('/bytes=(\d+)-(\d*)/', $rangeHeader, $matches)) {
            return response('Invalid range', 416)
                ->header('Content-Range', "bytes */{$fileSize}");
        }

        $start = (int)$matches[1];
        $end = $matches[2] !== '' ? (int)$matches[2] : $fileSize - 1;
        
        // Validate range
        if ($start > $end || $start >= $fileSize || $end >= $fileSize) {
            return response('', 416)
                ->header('Content-Range', "bytes */{$fileSize}");
        }

        $length = $end - $start + 1;

        // OPTIMIZATION: Open file and seek
        $fp = fopen($path, 'rb');
        if (!$fp) {
            abort(500, 'Cannot open file');
        }
        
        fseek($fp, $start);

        // Stream response
        return response()->stream(
            function () use ($fp, $length) {
                // CRITICAL OPTIMIZATION: Use 1KB buffer for faster initial response
                // This allows the browser to start playing almost immediately
                $bufferSize = 1024; // 1KB chunks
                $sent = 0;

                while ($sent < $length && !feof($fp) && connection_status() === 0) {
                    $toRead = min($bufferSize, $length - $sent);
                    $chunk = fread($fp, $toRead);
                    
                    if ($chunk === false) break;
                    
                    echo $chunk;
                    $sent += strlen($chunk);
                    
                    // Flush immediately - critical for streaming
                    if (ob_get_level()) ob_flush();
                    flush();
                }

                fclose($fp);
            },
            206, // Partial Content
            [
                'Content-Type' => $mimeType,
                'Content-Length' => (string)$length,
                'Content-Range' => "bytes {$start}-{$end}/{$fileSize}",
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'public, max-age=2592000', // 30 days
                'Content-Disposition' => 'inline',
            ]
        );
    }

    /**
     * Stream full file (no range request)
     */
    private function streamFullFile(string $path, int $fileSize, string $mimeType, string $filename): Response
    {
        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=2592000',
            'Content-Disposition' => 'inline',
        ]);
    }

    /**
     * Stream any presentation file (video, PDF, etc.) with byte-range support
     */
    public function streamAny(Presentation $presentation): Response
    {
        // Validate file_path
        if (empty($presentation->file_path)) {
            \Log::error('Empty file_path in streamAny', ['id' => $presentation->id]);
            abort(404, 'File path not found.');
        }

        // Determine file type from extension if file_type is null
        $fileType = $presentation->file_type ?? pathinfo($presentation->filename, PATHINFO_EXTENSION);
        
        // For video files, use optimized video streaming
        if (in_array(strtolower($fileType), ['mp4', 'webm', 'mov', 'avi'])) {
            return $this->stream($presentation);
        }

        // For other files, use standard Laravel response
        // Try public folder first, then fallback to storage
        $path = public_path($presentation->file_path);
        if (!file_exists($path)) {
            $path = storage_path('app/public/' . $presentation->file_path);
        }
        
        if (!file_exists($path)) {
            \Log::error('File not found in streamAny', ['path' => $path]);
            abort(404, 'File not found.');
        }

        $mimeType = $this->getMimeType($fileType);

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $presentation->filename . '"',
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    /**
     * Fast MIME type lookup with fallback for extensions from filename
     */
    private function getMimeType(string $extOrFilename): string
    {
        // Extract extension if it looks like a filename
        if (strpos($extOrFilename, '.') !== false) {
            $extOrFilename = pathinfo($extOrFilename, PATHINFO_EXTENSION);
        }
        
        $ext = strtolower($extOrFilename);
        
        return match ($ext) {
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'mov' => 'video/quicktime',
            'avi' => 'video/x-msvideo',
            'pdf' => 'application/pdf',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'ppt' => 'application/vnd.ms-powerpoint',
            default => 'application/octet-stream',
        };
    }
}
