<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class SewaController extends Controller
{
    public function index(Request $request): View
    {
        $rentals = $request->user()
            ->reservations()
            ->with(['rental.extensions.payments', 'rental.damageReports', 'room.property'])
            ->whereHas('rental')
            ->latest()
            ->get()
            ->pluck('rental');

        return view('sewa.index', [
            'rentals' => $rentals,
        ]);
    }
}
