<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jemaat;
use App\Models\Cabang;
use App\Exports\JemaatExport;
use App\Imports\JemaatImport;
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
            $query->where(function($q) use ($search) {
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

        $jemaats = $query->orderBy('nama_asli', 'asc')->get();

        // Ambil Data Cabang & Unique List Keluarga untuk Dropdown & Datalist Modal
        $cabangs = Cabang::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_cabang', 'asc')->get();
        $keluargas = Jemaat::where('isDelete', 0)->whereNotNull('keluarga')->where('keluarga', '!=', '')->distinct()->pluck('keluarga');

        return view('admin.masterdata.jemaatCMS', compact('jemaats', 'cabangs', 'keluargas'));
    }

    // Export Data Jemaat ke File .xlsx
    public function export()
    {
        $fileName = 'daftar-jemaat-' . date('Y-m-d') . '.xlsx';
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
            return redirect()->back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    // Download Template Excel .xlsx
    public function downloadTemplate()
    {
        return Excel::download(new JemaatExport, 'template-jemaat.xlsx');
    }
}