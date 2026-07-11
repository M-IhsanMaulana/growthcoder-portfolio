<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExperienceResource;
use App\Models\Experience;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    /**
     * Display a listing of experiences.
     */
    public function index(Request $request)
    {
        $experiences = Experience::query()
            ->with('logo')
            ->orderBy('order', 'asc')
            ->orderByDesc('start_date')
            ->get();

        return ExperienceResource::collection($experiences);
    }
}
