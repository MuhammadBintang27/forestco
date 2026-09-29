<?php

namespace Database\Seeders;

use App\Models\Properti;
use App\Models\Pengaturan;
use App\Models\Pengguna;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = Pengguna::factory()->create([
            'nama' => 'Admin ForestCo',
            'email' => 'admin@forestco.test',
            'no_telepon' => '081234567890',
            'peran' => 'admin',
            'password' => bcrypt('password'),
        ]);

        Pengguna::factory()->create([
            'nama' => 'Budi Santoso',
            'email' => 'penyewa@forestco.test',
            'no_telepon' => '081298765432',
            'peran' => 'penyewa',
            'password' => bcrypt('password'),
        ]);

        Pengaturan::create([
            'nama_bank' => 'Bank Aceh Syariah',
            'nomor_rekening' => '1234567890',
            'nama_pemilik_rekening' => 'Cut Mardila',
            'no_wa_admin' => $admin->no_telepon,
        ]);

        $kos = Properti::create([
            'admin_id' => $admin->id,
            'tipe' => 'kos',
            'nama' => 'Forest Kost',
            'slug' => 'forest-kost',
            'alamat' => 'Jalan Sentosa No. 3, Kampung Laksana, Banda Aceh',
            'deskripsi' => 'Forest Kost menyediakan 12 kamar sewa yang nyaman dan tertata, masing-masing dilengkapi meja belajar dengan rak dinding, lemari pakaian pintu geser bercermin, dan kamar mandi dalam. Berlokasi di Jalan Sentosa No. 3, Kampung Laksana, lingkungan kos ini aman dan mudah diakses, cocok untuk mahasiswa maupun pekerja.',
            'status' => 'published',
        ]);

        foreach (['WiFi', 'Kamar Mandi Dalam', 'Lemari Pintu Geser', 'Meja Belajar', 'Area Parkir'] as $facility) {
            $kos->facilities()->create(['nama' => $facility]);
        }

        foreach (['A1', 'A2', 'A3', 'A4', 'B1', 'B2', 'B3', 'B4', 'C1', 'C2', 'C3', 'C4'] as $i => $code) {
            $kos->rooms()->create([
                'kode' => $code,
                'harga' => 750000,
                'harga_tahunan' => 8000000,
                'ukuran_kamar' => '3x4 m',
                'tipe_kamar_mandi' => 'Dalam',
                'status' => in_array($code, ['A3', 'B2', 'C2'], true) ? 'terisi' : 'tersedia',
            ]);
        }

        $rumah = Properti::create([
            'admin_id' => $admin->id,
            'tipe' => 'rumah',
            'nama' => 'Rumah Damai Sentosa',
            'slug' => 'rumah-damai-sentosa',
            'alamat' => 'Jalan Merpati No. 12, Lampineung, Banda Aceh',
            'deskripsi' => 'Rumah 2 lantai dengan 3 kamar tidur, cocok untuk keluarga kecil. Lingkungan tenang dan dekat dengan fasilitas umum.',
            'status' => 'published',
        ]);

        foreach (['3 Kamar Tidur', 'Dapur', 'Carport'] as $facility) {
            $rumah->facilities()->create(['nama' => $facility]);
        }

        // Rumah/ruko selalu punya tepat 1 unit yang mewakili properti itu
        // sendiri, dan cuma diinput harga tahunan - `harga` (bulanan) di sini
        // murni turunan (harga_tahunan / 12), sama seperti yang dilakukan
        // PropertiController::syncSoleUnit() lewat form admin.
        $rumah->rooms()->create([
            'kode' => '-',
            'harga' => intdiv(38_000_000, 12),
            'harga_tahunan' => 38_000_000,
            'ukuran_kamar' => '90/120 m²',
            'tipe_kamar_mandi' => '2 Kamar Mandi',
            'status' => 'tersedia',
        ]);

        $ruko = Properti::create([
            'admin_id' => $admin->id,
            'tipe' => 'ruko',
            'nama' => 'Ruko Simpang Lima',
            'slug' => 'ruko-simpang-lima',
            'alamat' => 'Jalan T. Nyak Arief No. 8, Banda Aceh',
            'deskripsi' => 'Ruko 2 lantai strategis di simpang lima, cocok untuk usaha retail maupun kantor.',
            'status' => 'published',
        ]);

        foreach (['2 Lantai', 'Listrik 3500 Watt', 'Area Parkir Luas'] as $facility) {
            $ruko->facilities()->create(['nama' => $facility]);
        }

        $ruko->rooms()->create([
            'kode' => '-',
            'harga' => intdiv(60_000_000, 12),
            'harga_tahunan' => 60_000_000,
            'ukuran_kamar' => '6x12 m',
            'tipe_kamar_mandi' => '1 Kamar Mandi',
            'status' => 'tersedia',
        ]);
    }
}
