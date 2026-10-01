<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sesi;
use Illuminate\Http\Request;

class SesiController extends Controller
{
    public function index()
    {
        $sesis = Sesi::where('isDelete', 0)->orderBy('id', 'asc')->get();
        // Mengarahkan view ke folder admin/masterdata/sesiCMS.blade.php
        return view('admin.masterdata.sesiCMS', compact('sesis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sesi' => 'required|string|max:100|unique:sesis,nama_sesi,NULL,id,isDelete,0',
        ]);

        Sesi::create([
            'nama_sesi' => $request->nama_sesi,
            'isActive'  => 1,
            'isDelete'  => 0,
        ]);

        return redirect()->back()->with('success', 'Master Sesi berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $sesi = Sesi::findOrFail($id);

        $request->validate([
            'nama_sesi' => 'required|string|max:100|unique:sesis,nama_sesi,' . $id . ',id,isDelete,0',
        ]);

        $sesi->update([
            'nama_sesi' => $request->nama_sesi,
        ]);

        return redirect()->back()->with('success', 'Master Sesi berhasil diperbarui!');
    }

    public function toggleActive($id)
    {
        $sesi = Sesi::findOrFail($id);
        $sesi->update(['isActive' => !$sesi->isActive]);

        return redirect()->back()->with('success', 'Status keaktifan sesi berhasil diubah!');
    }

    public function destroy($id)
    {
        $sesi = Sesi::findOrFail($id);
        $sesi->update(['isDelete' => 1]);

        return redirect()->back()->with('success', 'Master Sesi berhasil dihapus!');
    }
}