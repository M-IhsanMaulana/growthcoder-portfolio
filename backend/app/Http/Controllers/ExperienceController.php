<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ExperienceController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company' => 'required|string|max:255',
            'title_position' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date_format:Y-m',
            'end_date' => 'nullable|date_format:Y-m|after_or_equal:start_date',
            'description' => 'nullable|string',
            'website_url' => 'nullable|url|max:255',
            'logo_media_id' => 'nullable|integer|exists:media,id',
            'order' => 'required|integer|min:0',
        ]);

        $validated['start_date'] = Carbon::createFromFormat('Y-m', $request->input('start_date'))->startOfMonth()->format('Y-m-d');
        if ($request->filled('end_date')) {
            $validated['end_date'] = Carbon::createFromFormat('Y-m', $request->input('end_date'))->startOfMonth()->format('Y-m-d');
        } else {
            $validated['end_date'] = null;
        }

        Experience::create($validated);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Riwayat pengalaman kerja berhasil ditambahkan.'),
        ]);

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Experience $experience)
    {
        $validated = $request->validate([
            'company' => 'required|string|max:255',
            'title_position' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date_format:Y-m',
            'end_date' => 'nullable|date_format:Y-m|after_or_equal:start_date',
            'description' => 'nullable|string',
            'website_url' => 'nullable|url|max:255',
            'logo_media_id' => 'nullable|integer|exists:media,id',
            'order' => 'required|integer|min:0',
        ]);

        $validated['start_date'] = Carbon::createFromFormat('Y-m', $request->input('start_date'))->startOfMonth()->format('Y-m-d');
        if ($request->filled('end_date')) {
            $validated['end_date'] = Carbon::createFromFormat('Y-m', $request->input('end_date'))->startOfMonth()->format('Y-m-d');
        } else {
            $validated['end_date'] = null;
        }

        $experience->update($validated);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Riwayat pengalaman kerja berhasil diperbarui.'),
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Experience $experience)
    {
        $experience->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Riwayat pengalaman kerja berhasil dihapus.'),
        ]);

        return redirect()->back();
    }

    /**
     * Reorder work experiences.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:experiences,id',
        ]);

        $ids = $request->input('ids');

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                Experience::where('id', $id)->update(['order' => $index]);
            }
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Urutan riwayat pengalaman kerja berhasil diperbarui.'),
        ]);

        return redirect()->back();
    }
}
