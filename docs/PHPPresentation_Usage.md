# PHPPresentation Usage Guide

## Overview

This application now uses **PHPPresentation** library instead of Cloudinary for generating and managing presentation files. PHPPresentation is a pure PHP library that allows you to create PowerPoint and OpenDocument presentations programmatically.

## Features

- ✅ **No Cloudinary API** - All files are stored locally
- ✅ **Generate PPTX files** - Create PowerPoint 2007 presentations
- ✅ **Generate ODP files** - Create OpenDocument presentations
- ✅ **Upload existing files** - Upload PDF, PPT, PPTX, MP4, and other presentation files
- ✅ **Programmatic generation** - Generate presentations from data using PHP

## Installation

PHPPresentation is already installed via Composer:

```bash
composer require phpoffice/phppresentation
```

## Configuration

### Environment Variables

Add to your `.env` file:

```env
# PHPPresentation Configuration
PRESENTATION_STORAGE_PATH=presentations
```

### Storage Setup

Make sure the storage link is created:

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`.

## Database Migration

The database has been updated to remove Cloudinary fields and add local storage fields:

```php
Schema::create('presentations', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->string('filename');
    $table->unsignedBigInteger('file_size');
    $table->string('file_type', 10); // pptx, odp, pdf, etc.
    $table->string('file_path'); // storage path
    $table->text('file_url'); // public URL
    $table->unsignedInteger('views_count')->default(0);
    $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
    $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
    $table->timestamps();
});
```

Run migration:

```bash
php artisan migrate:fresh --seed
```

## Usage Examples

### 1. Upload Existing Presentation

Users can upload existing presentation files through the web interface at `/admin/presentations/create`.

Supported formats:
- PDF (`.pdf`)
- PowerPoint (`.ppt`, `.pptx`)
- Video (`.mp4`, `.mov`)

### 2. Generate Presentation Programmatically

Use the `PresentationService` to generate presentations:

```php
use App\Services\PresentationService;
use Illuminate\Support\Facades\Auth;

$presentationService = app(PresentationService::class);

$slides = [
    [
        'title' => 'Welcome to Gramatecha',
        'content' => 'Your platform for educational presentations'
    ],
    [
        'title' => 'Features',
        'content' => 'Easy upload, categorization, and sharing of presentations'
    ],
    [
        'title' => 'Get Started',
        'content' => 'Upload your first presentation today!'
    ],
];

$presentation = $presentationService->generatePresentation(
    title: 'Welcome Presentation',
    description: 'An introduction to our platform',
    slides: $slides,
    uploader: Auth::user(),
    categoryId: 1, // optional
    format: 'PowerPoint2007' // or 'ODPresentation'
);
```

### 3. API Endpoint for Generation

POST to `/admin/presentations/generate` with the following JSON:

```json
{
    "title": "My Generated Presentation",
    "description": "A presentation generated from data",
    "category_id": 1,
    "format": "PowerPoint2007",
    "slides": [
        {
            "title": "Slide 1 Title",
            "content": "Slide 1 content goes here"
        },
        {
            "title": "Slide 2 Title",
            "content": "Slide 2 content goes here"
        }
    ]
}
```

### 4. Advanced Customization

For advanced customization, you can modify `PHPPresentationGenerator` service:

```php
use App\Services\PHPPresentationGenerator;

$generator = app(PHPPresentationGenerator::class);

// Create custom presentation
$presentation = $generator->generatePresentation(
    title: 'Custom Presentation',
    description: 'With custom styling',
    slides: $slides
);

// Save to custom location
$generator->savePresentation(
    presentation: $presentation,
    filePath: storage_path('app/public/custom/presentation.pptx'),
    format: 'PowerPoint2007'
);
```

## File Storage

All presentation files are stored in:
- **Storage Path**: `storage/app/public/presentations/`
- **Public URL**: `http://yourdomain.com/storage/presentations/filename.pptx`

## Services Architecture

### PHPPresentationGenerator

Handles the generation of presentations using PHPPresentation library.

**Methods:**
- `generatePresentation()` - Generate a presentation from data
- `savePresentation()` - Save presentation to file
- `getFileExtension()` - Get file extension for format

### FileStorageService

Handles file storage operations.

**Methods:**
- `storePresentation()` - Store uploaded file
- `storeGeneratedPresentation()` - Store generated file
- `deletePresentation()` - Delete file from storage
- `getPresentationUrl()` - Get public URL for file

### PresentationService

Main service for presentation management.

**Methods:**
- `createPresentation()` - Create from uploaded file
- `generatePresentation()` - Generate programmatically
- `updatePresentationMetadata()` - Update title/description
- `deletePresentation()` - Delete presentation

## Supported Formats

### Generation
- **PowerPoint2007** - Modern `.pptx` format (recommended)
- **ODPresentation** - OpenDocument `.odp` format

### Upload
- PDF (`.pdf`)
- PowerPoint (`.ppt`, `.pptx`)
- OpenDocument (`.odp`)
- Video (`.mp4`, `.mov`)

## Error Handling

All services include comprehensive logging:

```php
\Log::info('Starting presentation generation...');
\Log::error('Failed to generate: ' . $e->getMessage());
```

Check logs at `storage/logs/laravel.log` for debugging.

## Security Considerations

1. **File Validation** - All uploads are validated for type and size
2. **Filename Sanitization** - Filenames are sanitized to prevent path traversal
3. **Authentication Required** - Only authenticated users can upload/generate
4. **Storage Permissions** - Files are stored with proper permissions

## Testing

Create a test to verify generation:

```php
use App\Services\PresentationService;
use Tests\TestCase;

class PresentationGenerationTest extends TestCase
{
    public function test_can_generate_presentation()
    {
        $user = User::factory()->create();
        $service = app(PresentationService::class);
        
        $slides = [
            ['title' => 'Test', 'content' => 'Content']
        ];
        
        $presentation = $service->generatePresentation(
            title: 'Test Presentation',
            description: null,
            slides: $slides,
            uploader: $user
        );
        
        $this->assertFileExists(
            storage_path('app/public/' . $presentation->file_path)
        );
    }
}
```

## Migration from Cloudinary

If you were previously using Cloudinary, follow these steps:

1. **Update Environment** - Remove Cloudinary credentials
2. **Run Migration** - Update database schema
3. **Move Files** - Download files from Cloudinary to local storage
4. **Update Code** - Replace Cloudinary API calls with local storage

## Additional Resources

- [PHPPresentation Documentation](https://phppresentation.readthedocs.io/)
- [PHPPresentation GitHub](https://github.com/PHPOffice/PHPPresentation)
- [Laravel File Storage](https://laravel.com/docs/filesystem)

## Support

For issues or questions, check the logs or refer to the PHPPresentation documentation.
