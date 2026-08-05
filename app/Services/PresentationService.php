<?php

namespace App\Services;

use App\Models\Presentation;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PresentationService
{
    public function __construct(
        protected CloudinaryService $cloudinaryService
    ) {}

    /**
     * Create a new presentation by uploading file to Cloudinary.
     *
     * @param  UploadedFile  $file
     * @param  string  $title
     * @param  string|null  $description
     * @param  User  $uploader
     * @return Presentation
     *
     * @throws \Exception
     */
    public function createPresentation(
        UploadedFile $file,
        string $title,
        ?string $description,
        User $uploader
    ): Presentation {
        return DB::transaction(function () use ($file, $title, $description, $uploader) {
            // Sanitize filename
            $sanitizedFilename = $this->sanitizeFilename($file->getClientOriginalName());
            
            // Upload to Cloudinary
            $uploadResult = $this->cloudinaryService->uploadPresentation($file);
            
            // Create presentation record
            return Presentation::create([
                'title' => $title,
                'description' => $description,
                'filename' => $sanitizedFilename,
                'file_size' => $uploadResult['bytes'],
                'file_type' => strtolower($file->getClientOriginalExtension()),
                'cloudinary_public_id' => $uploadResult['public_id'],
                'cloudinary_url' => $uploadResult['secure_url'],
                'uploaded_by' => $uploader->id,
            ]);
        });
    }

    /**
     * Update presentation metadata (title and description only).
     *
     * @param  Presentation  $presentation
     * @param  string  $title
     * @param  string|null  $description
     * @return Presentation
     */
    public function updatePresentationMetadata(
        Presentation $presentation,
        string $title,
        ?string $description
    ): Presentation {
        $presentation->update([
            'title' => $title,
            'description' => $description,
        ]);

        return $presentation->fresh();
    }

    /**
     * Delete a presentation and its file from Cloudinary.
     *
     * @param  Presentation  $presentation
     * @return bool
     */
    public function deletePresentation(Presentation $presentation): bool
    {
        return DB::transaction(function () use ($presentation) {
            // Delete from Cloudinary first
            $cloudinaryDeleted = $this->cloudinaryService->deletePresentation(
                $presentation->cloudinary_public_id
            );
            
            // Delete database record regardless of Cloudinary result
            // (file might already be deleted or API might be unavailable)
            $presentation->delete();
            
            return true;
        });
    }

    /**
     * Sanitize filename to remove special characters and path traversal attempts.
     *
     * @param  string  $filename
     * @return string
     */
    protected function sanitizeFilename(string $filename): string
    {
        // Remove any directory path
        $filename = basename($filename);
        
        // Remove path traversal attempts
        $filename = str_replace(['../', '..\\', '../', '..\\'], '', $filename);
        
        // Get the file extension
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $nameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
        
        // Remove special characters but keep spaces, hyphens, and underscores
        $nameWithoutExtension = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $nameWithoutExtension);
        
        // Trim spaces and collapse multiple spaces
        $nameWithoutExtension = preg_replace('/\s+/', ' ', trim($nameWithoutExtension));
        
        // Rebuild filename
        return $nameWithoutExtension . ($extension ? '.' . $extension : '');
    }
}
