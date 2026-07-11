<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\Technology;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Skill::query()->with([
            'items' => function ($q) {
                $q->orderBy('order', 'asc');
            },
            'items.technology.logo',
        ]);

        // Search within technology name or custom name inside items
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('items', function ($itemQ) use ($search) {
                        $itemQ->where('name', 'like', "%{$search}%")
                            ->orWhereHas('technology', function ($techQ) use ($search) {
                                $techQ->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // Filter by skill level inside items
        if ($request->filled('level')) {
            $level = $request->input('level');
            $query->whereHas('items', function ($itemQ) use ($level) {
                $itemQ->where('level', $level);
            });
        }

        // Filter by featured status inside items
        if ($request->boolean('featured')) {
            $query->whereHas('items', function ($itemQ) {
                $itemQ->where('is_featured', true);
            });
        }

        $skills = $query->orderBy('order', 'asc')->get();
        $technologies = Technology::query()->with('logo')->orderBy('name', 'asc')->get();

        return Inertia::render('skills/Index', [
            'skills' => $skills,
            'technologies' => $technologies,
            'filters' => $request->only(['q', 'level', 'featured']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:skills,name',
            'order' => 'required|integer|min:0',
        ], [
            'name.unique' => __('Nama kategori/grup skill ini sudah terdaftar.'),
        ]);

        Skill::create([
            'name' => $request->input('name'),
            'order' => $request->input('order'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Kategori skill berhasil ditambahkan.'),
        ]);

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Skill $skill)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:skills,name,'.$skill->id,
            'order' => 'required|integer|min:0',
        ], [
            'name.unique' => __('Nama kategori/grup skill ini sudah terdaftar.'),
        ]);

        $skill->update([
            'name' => $request->input('name'),
            'order' => $request->input('order'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Kategori skill berhasil diperbarui.'),
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill)
    {
        $skill->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Kategori skill berhasil dihapus.'),
        ]);

        return redirect()->back();
    }

    /**
     * Reorder skill groups.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:skills,id',
        ]);

        $ids = $request->input('ids');

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                Skill::where('id', $id)->update(['order' => $index]);
            }
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Urutan kategori skill berhasil diperbarui.'),
        ]);

        return redirect()->back();
    }
}
