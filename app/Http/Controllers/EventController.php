<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;  
use App\Models\Cabang; 
use Carbon\Carbon;     

class EventController extends Controller
{
    /**
     * Menampilkan Daftar Event (Hanya Mendatang/Ongoing & Maksimal H-7 Selesai)
     */
    public function index(Request $request)
    {
        // 🎯 1. BATAS ARSIP OTOMATIS: Ambil waktu sekarang, mundurkan 7 hari ke belakang (H-7)
        $batasTanggal = Carbon::now()->subWeek();

        // 🎯 2. QUERY UTAMA: Filter hanya event yang waktu_selesai-nya belum lewat dari batasTanggal
        $query = Event::with('cabang')
                      ->where('waktu_selesai', '>=', $batasTanggal) // Menggunakan waktu_selesai agar event di hari H tidak langsung hilang
                      ->where('isDelete', false)
                      ->where('isActive', true);

        // B. LOGIKA FILTER CABANG (Jika jemaat memilih cabang tertentu)
        if ($request->has('cabang_id') && $request->cabang_id != '' && $request->cabang_id != 'Semua') {
            $query->where('cabang_id', $request->cabang_id);
        }

        // C. LOGIKA SORTING DINAMIS (Default: Start Date Descending / Paling Baru di Atas)
        $sortBy = $request->get('sort_by', 'date_desc'); 

        switch ($sortBy) {
            case 'date_asc':
                $query->orderBy('waktu_mulai', 'asc'); // Terlama/Terdekat -> Terbaru
                break;
            case 'name_asc':
                $query->orderBy('nama_acara', 'asc'); // Alfabet A-Z
                break;
            case 'name_desc':
                $query->orderBy('nama_acara', 'desc'); // Alfabet Z-A
                break;
            case 'date_desc':
            default:
                $query->orderBy('waktu_mulai', 'desc'); // 👈 Default Utama: Start Date Descending
                break;
        }

        // D. Eksekusi pagination (6 Item per halaman) sambil mengunci parameter query string di URL
        $events = $query->paginate(6)->withQueryString();

        // E. Tarik data cabang untuk dropdown filter jemaat
        $cabangs = Cabang::where('isDelete', false)
                         ->where('isActive', true)
                         ->get();

        return view('homepage.event', compact('events', 'cabangs', 'sortBy'));
    }
}