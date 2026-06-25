<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TechnologyResource;
use App\Models\Technology;
use Illuminate\Http\Request;

class TechnologyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Technology::query()->with('logo');

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $technologies = $query->orderBy('is_featured', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        return TechnologyResource::collection($technologies);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $technology = Technology::with('logo')
            ->where('slug', $slug)
            ->firstOrFail();

        return new TechnologyResource($technology);
    }
}
