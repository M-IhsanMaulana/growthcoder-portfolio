<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ApiDocsController extends Controller
{
    /**
     * Render the API Documentation page.
     */
    public function index(Request $request): Response
    {
        $settings = SiteSetting::first();

        return Inertia::render('ApiDocs', [
            'apiKey' => $settings?->api_key,
        ]);
    }
}
