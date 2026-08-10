<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Presentation;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PresentationService
{
    public function __construct(
        protected FileStorageService $fileStorageService,
        protected PHPPresentationGenerator $presentationGenerator
    ) {}

    /**
     * Create a new presentation by uploading file.
     *
     * @param  UploadedFile  $file
     * @param  string  $title
     * @param  string|null  $description
     * @param  User  $uploader
     * @param  int|null  $categoryId
     * @return Presentation
     *
     * @throws \Exception
     */
    public function createPresentation(
        UploadedFile $file,
        string $title,
        ?string $description,
        User $uploader,
        ?int $categoryId = null
    ): Presentation {
        return DB::transaction(function () use ($file, $title, $description, $uploader, $categoryId) {
            // Sanitize filename
            $originalFilename = $file->getClientOriginalName();
            $sanitizedFilename = $this->sanitizeFilename($originalFilename);
            
            // Store file locally
            $uploadResult = $this->fileStorageService->storePresentation($file);

            $extension = strtolower($file->getClientOriginalExtension());

            // Auto-categorize if no category provided
            if ($categoryId === null) {
                $categoryId = $this->autoDetectCategoryId($extension);
            }
            
            // Create presentation record
            $presentation = Presentation::create([
                'title'       => $title,
                'description' => $description,
                'filename'    => $sanitizedFilename,
                'file_size'   => $uploadResult['size'],
                'file_type'   => $extension,
                'file_path'   => $uploadResult['path'],
                'file_url'    => $uploadResult['url'],
                'uploaded_by' => $uploader->id,
                'category_id' => $categoryId,
            ]);
            
            return $presentation;
        });
    }


    /**
     * Generate a new presentation programmatically using PHPPresentation.
     *
     * @param  string  $title
     * @param  string|null  $description
     * @param  array  $slides  Array of slides with 'title' and 'content'
     * @param  User  $uploader
     * @param  int|null  $categoryId
     * @param  string  $format  'PowerPoint2007' or 'ODPresentation'
     * @return Presentation
     *
     * @throws \Exception
     */
    public function generatePresentation(
        string $title,
        ?string $description,
        array $slides,
        User $uploader,
        ?int $categoryId = null,
        string $format = 'PowerPoint2007'
    ): Presentation {
        return DB::transaction(function () use ($title, $description, $slides, $uploader, $categoryId, $format) {
            // Generate the presentation
            $phpPresentation = $this->presentationGenerator->generatePresentation(
                $title,
                $description,
                $slides
            );
            
            // Create temporary file
            $extension = $this->presentationGenerator->getFileExtension($format);
            $tempPath = sys_get_temp_dir() . '/' . Str::uuid() . '.' . $extension;
            
            $this->presentationGenerator->savePresentation($phpPresentation, $tempPath, $format);
            
            // Generate filename
            $filename = Str::slug($title) . '_' . time() . '.' . $extension;
            
            // Store the generated file
            $uploadResult = $this->fileStorageService->storeGeneratedPresentation(
                $tempPath,
                $filename
            );
            
            // Clean up temporary file
            @unlink($tempPath);
            
            // Create presentation record
            $presentation = Presentation::create([
                'title' => $title,
                'description' => $description,
                'filename' => $filename,
                'file_size' => $uploadResult['size'],
                'file_type' => $extension,
                'file_path' => $uploadResult['path'],
                'file_url' => $uploadResult['url'],
                'uploaded_by' => $uploader->id,
                'category_id' => $categoryId,
            ]);
            
            return $presentation;
        });
    }

    /**
     * Update presentation metadata (title and description only).
     *
     * @param  Presentation  $presentation
     * @param  string  $title
     * @param  string|null  $description
     * @param  int|null  $categoryId
     * @return Presentation
     */
    public function updatePresentationMetadata(
        Presentation $presentation,
        string $title,
        ?string $description,
        ?int $categoryId = null
    ): Presentation {
        $presentation->update([
            'title' => $title,
            'description' => $description,
            'category_id' => $categoryId,
        ]);

        return $presentation->fresh();
    }

    /**
     * Delete a presentation and its file.
     *
     * @param  Presentation  $presentation
     * @return bool
     */
    public function deletePresentation(Presentation $presentation): bool
    {
        return DB::transaction(function () use ($presentation) {
            // Delete from local storage first
            $fileDeleted = $this->fileStorageService->deletePresentation(
                $presentation->file_path
            );
            
            // Delete database record regardless of file result
            // (file might already be deleted or unavailable)
            $presentation->delete();
            
            return true;
        });
    }

    /**
     * Auto-detect the best category ID based on file extension.
     *
     * - Videos (.mp4 / .mov / .avi / .webm) → "Videos" category (or its first active sub-category)
     * - Everything else → "Reading Materials" category (or its first active sub-category)
     *
     * With the new hierarchical system, if the matched parent has children (sub-categories),
     * we use the first active child so the file lands in a selectable leaf category.
     * If the parent has no children it is itself a leaf, so we use the parent directly.
     * Returns null if the expected category doesn't exist at all.
     */
    protected function autoDetectCategoryId(string $extension): ?int
    {
        $videoExtensions = ['mp4', 'mov', 'avi', 'webm'];

        $slug = in_array($extension, $videoExtensions, true)
            ? 'videos'
            : 'reading-materials';

        $parent = Category::with(['children' => function ($q) {
            $q->where('is_active', true)->orderBy('order')->orderBy('name');
        }])
            ->where('slug', $slug)
            ->first();

        if (! $parent) {
            return null;
        }

        // If the parent already has sub-categories, use the first active child
        if ($parent->children->isNotEmpty()) {
            return $parent->children->first()->id;
        }

        // Otherwise the parent itself is the leaf — use it directly
        return $parent->id;
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
