<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung total events
        $totalEvent = Event::where('isDelete', false)->count();
        $totalCabang = \DB::table('cabangs')->where('isDelete', false)->count();

        // 📢 Arahkan ke file dashboardCMS
        return view('admin.dashboardCMS', compact('totalEvent', 'totalCabang'));
    }
}
