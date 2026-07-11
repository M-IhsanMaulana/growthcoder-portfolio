<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkflowResource;
use App\Models\Workflow;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    /**
     * Display a listing of workflows.
     */
    public function index(Request $request)
    {
        $workflows = Workflow::query()
            ->where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        return WorkflowResource::collection($workflows);
    }
}
