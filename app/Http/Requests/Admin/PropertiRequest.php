<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Kos mengelola harga per kamar lewat "Kelola Kamar" di halaman edit
        // (tidak lewat form ini). Rumah/ruko selalu punya tepat 1 unit yang
        // mewakili properti itu sendiri, jadi harganya diisi di sini - durasi
        // sewa rumah/ruko selalu kelipatan tahun (1/2 tahun), jadi cukup
        // harga_tahunan saja, tidak ada lagi harga per bulan.
        $isNotKos = fn () => $this->input('tipe') !== 'kos';

        return [
            'tipe' => ['required', 'in:kos,rumah,ruko'],
            'nama' => ['required', 'string', 'max:150'],
            'alamat' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'harga_tahunan' => [Rule::requiredIf($isNotKos), 'nullable', 'integer', 'min:0'],
            'ukuran_kamar' => ['nullable', 'string', 'max:50'],
            'tipe_kamar_mandi' => ['nullable', 'string', 'max:50'],
            // Status sewa unit tunggal rumah/ruko (tersedia/terisi) - beda dari
            // `status` di atas yang artinya draft/published (visibilitas katalog).
            'status_unit' => ['nullable', 'in:tersedia,terisi'],
            'status' => ['required', 'in:draft,published'],

            'photos' => ['nullable', 'array', 'max:3'],
            'photos.*' => ['file', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],

            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['nullable', 'string', 'max:100'],

            // Kamar (khusus tipe Kos) yang diisi langsung di halaman "Tambah
            // Properti" - dibuat sekaligus dengan propertinya.
            'kamar' => ['nullable', 'array'],
            'kamar.*.kode' => ['required_with:kamar', 'distinct', 'string', 'max:20'],
            'kamar.*.harga' => ['required_with:kamar', 'integer', 'min:0'],
            'kamar.*.harga_tahunan' => ['nullable', 'integer', 'min:0'],
            'kamar.*.ukuran_kamar' => ['nullable', 'string', 'max:50'],
            'kamar.*.tipe_kamar_mandi' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'photos.max' => 'Maksimal 3 foto per properti.',
        ];
    }
}
