<?php

namespace Database\Seeders;

use App\Models\Presentation;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => 'password',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create additional users if they don't exist
        $users = collect([$admin]);
        
        for ($i = 1; $i <= 4; $i++) {
            $user = User::firstOrCreate(
                ['email' => "user{$i}@example.com"],
                [
                    'name' => "User {$i}",
                    'password' => 'password',
                    'role' => 'regular_user',
                    'email_verified_at' => now(),
                ]
            );
            $users->push($user);
        }

        // Create dummy presentations with realistic data
        $presentationTitles = [
            'Introduction to Laravel',
            'Vue.js Best Practices',
            'Database Design Fundamentals',
            'API Development with REST',
            'Modern CSS Techniques',
            'JavaScript ES6+ Features',
            'Docker for Developers',
            'Git Workflows',
            'Testing Strategies',
            'Performance Optimization',
            'Security Best Practices',
            'Microservices Architecture',
            'Cloud Computing Basics',
            'DevOps Essentials',
            'Agile Methodologies',
        ];

        $descriptions = [
            'A comprehensive overview of the Laravel framework and its core concepts.',
            'Learn the best practices for building scalable Vue.js applications.',
            'Understanding database design principles and normalization.',
            'Creating robust and secure RESTful APIs.',
            'Modern techniques for responsive and maintainable CSS.',
            'Exploring the latest JavaScript features and patterns.',
            'Containerization with Docker for development and production.',
            'Effective Git workflows for team collaboration.',
            'Strategies for comprehensive testing coverage.',
            'Techniques to optimize application performance.',
            'Essential security practices for web applications.',
            'Designing and implementing microservices.',
            'Introduction to cloud platforms and services.',
            'DevOps practices and tools for modern development.',
            'Agile methodologies and scrum framework.',
        ];

        foreach ($presentationTitles as $index => $title) {
            // Randomly assign to users with weighted distribution (admin gets more)
            $uploader = $users->random();
            
            Presentation::firstOrCreate(
                ['title' => $title],
                [
                    'description' => $descriptions[$index] ?? 'An informative presentation.',
                    'filename' => str_replace(' ', '_', strtolower($title)) . '.pdf',
                    'file_size' => rand(500000, 5000000), // 500KB to 5MB
                    'file_type' => 'application/pdf',
                    'cloudinary_public_id' => 'presentations/' . str_replace(' ', '_', strtolower($title)),
                    'cloudinary_url' => 'https://res.cloudinary.com/demo/image/upload/sample.pdf',
                    'uploaded_by' => $uploader->id,
                    'views_count' => rand(10, 500),
                    'created_at' => now()->subDays(rand(1, 180)),
                ]
            );
        }

        $this->command->info('Dummy data seeded successfully!');
        $this->command->info('Users created: ' . $users->count());
        $this->command->info('Presentations created: ' . count($presentationTitles));
    }
}
