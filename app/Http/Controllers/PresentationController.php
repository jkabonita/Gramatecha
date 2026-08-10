<?php

namespace App\Http\Controllers;

use App\Http\Requests\PresentationStoreRequest;
use App\Http\Requests\PresentationUpdateRequest;
use App\Models\Presentation;
use App\Services\PresentationService;
use App\Services\PptxReaderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PresentationController extends Controller
{
    public function __construct(
        protected PresentationService $presentationService,
        protected PptxReaderService $pptxReaderService
    ) {}

    /**
     * Display a paginated list of presentations — PDFs / slides only (PUBLIC).
     */
    public function index(): Response
    {
        $videoTypes = ['mp4', 'mov', 'avi', 'webm'];

        $presentations = Presentation::with(['uploader', 'category'])
            ->whereNotIn('file_type', $videoTypes)
            ->orderBy('title', 'asc')
            ->paginate(24);

        $categories = \App\Models\Category::with(['children' => function ($q) {
            $q->active()->ordered();
        }])->active()->parents()->ordered()->withCount('presentations')->get();

        return Inertia::render('Presentations/Index', [
            'presentations' => $presentations,
            'categories'    => $categories,
        ]);
    }

    /**
     * Display a paginated list of video presentations only (PUBLIC).
     */
    public function videoIndex(): Response
    {
        $videoTypes = ['mp4', 'mov', 'avi', 'webm'];

        $presentations = Presentation::with(['uploader', 'category'])
            ->whereIn('file_type', $videoTypes)
            ->orderBy('title', 'asc')
            ->paginate(24);

        $categories = \App\Models\Category::with(['children' => function ($q) {
            $q->active()->ordered();
        }])->active()->parents()->ordered()->withCount('presentations')->get();

        return Inertia::render('Videos/Index', [
            'presentations' => $presentations,
            'categories'    => $categories,
        ]);
    }


    /**
     * Display a paginated list of presentations for admin management.
     */
    public function adminIndex(): Response
    {
        $presentations = Presentation::with(['uploader', 'category'])
            ->latest()
            ->paginate(20);

        $categories = \App\Models\Category::with(['children' => function ($q) {
            $q->active()->ordered();
        }])->active()->parents()->ordered()->get();

        return Inertia::render('Admin/Presentations/Index', [
            'presentations' => $presentations,
            'categories' => $categories,
        ]);
    }

    /**
     * Return JSON slide data for client-side rendering (PPTX / ODP).
     * Uses PHP's built-in ZipArchive + SimpleXML — no LibreOffice required.
     */
    public function slides(Presentation $presentation): JsonResponse
    {
        $ext = strtolower($presentation->file_type);

        if (! in_array($ext, ['pptx', 'ppt', 'pptm', 'ppsx', 'odp'], true)) {
            return response()->json([
                'error' => 'Slide extraction is only supported for PPTX / ODP files.',
            ], 422);
        }

        try {
            $slides = $this->pptxReaderService->extractSlides($presentation->file_path);

            return response()->json([
                'title'  => $presentation->title,
                'slides' => $slides,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error'   => 'Could not extract slides from this file.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a single presentation (PUBLIC).
     */
    public function show(Presentation $presentation): Response
    {
        $presentation->load(['uploader', 'category']);
        
        // Increment views count
        $presentation->increment('views_count');
        
        // Add inline view URL
        $presentation->inline_view_url = $presentation->getInlineViewUrl();

        return Inertia::render('Presentations/Show', [
            'presentation' => $presentation,
        ]);
    }

    /**
     * Display a single presentation (ADMIN).
     */
    public function adminShow(Presentation $presentation): Response
    {
        $presentation->load(['uploader', 'category']);
        
        // Add inline view URL
        $presentation->inline_view_url = $presentation->getInlineViewUrl();

        return Inertia::render('Admin/Presentations/Show', [
            'presentation' => $presentation,
        ]);
    }

    /**
     * Show the form for creating a new presentation (ADMIN).
     */
    public function create(): Response
    {
        $categories = \App\Models\Category::with(['children' => function ($q) {
            $q->active()->ordered();
        }])->active()->parents()->ordered()->get();
        
        return Inertia::render('Admin/Presentations/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for bulk uploading presentations (ADMIN).
     */
    public function bulkCreate(): Response
    {
        $categories = \App\Models\Category::with(['children' => function ($q) {
            $q->active()->ordered();
        }])->active()->parents()->ordered()->get();
        
        return Inertia::render('Admin/Presentations/BulkCreate', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created presentation (ADMIN).
     */
    public function store(PresentationStoreRequest $request): RedirectResponse
    {
        try {
            $presentation = $this->presentationService->createPresentation(
                file: $request->file('file'),
                title: $request->input('title'),
                description: $request->input('description'),
                uploader: Auth::user(),
                categoryId: $request->input('category_id')
            );

            return redirect()
                ->route('admin.presentations.index')
                ->with('success', 'Presentation uploaded successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['file' => 'Failed to upload presentation: ' . $e->getMessage()])
                ->with('error', 'Failed to upload presentation: ' . $e->getMessage());
        }
    }

    /**
     * Store multiple presentations at once (ADMIN).
     */
    public function bulkStore(\Illuminate\Http\Request $request): RedirectResponse
    {
        $request->validate([
            'files' => 'required|array|min:1|max:20',
            'files.*' => 'required|file|mimes:pdf,ppt,pptx,mp4,mov|max:512000',
            'category_id' => 'nullable|exists:categories,id',
        ], [
            'files.required' => 'Please select at least one file to upload.',
            'files.min' => 'Please select at least one file to upload.',
            'files.max' => 'You can upload a maximum of 20 files at once.',
            'files.*.required' => 'Each file is required.',
            'files.*.file' => 'Each upload must be a valid file.',
            'files.*.mimes' => 'Only PDF, PPT, PPTX, MP4, and MOV files are allowed.',
            'files.*.max' => 'Each file must not exceed 500MB.',
            'category_id.exists' => 'The selected category is invalid.',
        ]);

        $uploaded = 0;
        $failed = 0;
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                $title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                
                $this->presentationService->createPresentation(
                    file: $file,
                    title: $title,
                    description: null,
                    uploader: Auth::user(),
                    categoryId: $request->input('category_id')
                );

                $uploaded++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = $file->getClientOriginalName();
            }
        }

        $categoryMessage = $request->input('category_id') ? '' : ' (category auto-assigned by file type)';

        if ($failed === 0) {
            return redirect()
                ->route('admin.presentations.index')
                ->with('success', "{$uploaded} presentation(s) uploaded successfully{$categoryMessage}");
        } elseif ($uploaded === 0) {
            return redirect()
                ->back()
                ->withErrors(['files' => 'All uploads failed.'])
                ->with('error', 'Failed to upload all presentations.');
        } else {
            return redirect()
                ->route('admin.presentations.index')
                ->with('warning', "{$uploaded} uploaded{$categoryMessage}, {$failed} failed: " . implode(', ', $errors));
        }
    }

    /**
     * Show the form for editing a presentation (ADMIN).
     */
    public function edit(Presentation $presentation): Response
    {
        $presentation->load('category');
        $categories = \App\Models\Category::with(['children' => function ($q) {
            $q->active()->ordered();
        }])->active()->parents()->ordered()->get();
        
        return Inertia::render('Admin/Presentations/Edit', [
            'presentation' => $presentation,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified presentation metadata (ADMIN).
     */
    public function update(
        PresentationUpdateRequest $request,
        Presentation $presentation
    ): RedirectResponse {
        try {
            $this->presentationService->updatePresentationMetadata(
                presentation: $presentation,
                title: $request->input('title'),
                description: $request->input('description'),
                categoryId: $request->input('category_id')
            );

            return redirect()
                ->route('admin.presentations.index')
                ->with('success', 'Presentation updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update presentation: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified presentation (ADMIN).
     */
    public function destroy(Presentation $presentation): RedirectResponse
    {
        try {
            $this->presentationService->deletePresentation($presentation);

            return redirect()
                ->route('admin.presentations.index')
                ->with('success', 'Presentation deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete presentation: ' . $e->getMessage());
        }
    }

    /**
     * Stream a file with proper byte-range support for video seeking.
     * Also handles PDF inline viewing.
     * OPTIMIZED: Skip middleware overhead for better performance
     */
    public function stream(Presentation $presentation): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $path     = $presentation->file_path;
        $disk     = Storage::disk('public');

        abort_unless($disk->exists($path), 404, 'File not found.');

        $fullPath  = $disk->path($path);
        $mimeType  = $this->resolveMimeType($presentation->file_type);

        // Use BinaryFileResponse for optimal performance
        // This handles byte-range requests automatically and efficiently
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $presentation->filename . '"',
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Force-download a presentation file.
     */
    public function download(Presentation $presentation): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $path     = $presentation->file_path;
        $disk     = Storage::disk('public');

        abort_unless($disk->exists($path), 404, 'File not found.');

        $fullPath = $disk->path($path);
        $fileSize = $disk->size($path);
        $mimeType = $this->resolveMimeType($presentation->file_type);

        return response()->stream(
            function () use ($fullPath) {
                $handle = fopen($fullPath, 'rb');
                while (! feof($handle)) {
                    echo fread($handle, 8192);
                    flush();
                }
                fclose($handle);
            },
            200,
            [
                'Content-Type'        => $mimeType,
                'Content-Length'      => $fileSize,
                'Content-Disposition' => 'attachment; filename="' . $presentation->filename . '"',
                'Cache-Control'       => 'no-cache',
            ]
        );
    }

    /**
     * Resolve the MIME type for a given file extension.
     */
    private function resolveMimeType(string $extension): string
    {
        return match (strtolower($extension)) {
            'pdf'  => 'application/pdf',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
            'odp'  => 'application/vnd.oasis.opendocument.presentation',
            'mp4'  => 'video/mp4',
            'webm' => 'video/webm',
            'mov'  => 'video/quicktime',
            'avi'  => 'video/x-msvideo',
            default => 'application/octet-stream',
        };
    }

    /**
     * Generate a presentation programmatically (ADMIN).
     */
    public function generate(\Illuminate\Http\Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'format' => 'required|in:PowerPoint2007,ODPresentation',
            'slides' => 'required|array|min:1',
            'slides.*.title' => 'required|string|max:255',
            'slides.*.content' => 'required|string',
        ]);

        try {
            $presentation = $this->presentationService->generatePresentation(
                title: $request->input('title'),
                description: $request->input('description'),
                slides: $request->input('slides'),
                uploader: Auth::user(),
                categoryId: $request->input('category_id'),
                format: $request->input('format')
            );

            return redirect()
                ->route('presentations.show', $presentation)
                ->with('success', 'Presentation generated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to generate presentation: ' . $e->getMessage());
        }
    }

    /**
     * Bulk-delete multiple presentations (ADMIN).
     */
    public function bulkDestroy(\Illuminate\Http\Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:presentations,id'],
        ]);

        $presentations = Presentation::whereIn('id', $validated['ids'])->get();

        foreach ($presentations as $presentation) {
            // Remove physical file from storage
            if ($presentation->file_path && Storage::exists($presentation->file_path)) {
                Storage::delete($presentation->file_path);
            }
            $presentation->delete();
        }

        $count = count($validated['ids']);

        return redirect()
            ->route('admin.presentations.index')
            ->with('success', "{$count} presentation(s) deleted successfully.");
    }
}
