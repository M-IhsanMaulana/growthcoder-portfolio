<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ServiceController extends Controller
{
    /**
     * Display a listing of the services.
     */
    public function index(Request $request)
    {
        $services = Service::query()
            ->orderBy('order', 'asc')
            ->get();

        return Inertia::render('services/Index', [
            'services' => $services,
        ]);
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:services,slug',
            'short_description' => 'required|string|max:200',
            'long_description' => 'nullable|string',
            'icon' => 'nullable|string|max:65535',
            'is_active' => 'required|boolean',
            'order' => 'required|integer|min:0',
        ]);

        Service::create([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'short_description' => $request->input('short_description'),
            'long_description' => $request->input('long_description'),
            'icon' => $request->input('icon'),
            'is_active' => $request->boolean('is_active'),
            'order' => $request->input('order'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Layanan berhasil ditambahkan.'),
        ]);

        return redirect()->back();
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:services,slug,'.$service->id,
            'short_description' => 'required|string|max:200',
            'long_description' => 'nullable|string',
            'icon' => 'nullable|string|max:65535',
            'is_active' => 'required|boolean',
            'order' => 'required|integer|min:0',
        ]);

        $service->update([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'short_description' => $request->input('short_description'),
            'long_description' => $request->input('long_description'),
            'icon' => $request->input('icon'),
            'is_active' => $request->boolean('is_active'),
            'order' => $request->input('order'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Layanan berhasil diperbarui.'),
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified service.
     */
    public function destroy(Service $service)
    {
        $service->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Layanan berhasil dihapus.'),
        ]);

        return redirect()->back();
    }

    /**
     * Reorder services via drag-and-drop.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:services,id',
        ]);

        $ids = $request->input('ids');

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                Service::where('id', $id)->update(['order' => $index]);
            }
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Urutan layanan berhasil diperbarui.'),
        ]);

        return redirect()->back();
    }

    /**
     * Toggle the active status of the specified service.
     */
    public function toggleActive(Service $service)
    {
        $service->update(['is_active' => ! $service->is_active]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $service->is_active
                ? __('Layanan berhasil diaktifkan.')
                : __('Layanan berhasil dinonaktifkan.'),
        ]);

        return redirect()->back();
    }
}
