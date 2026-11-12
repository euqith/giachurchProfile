<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Cabang;
use App\Models\ChurchProfile;
use App\Models\Event;
use App\Models\Stream;
use App\Models\Warta;
use App\Models\WorshipSchedule;
use App\Services\YouTubeStreamDetector;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Throwable;

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

        // 📡 Auto-detect real-time live streaming berkala
        if (! Cache::has('streams_auto_checked_recently')) {
            try {
                $checkResult = app(YouTubeStreamDetector::class)->checkAllStreams();
                // Jika sedang ada yang live, periksa lebih cepat (60 detik) agar saat end-stream langsung offline.
                // Jika semua offline, cache 3 menit agar hemat kuota & performa kencang.
                $cacheTtl = ($checkResult['live_count'] > 0) ? now()->addSeconds(60) : now()->addMinutes(3);
                Cache::put('streams_auto_checked_recently', true, $cacheTtl);
            } catch (Throwable $e) {
                // Jangan gagalkan load homepage jika koneksi eksternal timeout
            }
        }

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

        // 📰 Artikel & Renungan Rohani Terbaru
        $latestArticles = Article::with(['author', 'likes', 'comments'])
            ->where('isDelete', false)
            ->where('isActive', true)
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return view('homepage.home', compact(
            'churchProfile',
            'sliderEvents',
            'streams',
            'activeLiveStreams',
            'upcomingSchedules',
            'cabangs',
            'latestWarta',
            'latestArticles'
        ));
    }
}
