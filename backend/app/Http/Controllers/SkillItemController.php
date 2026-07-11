<?php

namespace App\Http\Controllers;

use App\Enums\SkillLevel;
use App\Models\SkillItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;

class SkillItemController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'skill_id' => 'required|integer|exists:skills,id',
            'name' => 'required_without:technology_id|nullable|string|max:255',
            'technology_id' => 'required_without:name|nullable|integer|exists:technologies,id|unique:skill_items,technology_id',
            'level' => ['required', new Enum(SkillLevel::class)],
            'years_of_experience' => 'nullable|numeric|min:0|max:99.9',
            'is_featured' => 'required|boolean',
            'order' => 'required|integer|min:0',
        ], [
            'technology_id.unique' => __('Teknologi ini sudah terdaftar di entri keahlian lain.'),
            'name.required_without' => __('Nama harus diisi jika tidak memilih teknologi.'),
            'technology_id.required_without' => __('Silakan pilih teknologi atau isi nama kustom.'),
        ]);

        SkillItem::create([
            'skill_id' => $request->input('skill_id'),
            'name' => $request->input('technology_id') ? null : $request->input('name'),
            'technology_id' => $request->input('technology_id'),
            'level' => $request->input('level'),
            'years_of_experience' => $request->input('years_of_experience'),
            'is_featured' => $request->boolean('is_featured'),
            'order' => $request->input('order'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Item keahlian berhasil ditambahkan.'),
        ]);

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SkillItem $skillItem)
    {
        $request->validate([
            'skill_id' => 'required|integer|exists:skills,id',
            'name' => 'required_without:technology_id|nullable|string|max:255',
            'technology_id' => 'required_without:name|nullable|integer|exists:technologies,id|unique:skill_items,technology_id,'.$skillItem->id,
            'level' => ['required', new Enum(SkillLevel::class)],
            'years_of_experience' => 'nullable|numeric|min:0|max:99.9',
            'is_featured' => 'required|boolean',
            'order' => 'required|integer|min:0',
        ], [
            'technology_id.unique' => __('Teknologi ini sudah terdaftar di entri keahlian lain.'),
            'name.required_without' => __('Nama harus diisi jika tidak memilih teknologi.'),
            'technology_id.required_without' => __('Silakan pilih teknologi atau isi nama kustom.'),
        ]);

        $skillItem->update([
            'skill_id' => $request->input('skill_id'),
            'name' => $request->input('technology_id') ? null : $request->input('name'),
            'technology_id' => $request->input('technology_id'),
            'level' => $request->input('level'),
            'years_of_experience' => $request->input('years_of_experience'),
            'is_featured' => $request->boolean('is_featured'),
            'order' => $request->input('order'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Item keahlian berhasil diperbarui.'),
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SkillItem $skillItem)
    {
        $skillItem->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Item keahlian berhasil dihapus.'),
        ]);

        return redirect()->back();
    }

    /**
     * Reorder skill items within a group.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:skill_items,id',
        ]);

        $ids = $request->input('ids');

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                SkillItem::where('id', $id)->update(['order' => $index]);
            }
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Urutan item keahlian berhasil diperbarui.'),
        ]);

        return redirect()->back();
    }
}
