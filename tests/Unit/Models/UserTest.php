<?php

use App\Models\User;

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

    it('has presentations relationship method defined', function () {
        $user = new User();
        
        expect(method_exists($user, 'presentations'))->toBeTrue();
    });
});

