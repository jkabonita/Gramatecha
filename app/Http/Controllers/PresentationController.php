<?php

namespace App\Http\Controllers;

use App\Http\Requests\PresentationStoreRequest;
use App\Http\Requests\PresentationUpdateRequest;
use App\Models\Presentation;
use App\Services\PresentationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PresentationController extends Controller
{
    public function __construct(
        protected PresentationService $presentationService
    ) {}

    /**
     * Display a paginated list of presentations (PUBLIC).
     */
    public function index(): Response
    {
        $presentations = Presentation::with('uploader')
            ->latest()
            ->paginate(20);

        return Inertia::render('Presentations/Index', [
            'presentations' => $presentations,
        ]);
    }

    /**
     * Display a single presentation (PUBLIC).
     */
    public function show(Presentation $presentation): Response
    {
        $presentation->load('uploader');
        
        // Increment views count
        $presentation->increment('views_count');
        
        // Add inline view URL
        $presentation->inline_view_url = $presentation->getInlineViewUrl();

        return Inertia::render('Presentations/Show', [
            'presentation' => $presentation,
        ]);
    }

    /**
     * Show the form for creating a new presentation (ADMIN).
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Presentations/Create');
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
                uploader: Auth::user()
            );

            return redirect()
                ->route('presentations.show', $presentation)
                ->with('success', 'Presentation uploaded successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to upload presentation: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing a presentation (ADMIN).
     */
    public function edit(Presentation $presentation): Response
    {
        return Inertia::render('Admin/Presentations/Edit', [
            'presentation' => $presentation,
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
                description: $request->input('description')
            );

            return redirect()
                ->route('presentations.show', $presentation)
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
                ->route('presentations.index')
                ->with('success', 'Presentation deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete presentation: ' . $e->getMessage());
        }
    }
}
