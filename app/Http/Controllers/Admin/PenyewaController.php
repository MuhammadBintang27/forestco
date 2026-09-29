<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\View\View;

class PenyewaController extends Controller
{
    public function index(): View
    {
        $tenants = Pengguna::query()
            ->where('peran', 'penyewa')
            ->withCount('reservations')
            ->with(['reservations' => function ($query) {
                $query->whereHas('rental', fn ($q) => $q->where('status', 'active'))
                    ->with(['room.property', 'rental']);
            }])
            ->latest()
            ->paginate(15);

        return view('admin.penyewa.index', [
            'tenants' => $tenants,
        ]);
    }
}
