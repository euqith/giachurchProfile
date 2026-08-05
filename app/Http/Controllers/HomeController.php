<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        // Lempar data variabel $sliderEvents ke dalam view folder homepage.home
        return view('homepage.home', compact('sliderEvents'));
    }
}