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
 * @property string $cloudinary_public_id
 * @property string $cloudinary_url
 * @property int $uploaded_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $uploader
 */
#[Fillable([
    'title',
    'description',
    'filename',
    'file_size',
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
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
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
        return "https://res.cloudinary.com/{$cloudName}/image/upload/f_auto,q_auto/{$this->cloudinary_public_id}";
    }
}
