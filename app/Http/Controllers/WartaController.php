<?php

namespace App\Http\Controllers;

use App\Models\Warta;
use Illuminate\Http\Request;

class WartaController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Warta Teraktif & Terbaru untuk Edisi Utama
        $wartaUtamaQuery = Warta::with('slides')
                                ->where('isDelete', false)
                                ->where('isActive', true);

        // Jika user mengklik salah satu arsip dari sidebar (?warta_id=x)
        if ($request->has('warta_id') && $request->warta_id != '') {
            $wartaUtama = $wartaUtamaQuery->where('id', $request->warta_id)->first();
        } else {
            // Default: ambil edisi paling baru rilis
            $wartaUtama = $wartaUtamaQuery->orderBy('tanggal_rilis', 'desc')->first();
        }

        // 2. Ambil Semua Daftar Arsip Warta untuk Komponen Sidebar Kanan
        $arsipWartas = Warta::where('isDelete', false)
                            ->where('isActive', true)
                            ->orderBy('tanggal_rilis', 'desc')
                            ->get();

        return view('homepage.warta', compact('wartaUtama', 'arsipWartas'));
    }
}