<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CabangController extends Controller
{
    public function index()
    {
        $cabangs = Cabang::where('isDelete', 0)->orderBy('id', 'desc')->get();
        
        // 📁 Jalur view diperbarui ke folder admin/masterdata
        return view('admin.masterdata.cabangCMS', compact('cabangs'));
    }

   public function store(Request $request)
    {
        $request->validate([
            'nama_cabang' => 'required|string|max:255',
            'lokasi'      => 'nullable|string|max:255',
        ]);

        Cabang::create([
            'nama_cabang' => $request->nama_cabang,
            'lokasi'      => $request->lokasi,
            'createdBy'   => Auth::user()->name ?? 'Admin',
            'isActive'    => 1,
            'isDelete'    => 0,
        ]);

        return redirect()->back()->with('success', 'Data cabang berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_cabang' => 'required|string|max:255',
            'lokasi'      => 'nullable|string|max:255',
            'isActive'    => 'required|boolean',
        ]);

        $cabang = Cabang::findOrFail($id);
        $cabang->update([
            'nama_cabang' => $request->nama_cabang,
            'lokasi'      => $request->lokasi,
            'isActive'    => $request->isActive,
            'updatedBy'   => Auth::user()->name ?? 'Admin',
        ]);

        return redirect()->back()->with('success', 'Data cabang berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $cabang = Cabang::findOrFail($id);
        $cabang->update([
            'isDelete'  => 1,
            'updatedBy' => Auth::user()->name ?? 'Admin',
        ]);

        return redirect()->back()->with('success', 'Data cabang berhasil dihapus!');
    }
}