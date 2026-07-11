<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of active services, ordered by order ASC.
     */
    public function index(Request $request)
    {
        $services = Service::query()
            ->where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        return ServiceResource::collection($services);
    }
}
