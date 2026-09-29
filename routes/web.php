<?php

use App\Http\Controllers\Admin\LaporanKerusakanController as AdminLaporanKerusakanController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JadwalCekController as AdminJadwalCekController;
use App\Http\Controllers\Admin\PembayaranController as AdminPembayaranController;
use App\Http\Controllers\Admin\PropertiController as AdminPropertiController;
use App\Http\Controllers\Admin\FotoPropertiController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\PerpanjanganSewaController as AdminPerpanjanganSewaController;
use App\Http\Controllers\Admin\ReservasiController as AdminReservasiController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\StafController;
use App\Http\Controllers\Admin\PenyewaController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\LaporanKerusakanController;
use App\Http\Controllers\FavoritController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JadwalCekController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\SewaController;
use App\Http\Controllers\PerpanjanganSewaController;
use App\Http\Controllers\ReservasiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/cara-kerja', [HomeController::class, 'caraKerja'])->name('cara-kerja');
Route::get('/tentang', [HomeController::class, 'tentang'])->name('tentang');

Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/{property:slug}', [KatalogController::class, 'show'])->name('katalog.show');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('profil', [ProfilController::class, 'edit'])->name('profile.edit');
    Route::patch('profil', [ProfilController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'role:penyewa'])->group(function () {
    Route::get('favorit', [FavoritController::class, 'index'])->name('favorit.index');
    Route::post('katalog/{property}/favorit', [FavoritController::class, 'toggle'])->name('favorit.toggle');

    Route::post('reservasi', [ReservasiController::class, 'store'])->name('reservasi.store');
    Route::get('reservasi', [ReservasiController::class, 'index'])->name('reservasi.index');
    Route::get('reservasi/{reservation}', [ReservasiController::class, 'show'])->name('reservasi.show');

    Route::post('reservasi/{reservation}/inspeksi', [JadwalCekController::class, 'store'])->name('inspeksi.store');
    Route::post('reservasi/{reservation}/bayar', [PembayaranController::class, 'storeForReservation'])->name('pembayaran.store');
    Route::post('reservasi/{reservation}/batal', [ReservasiController::class, 'cancel'])->name('reservasi.cancel');

    Route::get('pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');

    Route::get('sewa', [SewaController::class, 'index'])->name('sewa.index');
    Route::get('sewa/{rental}/perpanjang', [PerpanjanganSewaController::class, 'create'])->name('perpanjangan.create');
    Route::post('sewa/{rental}/perpanjang', [PerpanjanganSewaController::class, 'store'])->name('perpanjangan.store');
    Route::post('perpanjangan/{extension}/bayar', [PembayaranController::class, 'storeForExtension'])->name('perpanjangan.bayar');

    Route::get('kerusakan', [LaporanKerusakanController::class, 'index'])->name('kerusakan.index');
    Route::post('sewa/{rental}/kerusakan', [LaporanKerusakanController::class, 'store'])->name('kerusakan.store');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('properti', AdminPropertiController::class)
        ->except(['show'])
        ->parameters(['properti' => 'property']);
    Route::delete('properti-foto/{photo}', [FotoPropertiController::class, 'destroy'])->name('properti.foto.destroy');
    Route::post('properti/{property}/kamar', [UnitController::class, 'store'])->name('properti.kamar.store');
    Route::patch('kamar/{room}', [UnitController::class, 'update'])->name('kamar.update');
    Route::delete('kamar/{room}', [UnitController::class, 'destroy'])->name('kamar.destroy');

    Route::get('reservasi', [AdminReservasiController::class, 'index'])->name('reservasi.index');
    Route::post('reservasi/{reservation}/keputusan', [AdminReservasiController::class, 'review'])->name('reservasi.review');

    Route::get('inspeksi', [AdminJadwalCekController::class, 'index'])->name('inspeksi.index');
    Route::post('inspeksi/{inspection}/keputusan', [AdminJadwalCekController::class, 'review'])->name('inspeksi.review');
    Route::post('inspeksi/{inspection}/selesai', [AdminJadwalCekController::class, 'done'])->name('inspeksi.done');

    Route::get('pembayaran', [AdminPembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('pembayaran/{payment}/keputusan', [AdminPembayaranController::class, 'review'])->name('pembayaran.review');

    Route::get('perpanjangan', [AdminPerpanjanganSewaController::class, 'index'])->name('perpanjangan.index');
    Route::post('perpanjangan/{extension}/keputusan', [AdminPerpanjanganSewaController::class, 'review'])->name('perpanjangan.review');

    Route::get('kerusakan', [AdminLaporanKerusakanController::class, 'index'])->name('kerusakan.index');
    Route::post('kerusakan/{report}/status', [AdminLaporanKerusakanController::class, 'updateStatus'])->name('kerusakan.status');

    Route::get('laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/csv', [AdminLaporanController::class, 'csv'])->name('laporan.csv');

    Route::resource('staf', StafController::class)->except(['show']);

    Route::get('pengaturan', [PengaturanController::class, 'edit'])->name('pengaturan.edit');
    Route::patch('pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');

    Route::get('penyewa', [PenyewaController::class, 'index'])->name('penyewa.index');
});
