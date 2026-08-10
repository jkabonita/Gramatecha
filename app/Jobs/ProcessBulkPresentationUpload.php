<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\PresentationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessBulkPresentationUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $tempFilePath,
        public string $originalFilename,
        public int $userId,
        public int $categoryId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(PresentationService $presentationService): void
    {
        try {
            // Create UploadedFile instance from temporary file
            $file = new UploadedFile(
                $this->tempFilePath,
                $this->originalFilename,
                mime_content_type($this->tempFilePath),
                null,
                true // test mode - allows setting error to null
            );

            $user = User::findOrFail($this->userId);
            $title = pathinfo($this->originalFilename, PATHINFO_FILENAME);

            // Create the presentation
            $presentationService->createPresentation(
                file: $file,
                title: $title,
                description: null,
                uploader: $user,
                categoryId: $this->categoryId
            );

            // Clean up temporary file
            @unlink($this->tempFilePath);

            Log::info("✅ Processed bulk upload: {$this->originalFilename}");
        } catch (\Exception $e) {
            Log::error("❌ Failed to process bulk upload: {$this->originalFilename}", [
                'error' => $e->getMessage(),
            ]);

            // Clean up temporary file on error
            @unlink($this->tempFilePath);

            throw $e;
        }
    }
}
