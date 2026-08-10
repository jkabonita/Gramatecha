<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'color',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // ── Relationships ────────────────────────────────────────────────────────

    /**
     * The parent category (null for top-level categories).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Direct sub-categories of this category.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order')->orderBy('name');
    }

    /**
     * All presentations directly assigned to this category.
     */
    public function presentations(): HasMany
    {
        return $this->hasMany(Presentation::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Only top-level (parent) categories — those without a parent_id.
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Only sub-categories — those that have a parent_id.
     */
    public function scopeChildren($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Order by the `order` column then alphabetically.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Returns true if this category is a top-level parent.
     */
    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Returns true if this category is a sub-category.
     */
    public function isChild(): bool
    {
        return ! is_null($this->parent_id);
    }
}
