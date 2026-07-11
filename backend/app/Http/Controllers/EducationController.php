<?php

namespace App\Http\Controllers;

use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class EducationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution' => 'required|string|max:255',
            'degree' => 'nullable|string|max:255',
            'major' => 'required|string|max:255',
            'gpa' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date_format:Y-m',
            'end_date' => 'nullable|date_format:Y-m|after_or_equal:start_date',
            'description' => 'nullable|string',
            'logo_media_id' => 'nullable|integer|exists:media,id',
            'order' => 'required|integer|min:0',
        ]);

        $validated['start_date'] = Carbon::createFromFormat('Y-m', $request->input('start_date'))->startOfMonth()->format('Y-m-d');
        if ($request->filled('end_date')) {
            $validated['end_date'] = Carbon::createFromFormat('Y-m', $request->input('end_date'))->startOfMonth()->format('Y-m-d');
        } else {
            $validated['end_date'] = null;
        }

        Education::create($validated);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Riwayat pendidikan berhasil ditambahkan.'),
        ]);

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Education $education)
    {
        $validated = $request->validate([
            'institution' => 'required|string|max:255',
            'degree' => 'nullable|string|max:255',
            'major' => 'required|string|max:255',
            'gpa' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date_format:Y-m',
            'end_date' => 'nullable|date_format:Y-m|after_or_equal:start_date',
            'description' => 'nullable|string',
            'logo_media_id' => 'nullable|integer|exists:media,id',
            'order' => 'required|integer|min:0',
        ]);

        $validated['start_date'] = Carbon::createFromFormat('Y-m', $request->input('start_date'))->startOfMonth()->format('Y-m-d');
        if ($request->filled('end_date')) {
            $validated['end_date'] = Carbon::createFromFormat('Y-m', $request->input('end_date'))->startOfMonth()->format('Y-m-d');
        } else {
            $validated['end_date'] = null;
        }

        $education->update($validated);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Riwayat pendidikan berhasil diperbarui.'),
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Education $education)
    {
        $education->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Riwayat pendidikan berhasil dihapus.'),
        ]);

        return redirect()->back();
    }

    /**
     * Reorder educations.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:educations,id',
        ]);

        $ids = $request->input('ids');

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                Education::where('id', $id)->update(['order' => $index]);
            }
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Urutan riwayat pendidikan berhasil diperbarui.'),
        ]);

        return redirect()->back();
    }
}
