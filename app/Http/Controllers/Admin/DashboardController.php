<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\Event;
use Throwable;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $totalEvent = Event::where('isDelete', 0)->count();
        } catch (Throwable $e) {
            $totalEvent = 0;
        }

        try {
            $totalCabang = Cabang::where('isDelete', 0)->count();
        } catch (Throwable $e) {
            $totalCabang = 0;
        }

        // 📢 Arahkan ke file dashboardCMS
        return view('admin.dashboardCMS', compact('totalEvent', 'totalCabang'));
    }
}
