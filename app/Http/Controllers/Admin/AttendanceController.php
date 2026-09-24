<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\JenisIbadah;
use Illuminate\Http\Request;
class AttendanceController extends Controller
{
   public function input()
    {
        // Ambil data cabang dan jenis ibadah yang aktif & tidak dihapus
        $cabangs = Cabang::where('isDelete', 0)->where('isActive', 1)->get();
        $jenisIbadahs = JenisIbadah::where('isDelete', 0)->where('isActive', 1)->get();

        // Pastikan nama view sesuai lokasi filemu (contoh: admin.attendance.inputCMS)
        return view('admin.attendance.inputCMS', compact('cabangs', 'jenisIbadahs'));
    }

    public function history()
    {
        return view('admin.attendance.historyCMS');
    }

    public function report()
    {
        return view('admin.attendance.reportCMS');
    }
}