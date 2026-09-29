<?php

namespace App\Http\Controllers;

use App\Models\Properti;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featured = Properti::query()
            ->where('status', 'published')
            ->with(['photos'])
            ->latest()
            ->take(6)
            ->get();

        return view('home', [
            'featured' => $featured,
        ]);
    }

    public function caraKerja(): View
    {
        return view('cara-kerja');
    }

    public function tentang(): View
    {
        return view('tentang');
    }
}
