<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FotoProperti;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class FotoPropertiController extends Controller
{
    public function destroy(FotoProperti $photo): RedirectResponse
    {
        $property = $photo->property;

        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return redirect()->route('admin.properti.edit', $property)->with('success', 'Foto berhasil dihapus.');
    }
}
