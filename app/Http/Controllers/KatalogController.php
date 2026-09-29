<?php

namespace App\Http\Controllers;

use App\Models\Properti;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KatalogController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->string('type')->toString();

        $query = Properti::query()
            ->where('status', 'published')
            ->with(['photos', 'facilities', 'rooms']);

        if ($type !== '' && $type !== 'semua') {
            $query->where('tipe', $type);
        }

        if ($request->filled('lokasi')) {
            $query->where('alamat', 'like', '%'.$request->string('lokasi').'%');
        }

        // Harga sekarang ada di unit, bukan properti - filter properti yang
        // punya minimal satu unit dengan harga di rentang yang diminta.
        if ($request->filled('harga_min') || $request->filled('harga_max')) {
            $query->whereHas('rooms', function ($r) use ($request) {
                if ($request->filled('harga_min')) {
                    $r->where('harga', '>=', (int) $request->input('harga_min'));
                }
                if ($request->filled('harga_max')) {
                    $r->where('harga', '<=', (int) $request->input('harga_max'));
                }
            });
        }

        if ($request->boolean('tersedia')) {
            $query->whereHas('rooms', fn ($r) => $r->where('status', 'tersedia'));
        }

        $sort = $request->string('sort')->toString();

        if (in_array($sort, ['harga_asc', 'harga_desc'], true)) {
            $query->withMin('rooms as harga_termurah', 'harga');
        }

        match ($sort) {
            'harga_asc' => $query->orderBy('harga_termurah'),
            'harga_desc' => $query->orderByDesc('harga_termurah'),
            default => $query->latest(),
        };

        $properties = $query->paginate(9)->withQueryString();

        $lokasiOptions = Properti::query()
            ->where('status', 'published')
            ->get()
            ->map(fn (Properti $property) => $property->locationLabel())
            ->unique()
            ->sort()
            ->values();

        return view('katalog.index', [
            'properties' => $properties,
            'totalPublished' => Properti::query()->where('status', 'published')->count(),
            'lokasiOptions' => $lokasiOptions,
            'filters' => [
                'type' => $type ?: 'semua',
                'lokasi' => $request->string('lokasi')->toString(),
                'harga_min' => $request->string('harga_min')->toString(),
                'harga_max' => $request->string('harga_max')->toString(),
                'tersedia' => $request->boolean('tersedia'),
                'sort' => $sort ?: 'terbaru',
            ],
        ]);
    }

    public function show(Properti $property): View
    {
        abort_unless($property->status === 'published', 404);

        $property->load(['photos', 'facilities', 'rooms' => fn ($q) => $q->orderBy('kode')]);

        return view('katalog.show', [
            'property' => $property,
        ]);
    }
}
