<?php

use App\Models\User;
use App\Models\Presentation;

describe('User Relationships', function () {
    it('has presentations relationship returning HasMany', function () {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
        
        expect($user->presentations())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    });

    it('can retrieve presentations uploaded by user', function () {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
        
        $otherUser = User::create([
            'name' => 'Other Admin',
            'email' => 'other@test.com',
            'password' => 'password',
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
    
    it('correctly filters presentations by uploaded_by foreign key', function () {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
        
        $otherAdmin = User::create([
            'name' => 'Other Admin',
            'email' => 'other@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
        
        $presentation1 = Presentation::create([
            'title' => 'Admin Presentation',
            'description' => 'By admin',
            'filename' => 'admin.pptx',
            'file_size' => 500000,
            'cloudinary_public_id' => 'presentations/admin1',
            'cloudinary_url' => 'https://cloudinary.test/admin1.pptx',
            'uploaded_by' => $admin->id,
        ]);
        
        $presentation2 = Presentation::create([
            'title' => 'Other Admin Presentation',
            'description' => 'By other admin',
            'filename' => 'other.pptx',
            'file_size' => 600000,
            'cloudinary_public_id' => 'presentations/other1',
            'cloudinary_url' => 'https://cloudinary.test/other1.pptx',
            'uploaded_by' => $otherAdmin->id,
        ]);
        
        $adminPresentations = $admin->presentations()->get();
        $otherAdminPresentations = $otherAdmin->presentations()->get();
        
        expect($adminPresentations)->toHaveCount(1)
            ->and($adminPresentations->first()->id)->toBe($presentation1->id)
            ->and($otherAdminPresentations)->toHaveCount(1)
            ->and($otherAdminPresentations->first()->id)->toBe($presentation2->id);
    });
});
