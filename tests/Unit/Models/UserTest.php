<?php

use App\Models\User;
use App\Models\Presentation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('User Model', function () {
    it('has role in fillable fields', function () {
        $user = new User();
        $fillable = $user->getFillable();
        
        expect($fillable)->toContain('role');
    });

    it('identifies admin users correctly', function () {
        $admin = new User(['role' => 'admin']);
        $regularUser = new User(['role' => 'regular_user']);
        
        expect($admin->isAdmin())->toBeTrue()
            ->and($regularUser->isAdmin())->toBeFalse();
    });

    it('identifies regular users correctly', function () {
        $admin = new User(['role' => 'admin']);
        $regularUser = new User(['role' => 'regular_user']);
        
        expect($admin->isRegularUser())->toBeFalse()
            ->and($regularUser->isRegularUser())->toBeTrue();
    });

    it('has presentations relationship defined', function () {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        
        expect($user->presentations())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    });

    it('can retrieve presentations uploaded by user', function () {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        
        $otherUser = User::create([
            'name' => 'Other Admin',
            'email' => 'other@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        
        // Create presentations for this user
        for ($i = 0; $i < 3; $i++) {
            Presentation::create([
                'title' => "Presentation $i",
                'description' => 'Test description',
                'filename' => "test$i.pptx",
                'file_size' => 1000000,
                'cloudinary_public_id' => "presentations/test$i",
                'cloudinary_url' => "https://cloudinary.test/test$i.pptx",
                'uploaded_by' => $user->id,
            ]);
        }
        
        // Create presentations for another user
        for ($i = 0; $i < 2; $i++) {
            Presentation::create([
                'title' => "Other Presentation $i",
                'description' => 'Test description',
                'filename' => "other$i.pptx",
                'file_size' => 1000000,
                'cloudinary_public_id' => "presentations/other$i",
                'cloudinary_url' => "https://cloudinary.test/other$i.pptx",
                'uploaded_by' => $otherUser->id,
            ]);
        }
        
        expect($user->presentations()->count())->toBe(3)
            ->and($otherUser->presentations()->count())->toBe(2);
    });
});

