<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /**
     * Display all categories as a parent → children tree.
     */
    public function index(): Response
    {
        $categories = Category::with(['children' => function ($q) {
            $q->withCount('presentations')->ordered();
        }])
            ->withCount('presentations')
            ->parents()
            ->ordered()
            ->get();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a new category (parent or sub-category).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
            'color'       => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'order'       => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['boolean'],
            'parent_id'   => ['nullable', 'exists:categories,id'],
        ]);

        // Prevent sub-categories from having a parent that is itself a sub-category
        if (! empty($validated['parent_id'])) {
            $parent = Category::findOrFail($validated['parent_id']);
            if ($parent->isChild()) {
                return back()->withErrors(['parent_id' => 'Cannot nest more than 2 levels deep.']);
            }
        }

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string'],
            'color'       => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'order'       => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['boolean'],
            'parent_id'   => ['nullable', 'exists:categories,id'],
        ]);

        // Can't make a category its own parent
        if (! empty($validated['parent_id']) && $validated['parent_id'] == $category->id) {
            return back()->withErrors(['parent_id' => 'A category cannot be its own parent.']);
        }

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Delete a category — blocked if it has children or presentations.
     */
    public function destroy(Category $category)
    {
        $childCount         = $category->children()->count();
        $presentationsCount = $category->presentations()->count();

        if ($childCount > 0) {
            return redirect()->back()
                ->with('error', "Cannot delete category with {$childCount} sub-category(ies). Delete or move them first.");
        }

        if ($presentationsCount > 0) {
            return redirect()->back()
                ->with('error', "Cannot delete category with {$presentationsCount} presentation(s). Move or delete them first.");
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    /**
     * Return the full tree (parents + children) for use in dropdowns.
     * Only sub-categories (leaves) are flagged as selectable.
     */
    public function getActive()
    {
        $parents = Category::with(['children' => function ($q) {
            $q->active()->ordered();
        }])
            ->active()
            ->parents()
            ->ordered()
            ->get()
            ->map(function ($parent) {
                return [
                    'id'         => $parent->id,
                    'name'       => $parent->name,
                    'color'      => $parent->color,
                    'selectable' => $parent->children->isEmpty(), // selectable only if no children
                    'children'   => $parent->children->map(fn ($c) => [
                        'id'         => $c->id,
                        'name'       => $c->name,
                        'color'      => $c->color,
                        'selectable' => true,
                    ])->values(),
                ];
            });

        return response()->json($parents);
    }

    /**
     * Reorder categories (within the same level).
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'categories'        => ['required', 'array'],
            'categories.*.id'   => ['required', 'exists:categories,id'],
            'categories.*.order'=> ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['categories'] as $item) {
            Category::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return back();
    }
}
