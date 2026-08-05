<?php

use App\Models\Presentation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

describe('Presentation Model', function () {
    it('has correct fillable fields', function () {
        $fillable = (new Presentation())->getFillable();
        
        expect($fillable)->toContain('title')
            ->and($fillable)->toContain('description')
            ->and($fillable)->toContain('filename')
            ->and($fillable)->toContain('file_size')
            ->and($fillable)->toContain('file_type')
            ->and($fillable)->toContain('cloudinary_public_id')
            ->and($fillable)->toContain('cloudinary_url')
            ->and($fillable)->toContain('uploaded_by');
    });

    it('casts file_size to integer', function () {
        $presentation = Presentation::factory()->make(['file_size' => '1024']);
        
        expect($presentation->file_size)->toBeInt();
    });

    it('casts timestamps to datetime', function () {
        $presentation = Presentation::factory()->create();
        
        expect($presentation->created_at)->toBeInstanceOf(\DateTimeInterface::class)
            ->and($presentation->updated_at)->toBeInstanceOf(\DateTimeInterface::class);
    });

    it('has uploader relationship', function () {
        $user = User::factory()->create();
        $presentation = Presentation::factory()->create(['uploaded_by' => $user->id]);
        
        expect($presentation->uploader)->toBeInstanceOf(User::class)
            ->and($presentation->uploader->id)->toBe($user->id);
    });

    it('formats file size for bytes', function () {
        $presentation = Presentation::factory()->make(['file_size' => 500]);
        
        expect($presentation->getFormattedFileSize())->toBe('500 B');
    });

    it('formats file size for kilobytes', function () {
        $presentation = Presentation::factory()->make(['file_size' => 2048]);
        
        expect($presentation->getFormattedFileSize())->toBe('2 KB');
    });

    it('formats file size for megabytes', function () {
        $presentation = Presentation::factory()->make(['file_size' => 2621440]); // 2.5 MB
        
        expect($presentation->getFormattedFileSize())->toBe('2.5 MB');
    });

    it('formats file size for gigabytes', function () {
        $presentation = Presentation::factory()->make(['file_size' => 1073741824]); // 1 GB
        
        expect($presentation->getFormattedFileSize())->toBe('1 GB');
    });

    it('generates cloudinary view url with optimizations', function () {
        $presentation = Presentation::factory()->make([
            'cloudinary_public_id' => 'presentations/test-presentation',
        ]);
        
        $url = $presentation->getCloudinaryViewUrl();
        $cloudName = config('services.cloudinary.cloud_name', 'io7qeps0');
        
        expect($url)->toBe("https://res.cloudinary.com/{$cloudName}/image/upload/f_auto,q_auto/presentations/test-presentation");
    });

    it('supports pptx file type', function () {
        $presentation = Presentation::factory()->create(['file_type' => 'pptx']);
        
        expect($presentation->file_type)->toBe('pptx');
    });

    it('supports mp4 video file type', function () {
        $presentation = Presentation::factory()->create(['file_type' => 'mp4']);
        
        expect($presentation->file_type)->toBe('mp4');
    });

    it('supports mov video file type', function () {
        $presentation = Presentation::factory()->create(['file_type' => 'mov']);
        
        expect($presentation->file_type)->toBe('mov');
    });
});
