<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\EducationResource;
use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    /**
     * Display a listing of educations.
     */
    public function index(Request $request)
    {
        $educations = Education::query()
            ->with('logo')
            ->orderBy('order', 'asc')
            ->orderByDesc('start_date')
            ->get();

        return EducationResource::collection($educations);
    }
}
