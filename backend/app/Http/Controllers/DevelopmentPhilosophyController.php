<?php

namespace App\Http\Controllers;

use App\Models\DevelopmentPhilosophy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DevelopmentPhilosophyController extends Controller
{
    /**
     * Display a listing of the development philosophies.
     */
    public function index(Request $request)
    {
        $philosophies = DevelopmentPhilosophy::query()
            ->orderBy('order', 'asc')
            ->get();

        return Inertia::render('development-philosophies/Index', [
            'philosophies' => $philosophies,
        ]);
    }

    /**
     * Store a newly created development philosophy.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:development_philosophies,slug',
            'description' => 'required|string|max:300',
            'icon' => 'nullable|string|max:65535',
            'is_active' => 'required|boolean',
            'order' => 'required|integer|min:0',
        ]);

        DevelopmentPhilosophy::create([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'description' => $request->input('description'),
            'icon' => $request->input('icon'),
            'is_active' => $request->boolean('is_active'),
            'order' => $request->input('order'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Filosofi pengembangan berhasil ditambahkan.'),
        ]);

        return redirect()->back();
    }

    /**
     * Update the specified development philosophy.
     */
    public function update(Request $request, DevelopmentPhilosophy $developmentPhilosophy)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:development_philosophies,slug,'.$developmentPhilosophy->id,
            'description' => 'required|string|max:300',
            'icon' => 'nullable|string|max:65535',
            'is_active' => 'required|boolean',
            'order' => 'required|integer|min:0',
        ]);

        $developmentPhilosophy->update([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'description' => $request->input('description'),
            'icon' => $request->input('icon'),
            'is_active' => $request->boolean('is_active'),
            'order' => $request->input('order'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Filosofi pengembangan berhasil diperbarui.'),
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified development philosophy.
     */
    public function destroy(DevelopmentPhilosophy $developmentPhilosophy)
    {
        $developmentPhilosophy->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Filosofi pengembangan berhasil dihapus.'),
        ]);

        return redirect()->back();
    }

    /**
     * Reorder development philosophies via drag-and-drop.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:development_philosophies,id',
        ]);

        $ids = $request->input('ids');

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                DevelopmentPhilosophy::where('id', $id)->update(['order' => $index]);
            }
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Urutan filosofi pengembangan berhasil diperbarui.'),
        ]);

        return redirect()->back();
    }

    /**
     * Toggle the active status of the specified development philosophy.
     */
    public function toggleActive(DevelopmentPhilosophy $developmentPhilosophy)
    {
        $developmentPhilosophy->update(['is_active' => ! $developmentPhilosophy->is_active]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $developmentPhilosophy->is_active
                ? __('Filosofi pengembangan berhasil diaktifkan.')
                : __('Filosofi pengembangan berhasil dinonaktifkan.'),
        ]);

        return redirect()->back();
    }
}
