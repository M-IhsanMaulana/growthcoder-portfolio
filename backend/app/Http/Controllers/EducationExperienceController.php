<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\Experience;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EducationExperienceController extends Controller
{
    /**
     * Display the education and experience dashboard page.
     */
    public function index(Request $request)
    {
        $experiences = Experience::query()
            ->with('logo')
            ->orderBy('order', 'asc')
            ->orderByDesc('start_date')
            ->get()
            ->map(function ($exp) {
                return [
                    'id' => $exp->id,
                    'company' => $exp->company,
                    'title_position' => $exp->title_position,
                    'location' => $exp->location,
                    'start_date' => $exp->start_date?->format('Y-m'),
                    'end_date' => $exp->end_date?->format('Y-m'),
                    'description' => $exp->description,
                    'website_url' => $exp->website_url,
                    'logo_media_id' => $exp->logo_media_id,
                    'logo' => $exp->logo,
                    'order' => $exp->order,
                ];
            });

        $educations = Education::query()
            ->with('logo')
            ->orderBy('order', 'asc')
            ->orderByDesc('start_date')
            ->get()
            ->map(function ($edu) {
                return [
                    'id' => $edu->id,
                    'institution' => $edu->institution,
                    'degree' => $edu->degree,
                    'major' => $edu->major,
                    'gpa' => $edu->gpa,
                    'location' => $edu->location,
                    'start_date' => $edu->start_date?->format('Y-m'),
                    'end_date' => $edu->end_date?->format('Y-m'),
                    'description' => $edu->description,
                    'logo_media_id' => $edu->logo_media_id,
                    'logo' => $edu->logo,
                    'order' => $edu->order,
                ];
            });

        return Inertia::render('education-experience/Index', [
            'experiences' => $experiences,
            'educations' => $educations,
        ]);
    }
}
