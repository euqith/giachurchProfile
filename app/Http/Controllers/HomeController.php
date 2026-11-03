<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\ChurchProfile;
use App\Models\Event;
use App\Models\Stream;
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
        // 🏛️ Profil Gereja & Foto Utama Banner 1
        $churchProfile = ChurchProfile::getProfile();

        // 🎯 Banner 2+: Event Ongoing & Future
        $sliderEvents = Event::with('cabang')
            ->where('isDelete', false)
            ->where('isActive', true)
            ->where('waktu_selesai', '>=', Carbon::now())
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        // 📡 Streaming 5 Cabang & Deteksi Siaran Langsung (Live Detection)
        $streams = Stream::with('cabang')
            ->where('isDelete', 0)
            ->where('isActive', 1)
            ->get();

        $activeLiveStreams = $streams->where('is_live', true);

        // ⛪ Jadwal Ibadah Mendatang & Daftar Cabang
        $upcomingSchedules = WorshipSchedule::with(['cabang', 'jenisIbadah', 'sesi'])
            ->where('isDelete', 0)
            ->where('isActive', 1)
            ->where('tanggal', '>=', Carbon::today('Asia/Jakarta')->format('Y-m-d'))
            ->orderBy('tanggal', 'asc')
            ->take(6)
            ->get();

        $cabangs = Cabang::where('isDelete', 0)
            ->where('isActive', 1)
            ->get();

        // 📄 Warta Jemaat Digital Terbaru
        $latestWarta = Warta::where('isDelete', false)
            ->where('isActive', true)
            ->latest('tanggal_rilis')
            ->first();

        return view('homepage.home', compact(
            'churchProfile',
            'sliderEvents',
            'streams',
            'activeLiveStreams',
            'upcomingSchedules',
            'cabangs',
            'latestWarta'
        ));
    }
}
