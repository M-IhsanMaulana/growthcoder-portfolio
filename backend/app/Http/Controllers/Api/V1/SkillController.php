<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkillResource;
use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Skill::query()->with([
            'items' => function ($q) use ($request) {
                $q->orderBy('order', 'asc');
                if ($request->boolean('featured')) {
                    $q->where('is_featured', true);
                }
            },
            'items.technology.logo',
        ]);

        $skills = $query->orderBy('order', 'asc')
            ->get();

        // If filtering featured or specific search, we might want to filter out empty groups
        if ($request->boolean('featured')) {
            $skills = $skills->filter(function ($skill) {
                return $skill->items->count() > 0;
            })->values();
        }

        return SkillResource::collection($skills);
    }
}
