<?php

namespace App\Http\Controllers;

use App\Models\Properti;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoritController extends Controller
{
    public function index(Request $request): View
    {
        $properties = $request->user()
            ->favoriteProperties()
            ->with('photos', 'facilities', 'rooms')
            ->latest('favorit.created_at')
            ->paginate(9);

        return view('favorit.index', [
            'properties' => $properties,
        ]);
    }

    public function toggle(Request $request, Properti $property): RedirectResponse
    {
        $user = $request->user();

        $existing = $property->favorites()->where('penyewa_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            $message = 'Dihapus dari favorit.';
        } else {
            $property->favorites()->create(['penyewa_id' => $user->id]);
            $message = 'Ditambahkan ke favorit.';
        }

        return back()->with('success', $message);
    }
}
