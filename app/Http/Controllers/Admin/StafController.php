<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class StafController extends Controller
{
    public function index(): View
    {
        $staff = Pengguna::query()->where('peran', 'admin')->latest()->paginate(15);

        return view('admin.staf.index', [
            'staff' => $staff,
        ]);
    }

    public function create(): View
    {
        return view('admin.staf.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:pengguna,email'],
            'no_telepon' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Pengguna::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_telepon' => $validated['no_telepon'],
            'password' => Hash::make($validated['password']),
            'peran' => 'admin',
        ]);

        return redirect()->route('admin.staf.index')->with('success', 'Akun admin berhasil ditambahkan.');
    }

    public function edit(Pengguna $staf): View
    {
        abort_unless($staf->isAdmin(), 404);

        return view('admin.staf.edit', ['staf' => $staf]);
    }

    public function update(Request $request, Pengguna $staf): RedirectResponse
    {
        abort_unless($staf->isAdmin(), 404);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:pengguna,email,'.$staf->id],
            'no_telepon' => ['required', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $staf->fill([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_telepon' => $validated['no_telepon'],
        ]);

        if (! empty($validated['password'])) {
            $staf->password = Hash::make($validated['password']);
        }

        $staf->save();

        return redirect()->route('admin.staf.index')->with('success', 'Akun admin berhasil diperbarui.');
    }

    public function destroy(Request $request, Pengguna $staf): RedirectResponse
    {
        abort_unless($staf->isAdmin(), 404);

        if ($staf->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        if (Pengguna::query()->where('peran', 'admin')->count() <= 1) {
            return back()->with('error', 'Tidak bisa menghapus admin terakhir.');
        }

        $staf->delete();

        return redirect()->route('admin.staf.index')->with('success', 'Akun admin berhasil dihapus.');
    }
}
