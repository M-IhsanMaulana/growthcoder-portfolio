<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class StatsController extends Controller
{
    /**
     * Get statistics for technologies, projects, and years of experience.
     */
    public function index(): JsonResponse
    {
        $technologiesCount = Technology::count();
        $projectsCount = Project::count();

        $earliestStartDate = Experience::min('start_date');
        $yearsOfExperience = 0;

        if ($earliestStartDate) {
            $startDate = Carbon::parse($earliestStartDate);
            $yearsOfExperience = (int) max(0, $startDate->diffInYears(now()));
        }

        return response()->json([
            'data' => [
                'technologies_count' => $technologiesCount,
                'projects_count' => $projectsCount,
                'years_of_experience' => $yearsOfExperience,
            ],
        ]);
    }
}
