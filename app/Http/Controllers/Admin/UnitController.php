<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Properti;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UnitController extends Controller
{
    /**
     * Tambah kamar baru sekaligus banyak (dipakai oleh repeater "Tambah
     * Kamar Baru" di halaman edit - bisa satu baris atau puluhan sekaligus
     * lewat fitur generate).
     */
    public function store(Request $request, Properti $property): RedirectResponse
    {
        $validated = $request->validate([
            'kamar' => ['required', 'array', 'min:1'],
            'kamar.*.kode' => ['required', 'distinct', 'string', 'max:20'],
            'kamar.*.harga' => ['required', 'integer', 'min:0'],
            'kamar.*.harga_tahunan' => ['nullable', 'integer', 'min:0'],
            'kamar.*.ukuran_kamar' => ['nullable', 'string', 'max:50'],
            'kamar.*.tipe_kamar_mandi' => ['nullable', 'string', 'max:50'],
        ]);

        $existingCodes = $property->rooms()->pluck('kode')->all();

        foreach ($validated['kamar'] as $room) {
            if (in_array($room['kode'], $existingCodes, true)) {
                throw ValidationException::withMessages([
                    'kamar' => "Kode kamar \"{$room['kode']}\" sudah dipakai di properti ini.",
                ]);
            }
            $existingCodes[] = $room['kode'];
        }

        foreach ($validated['kamar'] as $room) {
            $property->rooms()->create($room);
        }

        return redirect()->route('admin.properti.edit', $property)
            ->with('success', count($validated['kamar']).' kamar berhasil ditambahkan.');
    }

    public function update(Request $request, Unit $room): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:unit,kode,'.$room->id.',id,properti_id,'.$room->properti_id],
            'harga' => ['required', 'integer', 'min:0'],
            'harga_tahunan' => ['nullable', 'integer', 'min:0'],
            'ukuran_kamar' => ['nullable', 'string', 'max:50'],
            'tipe_kamar_mandi' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:tersedia,terisi'],
        ]);

        $room->update($validated);

        return redirect()->route('admin.properti.edit', $room->property)->with('success', 'Kamar berhasil diperbarui.');
    }

    public function destroy(Unit $room): RedirectResponse
    {
        $property = $room->property;

        if ($room->reservations()->exists()) {
            return back()->with('error', 'Kamar tidak bisa dihapus karena sudah memiliki riwayat reservasi.');
        }

        if ($property->tipe !== 'kos' && $property->rooms()->count() <= 1) {
            return back()->with('error', 'Properti ini harus punya minimal 1 unit dan tidak bisa dihapus.');
        }

        $room->delete();

        return redirect()->route('admin.properti.edit', $property)->with('success', 'Kamar berhasil dihapus.');
    }
}
