<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Warta;
use App\Models\WorshipSchedule;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Menampilkan Halaman Depan (Homepage)
     */
    public function index()
    {
        // 🎯 TARIK DATA: Ambil maksimal 3 event yang belum selesai/expired untuk slider banner
        $sliderEvents = Event::with('cabang')
            ->where('isDelete', false)
            ->where('isActive', true)
            ->where('waktu_selesai', '>=', Carbon::now())
            ->orderBy('waktu_mulai', 'asc')
            ->take(3)
            ->get();

        // ⛪ TARIK DATA: Jadwal Ibadah Mendatang Terdekat untuk Quick Access & Liturgi
        $upcomingSchedule = WorshipSchedule::with(['cabang', 'jenisIbadah', 'scheduleSongs.song'])
            ->where('isDelete', 0)
            ->where('isActive', 1)
            ->where('tanggal', '>=', Carbon::today('Asia/Jakarta')->format('Y-m-d'))
            ->orderBy('tanggal', 'asc')
            ->first();

        // 📄 TARIK DATA: Warta Jemaat Digital Terbaru
        $latestWarta = Warta::where('isDelete', false)
            ->where('isActive', true)
            ->latest('tanggal_rilis')
            ->first();

        return view('homepage.home', compact('sliderEvents', 'upcomingSchedule', 'latestWarta'));
    }
}
