<?php

namespace App\Http\Controllers;

use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProjectCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProjectCategory::query()->orderBy('order', 'asc')->orderBy('id', 'asc');

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->get();

        if ($request->wantsJson()) {
            return response()->json($categories);
        }

        return Inertia::render('project-categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:project_categories,name',
            'slug' => 'nullable|string|max:255|unique:project_categories,slug|regex:/^[a-z0-9\-]+$/',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:65535',
            'order' => 'required|integer|min:0',
        ]);

        $slug = $request->filled('slug')
            ? Str::slug($request->input('slug'))
            : Str::slug($request->input('name'));

        // Double check uniqueness of generated slug
        $originalSlug = $slug;
        $count = 1;
        while (ProjectCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        ProjectCategory::create([
            'name' => $request->input('name'),
            'slug' => $slug,
            'description' => $request->input('description'),
            'icon' => $request->input('icon'),
            'order' => $request->input('order'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Category created successfully.'),
        ]);

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectCategory $projectCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:project_categories,name,'.$projectCategory->id,
            'slug' => 'required|string|max:255|unique:project_categories,slug,'.$projectCategory->id.'|regex:/^[a-z0-9\-]+$/',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:65535',
            'order' => 'required|integer|min:0',
        ]);

        $slug = Str::slug($request->input('slug'));

        $projectCategory->update([
            'name' => $request->input('name'),
            'slug' => $slug,
            'description' => $request->input('description'),
            'icon' => $request->input('icon'),
            'order' => $request->input('order'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Category updated successfully.'),
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectCategory $projectCategory)
    {
        // Check for referential integrity
        $count = 0;
        if (Schema::hasTable('projects')) {
            $count = DB::table('projects')->where('category_id', $projectCategory->id)->count();
        }

        if ($count > 0) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => __('Kategori ini tidak dapat dihapus karena masih digunakan oleh :count proyek. Pindahkan proyek-proyek tersebut ke kategori lain terlebih dahulu.', ['count' => $count]),
            ]);

            return redirect()->back();
        }

        $projectCategory->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Category deleted successfully.'),
        ]);

        return redirect()->back();
    }

    /**
     * Swap order with the adjacent category.
     */
    public function move(Request $request, ProjectCategory $projectCategory)
    {
        $request->validate([
            'direction' => 'required|in:up,down',
        ]);

        $direction = $request->input('direction');
        $currentOrder = $projectCategory->order;

        if ($direction === 'up') {
            // Find the category just above it
            $sibling = ProjectCategory::where('order', '<', $currentOrder)
                ->orderBy('order', 'desc')
                ->first();

            if (! $sibling) {
                // If same order, swap by id
                $sibling = ProjectCategory::where('order', '=', $currentOrder)
                    ->where('id', '<', $projectCategory->id)
                    ->orderBy('id', 'desc')
                    ->first();
            }
        } else {
            // Find the category just below it
            $sibling = ProjectCategory::where('order', '>', $currentOrder)
                ->orderBy('order', 'asc')
                ->first();

            if (! $sibling) {
                // If same order, swap by id
                $sibling = ProjectCategory::where('order', '=', $currentOrder)
                    ->where('id', '>', $projectCategory->id)
                    ->orderBy('id', 'asc')
                    ->first();
            }
        }

        if ($sibling) {
            $tempOrder = $projectCategory->order;

            if ($sibling->order === $projectCategory->order) {
                if ($direction === 'up') {
                    $projectCategory->order = max(0, $sibling->order - 1);
                } else {
                    $sibling->order = max(0, $projectCategory->order - 1);
                }
            } else {
                $projectCategory->order = $sibling->order;
                $sibling->order = $tempOrder;
            }

            $projectCategory->save();
            $sibling->save();

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => __('Order updated successfully.'),
            ]);
        }

        return redirect()->back();
    }
}
