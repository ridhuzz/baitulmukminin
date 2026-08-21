<?php

namespace App\Http\Controllers;

use App\Models\Entitas;
use App\Support\EntitasAktif;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EntitasController extends Controller
{
    /** Ganti entitas aktif di panel pengurus (pemilih di topbar). */
    public function ganti(Request $request, string $id): RedirectResponse
    {
        if (! EntitasAktif::bolehSemua()) {
            return redirect()->back();
        }

        if ($id === 'semua') {
            EntitasAktif::set(null);
        } else {
            $entitas = Entitas::aktif()->whereKey($id)->first();
            EntitasAktif::set($entitas?->id);
        }

        return redirect()->to(url()->previous() ?: url('/admin'));
    }
}
