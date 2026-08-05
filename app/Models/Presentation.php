<?php

namespace App\Models;

use Database\Factories\PresentationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $filename
 * @property int $file_size
 * @property string $file_type
 * @property string $cloudinary_public_id
 * @property string $cloudinary_url
 * @property int $uploaded_by
 * @property int $views_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $uploader
 */
#[Fillable([
    'title',
    'description',
    'filename',
    'file_size',
    'file_type',
    'cloudinary_public_id',
    'cloudinary_url',
    'uploaded_by',
])]
class Presentation extends Model
{
    /** @use HasFactory<PresentationFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user who uploaded this presentation.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the formatted file size (e.g., "2.5 MB").
     */
    public function getFormattedFileSize(): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $this->file_size;
        
        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the Cloudinary viewing URL with optimizations.
     */
    public function getCloudinaryViewUrl(): string
    {
        $cloudName = config('services.cloudinary.cloud_name', 'io7qeps0');
        
        // Determine resource type based on file extension
        $isPdf = str_contains(strtolower($this->filename), '.pdf');
        $isVideo = in_array(strtolower($this->file_type), ['mp4', 'mov', 'avi', 'video']);
        
        if ($isPdf) {
            // For PDFs, use raw resource type with inline flag
            return "https://res.cloudinary.com/{$cloudName}/raw/upload/fl_attachment:false/{$this->cloudinary_public_id}";
        } elseif ($isVideo) {
            // For videos, use video resource type
            return "https://res.cloudinary.com/{$cloudName}/video/upload/{$this->cloudinary_public_id}";
        } else {
            // For other files (PPTX, etc), use raw resource type with inline flag
            return "https://res.cloudinary.com/{$cloudName}/raw/upload/fl_attachment:false/{$this->cloudinary_public_id}";
        }
    }
    
    /**
     * Get the Cloudinary URL for inline viewing (forces browser to display, not download).
     */
    public function getInlineViewUrl(): string
    {
        return $this->getCloudinaryViewUrl();
    }
}
