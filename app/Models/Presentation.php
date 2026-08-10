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
 * @property string $file_path
 * @property string $file_url
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
    'file_path',
    'file_url',
    'uploaded_by',
    'category_id',
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
     * Get the category this presentation belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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
     * Get the inline viewing URL for the presentation.
     * PDFs are served via the /stream route (works on localhost).
     * Other types return the direct public URL.
     */
    public function getInlineViewUrl(): string
    {
        if (strtolower($this->file_type) === 'pdf') {
            return route('presentations.stream', $this->id);
        }

        return $this->file_url;
    }
}
