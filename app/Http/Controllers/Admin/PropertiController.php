<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PropertiRequest;
use App\Models\Properti;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PropertiController extends Controller
{
    public function index(): View
    {
        $properties = Properti::query()
            ->withCount(['photos', 'rooms', 'reservations'])
            ->with('rooms')
            ->latest()
            ->paginate(12);

        return view('admin.properti.index', [
            'properties' => $properties,
        ]);
    }

    public function create(): View
    {
        return view('admin.properti.create');
    }

    public function store(PropertiRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $property = Properti::create([
            'admin_id' => $request->user()->id,
            'tipe' => $validated['tipe'],
            'nama' => $validated['nama'],
            'slug' => $this->uniqueSlug($validated['nama']),
            'alamat' => $validated['alamat'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'status' => $validated['status'],
        ]);

        $this->syncFacilities($property, $validated['facilities'] ?? []);
        $this->storePhotos($property, $request->file('photos', []));
        $this->syncSoleUnit($property, $validated);

        // Kos bisa langsung diisi kamarnya di form ini juga, tidak wajib ke
        // halaman edit dulu.
        if ($property->tipe === 'kos') {
            foreach ($validated['kamar'] ?? [] as $room) {
                $property->rooms()->create($room);
            }
        }

        return redirect()->route('admin.properti.edit', $property)
            ->with('success', 'Properti berhasil ditambahkan.');
    }

    public function edit(Properti $property): View
    {
        $property->load(['photos', 'facilities', 'rooms' => fn ($q) => $q->orderBy('kode')]);

        return view('admin.properti.edit', [
            'property' => $property,
        ]);
    }

    public function update(PropertiRequest $request, Properti $property): RedirectResponse
    {
        $validated = $request->validated();

        $existingPhotoCount = $property->photos()->count();
        $newPhotoCount = count($request->file('photos', []));

        if ($existingPhotoCount + $newPhotoCount > 3) {
            throw ValidationException::withMessages([
                'photos' => 'Total foto maksimal 3. Hapus foto lama terlebih dahulu jika ingin menambah foto baru.',
            ]);
        }

        $property->update([
            'tipe' => $validated['tipe'],
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'status' => $validated['status'],
        ]);

        $this->syncFacilities($property, $validated['facilities'] ?? []);
        $this->storePhotos($property, $request->file('photos', []));
        $this->syncSoleUnit($property, $validated);

        return redirect()->route('admin.properti.edit', $property)
            ->with('success', 'Properti berhasil diperbarui.');
    }

    public function destroy(Properti $property): RedirectResponse
    {
        if ($property->reservations()->exists()) {
            return back()->with('error', 'Properti tidak bisa dihapus karena sudah memiliki riwayat reservasi. Ubah status ke draft jika ingin menyembunyikannya dari katalog.');
        }

        foreach ($property->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        $property->delete();

        return redirect()->route('admin.properti.index')->with('success', 'Properti berhasil dihapus.');
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Properti::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }

    private function syncFacilities(Properti $property, array $facilities): void
    {
        $property->facilities()->delete();

        $names = array_values(array_filter(array_map('trim', $facilities)));

        foreach ($names as $name) {
            $property->facilities()->create(['nama' => $name]);
        }
    }

    /**
     * Rumah/ruko selalu punya tepat 1 unit yang mewakili properti itu
     * sendiri (sesuai ERD: reservasi/sewa selalu berelasi ke unit, bukan
     * properti langsung) - dibuat/diperbarui lewat harga & atribut fisik
     * yang diisi pada form properti ini. Kos tidak disentuh di sini; kamar
     * dikelola satu per satu lewat panel "Kelola Kamar".
     */
    private function syncSoleUnit(Properti $property, array $validated): void
    {
        if ($property->tipe === 'kos') {
            return;
        }

        $data = [
            // Rumah/ruko cuma diinput harga tahunan lewat form - harga bulanan
            // di sini murni nilai turunan (harga_tahunan / 12), tidak pernah
            // dipakai buat hitung tagihan (durasi rumah/ruko selalu kelipatan
            // tahun), cuma supaya kolom `harga` tetap terisi wajar.
            'harga' => intdiv($validated['harga_tahunan'], 12),
            'harga_tahunan' => $validated['harga_tahunan'],
            'ukuran_kamar' => $validated['ukuran_kamar'] ?? null,
            'tipe_kamar_mandi' => $validated['tipe_kamar_mandi'] ?? null,
        ];

        $unit = $property->rooms()->first();

        if ($unit) {
            // Status sewa (tersedia/terisi) boleh diubah manual di sini juga -
            // kalau tidak dikirim (mis. request lama), pertahankan nilai lama.
            $data['status'] = $validated['status_unit'] ?? $unit->status;
            $unit->update($data);
        } else {
            $data['status'] = $validated['status_unit'] ?? 'tersedia';
            $property->rooms()->create(array_merge($data, ['kode' => '-']));
        }
    }

    private function storePhotos(Properti $property, array $files): void
    {
        $nextOrder = (int) $property->photos()->max('urutan') + 1;

        foreach ($files as $file) {
            if (! $file) {
                continue;
            }

            $path = $file->store('properti', 'public');

            $property->photos()->create([
                'path' => $path,
                'urutan' => $nextOrder++,
            ]);
        }
    }

}
