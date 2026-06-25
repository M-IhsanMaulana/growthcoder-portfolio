<?php

namespace App\Http\Controllers;

use App\Models\Technology;
use App\Enums\TechnologyCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;

class TechnologyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Technology::query()->with('logo');

        // Apply filters
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Sort: Featured first, then alphabetically by name
        $query->orderBy('is_featured', 'desc')->orderBy('name', 'asc');

        $technologies = $query->get()->map(function ($tech) {
            // Dynamic relationship count checks (safe if tables do not exist yet)
            $tech->projects_count = Schema::hasTable('project_technology')
                ? DB::table('project_technology')->where('technology_id', $tech->id)->count()
                : 0;

            $tech->skills_count = Schema::hasTable('skills')
                ? DB::table('skills')->where('technology_id', $tech->id)->count()
                : 0;

            return $tech;
        });

        if ($request->wantsJson()) {
            return response()->json($technologies);
        }

        return Inertia::render('technologies/Index', [
            'technologies' => $technologies,
            'filters' => $request->only(['q', 'category', 'featured']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:technologies,name',
            'slug' => 'nullable|string|max:255|unique:technologies,slug|regex:/^[a-z0-9\-]+$/',
            'category' => ['required', new Enum(TechnologyCategory::class)],
            'logo_media_id' => 'nullable|integer|exists:media,id',
            'description' => 'nullable|string',
            'url' => 'nullable|string|max:255|url',
            'is_featured' => 'required|boolean',
        ], [
            'name.unique' => __('Nama teknologi ini sudah terdaftar dalam sistem.'),
            'slug.unique' => __('Slug teknologi ini sudah terdaftar dalam sistem.'),
        ]);

        $slug = $request->filled('slug')
            ? Str::slug($request->input('slug'))
            : Str::slug($request->input('name'));

        // Ensure uniqueness of auto-generated slug
        $originalSlug = $slug;
        $count = 1;
        while (Technology::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        Technology::create([
            'name' => $request->input('name'),
            'slug' => $slug,
            'category' => $request->input('category'),
            'logo_media_id' => $request->input('logo_media_id'),
            'description' => $request->input('description'),
            'url' => $request->input('url'),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Technology created successfully.'),
        ]);

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Technology $technology)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:technologies,name,' . $technology->id,
            'slug' => 'required|string|max:255|unique:technologies,slug,' . $technology->id . '|regex:/^[a-z0-9\-]+$/',
            'category' => ['required', new Enum(TechnologyCategory::class)],
            'logo_media_id' => 'nullable|integer|exists:media,id',
            'description' => 'nullable|string',
            'url' => 'nullable|string|max:255|url',
            'is_featured' => 'required|boolean',
        ], [
            'name.unique' => __('Nama teknologi ini sudah terdaftar dalam sistem.'),
            'slug.unique' => __('Slug teknologi ini sudah terdaftar dalam sistem.'),
        ]);

        $slug = Str::slug($request->input('slug'));

        $technology->update([
            'name' => $request->input('name'),
            'slug' => $slug,
            'category' => $request->input('category'),
            'logo_media_id' => $request->input('logo_media_id'),
            'description' => $request->input('description'),
            'url' => $request->input('url'),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Technology updated successfully.'),
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Technology $technology)
    {
        // Safe cascade deleting for related records (if tables exist)
        if (Schema::hasTable('project_technology')) {
            DB::table('project_technology')->where('technology_id', $technology->id)->delete();
        }

        if (Schema::hasTable('skills')) {
            DB::table('skills')->where('technology_id', $technology->id)->delete();
        }

        $technology->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Technology deleted successfully.'),
        ]);

        return redirect()->back();
    }
}
