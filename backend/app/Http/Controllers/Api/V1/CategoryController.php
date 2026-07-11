<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::query()
            ->withCount(['posts' => function ($query) {
                $query->where('status', PostStatus::Published);
            }])
            ->orderBy('name', 'asc')
            ->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        return new CategoryResource($category);
    }
}
