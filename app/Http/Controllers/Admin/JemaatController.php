<?php

namespace App\Http\Controllers\Admin;

use App\Exports\JemaatExport;
use App\Http\Controllers\Controller;
use App\Imports\JemaatImport;
use App\Models\Cabang;
use App\Models\Jemaat;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class JemaatController extends Controller
{
    // Halaman Master Jemaat
    public function index(Request $request)
    {
        $query = Jemaat::with('cabang')->where('isDelete', 0);

        // Filter Search (Nama, Alias, Keluarga, Alamat)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_asli', 'like', "%{$search}%")
                    ->orWhere('alias_1', 'like', "%{$search}%")
                    ->orWhere('alias_2', 'like', "%{$search}%")
                    ->orWhere('keluarga', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        // Filter Status (Anggota / Tamu)
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        // Ambil nilai per_page dari request, default ke 50
        $perPage = $request->input('per_page', '50');

        // Urutkan berdasarkan nama asli
        $query->orderBy('nama_asli', 'asc');

        // Jika opsi 'all' dipilih, tampilkan semua tanpa pagination
        if ($perPage === 'all') {
            $jemaats = $query->get();
        } else {
            // Mempertahankan query string untuk pagination
            $jemaats = $query->paginate((int) $perPage)->appends($request->query());
        }

        // Ambil Data Cabang & Unique List Keluarga untuk Dropdown & Datalist Modal
        $cabangs = Cabang::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_cabang', 'asc')->get();
        $keluargas = Jemaat::where('isDelete', 0)->whereNotNull('keluarga')->where('keluarga', '!=', '')->distinct()->pluck('keluarga');

        return view('admin.masterdata.jemaatCMS', compact('jemaats', 'cabangs', 'keluargas'));
    }

    // Simpan Data Jemaat Baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_asli' => 'required|string|max:255',
            'status' => 'nullable|string|max:50',
            'fokus_ibadah' => 'nullable|array',
            'alias_1' => 'nullable|string|max:255',
            'alias_2' => 'nullable|string|max:255',
            'keluarga' => 'nullable|string|max:255',
            'cabang_id' => 'nullable|exists:cabangs,id',
            'alamat' => 'nullable|string',
            'tempat_lahir' => 'nullable|string|max:255',
            'tgl_lahir' => 'nullable|date',
            'telepon' => 'nullable|string|max:50',
        ]);

        Jemaat::create([
            'nama_asli' => $request->nama_asli,
            'status' => $request->status ?? 'Anggota',
            'fokus_ibadah' => $request->fokus_ibadah, // Otomatis dicast ke JSON oleh Model
            'alias_1' => $request->alias_1,
            'alias_2' => $request->alias_2,
            'keluarga' => $request->keluarga,
            'cabang_id' => $request->cabang_id,
            'alamat' => $request->alamat,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => $request->tgl_lahir ?: $request->tanggal_lahir,
            'telepon' => $request->telepon,
            'isActive' => 1,
            'isDelete' => 0,
        ]);

        return redirect()->back()->with('success', 'Data jemaat berhasil ditambahkan!');
    }

    // Update Data Jemaat
    public function update(Request $request, $id)
    {
        $jemaat = Jemaat::where('isDelete', 0)->findOrFail($id);

        $request->validate([
            'nama_asli' => 'required|string|max:255',
            'status' => 'nullable|string|max:50',
            'fokus_ibadah' => 'nullable|array',
            'alias_1' => 'nullable|string|max:255',
            'alias_2' => 'nullable|string|max:255',
            'keluarga' => 'nullable|string|max:255',
            'cabang_id' => 'nullable|exists:cabangs,id',
            'alamat' => 'nullable|string',
            'tempat_lahir' => 'nullable|string|max:255',
            'tgl_lahir' => 'nullable|date',
            'telepon' => 'nullable|string|max:50',
        ]);

        $jemaat->update([
            'nama_asli' => $request->nama_asli,
            'status' => $request->status ?? 'Anggota',
            'fokus_ibadah' => $request->fokus_ibadah, // Menyimpan array/null ke JSON
            'alias_1' => $request->alias_1,
            'alias_2' => $request->alias_2,
            'keluarga' => $request->keluarga,
            'cabang_id' => $request->cabang_id,
            'alamat' => $request->alamat,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => $request->tgl_lahir ?: $request->tanggal_lahir,
            'telepon' => $request->telepon,
        ]);

        return redirect()->back()->with('success', 'Data jemaat berhasil diperbarui!');
    }

    // Soft Delete Data Jemaat
    public function destroy($id)
    {
        $jemaat = Jemaat::where('isDelete', 0)->findOrFail($id);
        $jemaat->update(['isDelete' => 1]);

        return redirect()->back()->with('success', 'Data jemaat berhasil dihapus!');
    }

    // Export Data Jemaat ke File .xlsx
    public function export()
    {
        $fileName = 'daftar-jemaat-'.date('Y-m-d').'.xlsx';

        return Excel::download(new JemaatExport, $fileName);
    }

    // Import Data Jemaat dari File .xlsx / .csv
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new JemaatImport, $request->file('file'));

            return redirect()->back()->with('success', 'Data jemaat berhasil di-import!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses file: '.$e->getMessage());
        }
    }

    // Download Template Excel .xlsx
    public function downloadTemplate()
    {
        return Excel::download(new JemaatExport, 'template-jemaat.xlsx');
    }
}
