<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DevelopmentPhilosophyResource;
use App\Models\DevelopmentPhilosophy;
use Illuminate\Http\Request;

class DevelopmentPhilosophyController extends Controller
{
    /**
     * Display a listing of development philosophies.
     */
    public function index(Request $request)
    {
        $philosophies = DevelopmentPhilosophy::query()
            ->where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        return DevelopmentPhilosophyResource::collection($philosophies);
    }
}
