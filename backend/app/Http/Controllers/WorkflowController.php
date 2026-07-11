<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WorkflowController extends Controller
{
    /**
     * Display a listing of the workflows.
     */
    public function index(Request $request)
    {
        $workflows = Workflow::query()
            ->orderBy('order', 'asc')
            ->get();

        return Inertia::render('workflows/Index', [
            'workflows' => $workflows,
        ]);
    }

    /**
     * Store a newly created workflow.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:workflows,slug',
            'short_description' => 'required|string|max:200',
            'icon' => 'nullable|string|max:65535',
            'is_active' => 'required|boolean',
            'order' => 'required|integer|min:0',
        ]);

        Workflow::create([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'short_description' => $request->input('short_description'),
            'icon' => $request->input('icon'),
            'is_active' => $request->boolean('is_active'),
            'order' => $request->input('order'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Alur kerja berhasil ditambahkan.'),
        ]);

        return redirect()->back();
    }

    /**
     * Update the specified workflow.
     */
    public function update(Request $request, Workflow $workflow)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:workflows,slug,'.$workflow->id,
            'short_description' => 'required|string|max:200',
            'icon' => 'nullable|string|max:65535',
            'is_active' => 'required|boolean',
            'order' => 'required|integer|min:0',
        ]);

        $workflow->update([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'short_description' => $request->input('short_description'),
            'icon' => $request->input('icon'),
            'is_active' => $request->boolean('is_active'),
            'order' => $request->input('order'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Alur kerja berhasil diperbarui.'),
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified workflow.
     */
    public function destroy(Workflow $workflow)
    {
        $workflow->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Alur kerja berhasil dihapus.'),
        ]);

        return redirect()->back();
    }

    /**
     * Reorder workflows via drag-and-drop.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:workflows,id',
        ]);

        $ids = $request->input('ids');

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                Workflow::where('id', $id)->update(['order' => $index]);
            }
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Urutan alur kerja berhasil diperbarui.'),
        ]);

        return redirect()->back();
    }

    /**
     * Toggle the active status of the specified workflow.
     */
    public function toggleActive(Workflow $workflow)
    {
        $workflow->update(['is_active' => ! $workflow->is_active]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $workflow->is_active
                ? __('Alur kerja berhasil diaktifkan.')
                : __('Alur kerja berhasil dinonaktifkan.'),
        ]);

        return redirect()->back();
    }
}
