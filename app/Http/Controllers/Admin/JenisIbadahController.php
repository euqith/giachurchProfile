<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisIbadah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JenisIbadahController extends Controller
{
    public function index()
    {
        $ibadahs = JenisIbadah::where('isDelete', 0)->orderBy('id', 'desc')->get();
        return view('admin.masterdata.ibadahCMS', compact('ibadahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_ibadah' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
        ]);

        JenisIbadah::create([
            'nama_ibadah' => $request->nama_ibadah,
            'deskripsi'   => $request->deskripsi,
            'createdBy'   => Auth::user()->name ?? 'Admin',
            'isActive'    => 1,
            'isDelete'    => 0,
        ]);

        return redirect()->back()->with('success', 'Jenis ibadah berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_ibadah' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'isActive'    => 'required|boolean',
        ]);

        $ibadah = JenisIbadah::findOrFail($id);
        $ibadah->update([
            'nama_ibadah' => $request->nama_ibadah,
            'deskripsi'   => $request->deskripsi,
            'isActive'    => $request->isActive,
            'updatedBy'   => Auth::user()->name ?? 'Admin',
        ]);

        return redirect()->back()->with('success', 'Jenis ibadah berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $ibadah = JenisIbadah::findOrFail($id);
        $ibadah->update([
            'isDelete'  => 1,
            'updatedBy' => Auth::user()->name ?? 'Admin',
        ]);

        return redirect()->back()->with('success', 'Jenis ibadah berhasil dihapus!');
    }
}