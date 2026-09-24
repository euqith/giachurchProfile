<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jemaat;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JemaatController extends Controller
{
   public function index(Request $request)
    {
        $query = Jemaat::with('cabang')->where('isDelete', 0);

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_asli', 'like', "%{$search}%")
                  ->orWhere('alias_1', 'like', "%{$search}%")
                  ->orWhere('alias_2', 'like', "%{$search}%")
                  ->orWhere('keluarga', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        // Filter Status
        if ($request->filled('status') && $request->status != 'semua') {
            $query->where('status', $request->status);
        }

        $jemaats = $query->orderBy('id', 'desc')->get();
        $cabangs = Cabang::where('isDelete', 0)->where('isActive', 1)->get();

        // 👈 Ambil daftar nama keluarga unik dari database yang tidak null
        $keluargas = Jemaat::where('isDelete', 0)
                            ->whereNotNull('keluarga')
                            ->where('keluarga', '!=', '')
                            ->distinct()
                            ->pluck('keluarga');

        return view('admin.masterdata.jemaatCMS', compact('jemaats', 'cabangs', 'keluargas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_asli'    => 'required|string|max:255',
            'status'       => 'required|string',
            'cabang_id'    => 'nullable|exists:cabangs,id',
            'telepon'      => 'nullable|string|max:20',
        ]);

        Jemaat::create([
            'nama_asli'    => $request->nama_asli,
            'status'       => $request->status,
            'badge_tag'    => $request->badge_tag,
            'alias_1'      => $request->alias_1,
            'alias_2'      => $request->alias_2,
            'keluarga'     => $request->keluarga,
            'cabang_id'    => $request->cabang_id,
            'alamat'       => $request->alamat,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir'    => $request->tgl_lahir,
            'telepon'      => $request->telepon,
            'createdBy'    => Auth::user()->name ?? 'Admin',
            'isActive'     => 1,
            'isDelete'     => 0,
        ]);

        return redirect()->back()->with('success', 'Data jemaat berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_asli' => 'required|string|max:255',
            'status'    => 'required|string',
        ]);

        $jemaat = Jemaat::findOrFail($id);
        $jemaat->update([
            'nama_asli'    => $request->nama_asli,
            'status'       => $request->status,
            'badge_tag'    => $request->badge_tag,
            'alias_1'      => $request->alias_1,
            'alias_2'      => $request->alias_2,
            'keluarga'     => $request->keluarga,
            'cabang_id'    => $request->cabang_id,
            'alamat'       => $request->alamat,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir'    => $request->tgl_lahir,
            'telepon'      => $request->telepon,
            'updatedBy'    => Auth::user()->name ?? 'Admin',
        ]);

        return redirect()->back()->with('success', 'Data jemaat berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jemaat = Jemaat::findOrFail($id);
        $jemaat->update([
            'isDelete'  => 1,
            'updatedBy' => Auth::user()->name ?? 'Admin',
        ]);

        return redirect()->back()->with('success', 'Data jemaat berhasil dihapus!');
    }
}