<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectDetailResource;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Project::query()
            ->with(['category', 'coverImage', 'technologies'])
            ->where('status', 'published')
            ->orderBy('order', 'asc')
            ->orderBy('published_at', 'desc');

        // Filter by category slug
        if ($request->filled('category')) {
            $categorySlug = $request->input('category');
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Filter by featured status
        if ($request->boolean('featured') || $request->input('featured') === 'true') {
            $query->where('is_featured', true);
        }

        $projects = $query->get();

        return ProjectResource::collection($projects);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $project = Project::query()
            ->with(['category', 'coverImage', 'technologies', 'galleryImages'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return new ProjectDetailResource($project);
    }
}
