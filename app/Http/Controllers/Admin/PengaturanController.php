<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function edit(): View
    {
        return view('admin.pengaturan.edit', [
            'setting' => Pengaturan::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_bank' => ['required', 'string', 'max:100'],
            'nomor_rekening' => ['required', 'string', 'max:50'],
            'nama_pemilik_rekening' => ['required', 'string', 'max:150'],
            'no_wa_admin' => ['required', 'string', 'max:20'],
        ]);

        Pengaturan::current()->update($validated);

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
