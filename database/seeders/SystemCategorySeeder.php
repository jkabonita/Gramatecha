<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * Seeds the two system auto-categorization categories:
 *  - "Videos"             (for mp4/mov/avi/webm uploads)
 *  - "Reading Materials"  (for pdf/ppt/pptx/odp uploads)
 *
 * Uses firstOrCreate so it is safe to run multiple times.
 */
class SystemCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Videos',
                'slug'        => 'videos',
                'description' => 'Video lessons and recorded presentations',
                'color'       => '#7C3AED', // violet-700
                'order'       => 100,
                'is_active'   => true,
            ],
            [
                'name'        => 'Reading Materials',
                'slug'        => 'reading-materials',
                'description' => 'PDF documents, slide decks, and other reading materials',
                'color'       => '#2563EB', // blue-600
                'order'       => 101,
                'is_active'   => true,
            ],
        ];

        foreach ($categories as $data) {
            Category::firstOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
