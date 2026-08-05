<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
        ]);
    }

    /**
     * Upload a presentation file to Cloudinary.
     *
     * @param  UploadedFile  $file
     * @return array{public_id: string, secure_url: string, bytes: int}
     *
     * @throws \Exception
     */
    public function uploadPresentation(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $resourceType = $this->getResourceType($extension);

        $uploadOptions = [
            'resource_type' => $resourceType,
            'folder' => 'gramatecha/presentations',
            'flags' => 'attachment', // This will be overridden by fl_attachment transformation for raw files
        ];

        // For raw files (PDFs, PPTX), ensure inline viewing
        if ($resourceType === 'raw') {
            $uploadOptions['flags'] = 'attachment:false';
        }

        $uploadResult = $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            $uploadOptions
        );

        return [
            'public_id' => $uploadResult['public_id'],
            'secure_url' => $uploadResult['secure_url'],
            'bytes' => $uploadResult['bytes'],
        ];
    }

    /**
     * Delete a presentation from Cloudinary.
     *
     * @param  string  $publicId
     * @return bool
     */
    public function deletePresentation(string $publicId): bool
    {
        try {
            // Determine resource type from public_id or try both
            $resourceType = $this->getResourceTypeFromPublicId($publicId);
            
            $result = $this->cloudinary->uploadApi()->destroy(
                $publicId,
                ['resource_type' => $resourceType]
            );

            return isset($result['result']) && $result['result'] === 'ok';
        } catch (\Exception $e) {
            // Try alternative resource type if first attempt fails
            try {
                $alternativeType = $resourceType === 'video' ? 'raw' : 'video';
                $result = $this->cloudinary->uploadApi()->destroy(
                    $publicId,
                    ['resource_type' => $alternativeType]
                );
                
                return isset($result['result']) && $result['result'] === 'ok';
            } catch (\Exception $e) {
                return false;
            }
        }
    }

    /**
     * Get the Cloudinary URL for a presentation.
     *
     * @param  string  $publicId
     * @return string
     */
    public function getPresentationUrl(string $publicId): string
    {
        $cloudName = config('services.cloudinary.cloud_name');
        $resourceType = $this->getResourceTypeFromPublicId($publicId);
        
        return "https://res.cloudinary.com/{$cloudName}/{$resourceType}/upload/{$publicId}";
    }

    /**
     * Determine the resource type based on file extension.
     *
     * @param  string  $extension
     * @return string
     */
    protected function getResourceType(string $extension): string
    {
        return in_array($extension, ['mp4', 'mov']) ? 'video' : 'raw';
    }

    /**
     * Determine the resource type from a public_id (uses stored file type if needed).
     *
     * @param  string  $publicId
     * @return string
     */
    protected function getResourceTypeFromPublicId(string $publicId): string
    {
        // Try to infer from public_id or default to 'raw'
        // In practice, we might need to store this with the model
        return 'raw'; // Default for PPTX files
    }
}
