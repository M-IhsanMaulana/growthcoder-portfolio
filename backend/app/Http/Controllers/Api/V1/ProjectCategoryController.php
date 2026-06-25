<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectCategoryResource;
use App\Models\ProjectCategory;

class ProjectCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = ProjectCategory::query()
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return ProjectCategoryResource::collection($categories);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $category = ProjectCategory::where('slug', $slug)->firstOrFail();

        return new ProjectCategoryResource($category);
    }
}
