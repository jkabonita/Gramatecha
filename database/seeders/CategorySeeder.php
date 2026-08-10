<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Grammar & Gramatika',
                'slug' => 'grammar-gramatika',
                'description' => 'Filipino grammar rules, sentence structure, and grammatical concepts',
                'color' => '#3b82f6', // blue
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Written Communication',
                'slug' => 'written-communication',
                'description' => 'Pasulat - Essays, letters, academic writing, and formal documents',
                'color' => '#10b981', // green
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Oral Communication',
                'slug' => 'oral-communication',
                'description' => 'Pasalita - Speaking skills, presentations, and verbal communication',
                'color' => '#8b5cf6', // purple
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Reading Comprehension',
                'slug' => 'reading-comprehension',
                'description' => 'Pagbasa - Reading strategies, comprehension, and analysis',
                'color' => '#f59e0b', // amber
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Vocabulary & Talasalitaan',
                'slug' => 'vocabulary-talasalitaan',
                'description' => 'Word meanings, synonyms, antonyms, and Filipino vocabulary building',
                'color' => '#ec4899', // pink
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'KWF Standards',
                'slug' => 'kwf-standards',
                'description' => 'Official guidelines and standards from Komisyon sa Wikang Filipino',
                'color' => '#06b6d4', // cyan
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Literature & Panitikan',
                'slug' => 'literature-panitikan',
                'description' => 'Filipino literary works, poetry, and literary analysis',
                'color' => '#14b8a6', // teal
                'order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Business Communication',
                'slug' => 'business-communication',
                'description' => 'Professional communication in Filipino for business contexts',
                'color' => '#64748b', // slate
                'order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
