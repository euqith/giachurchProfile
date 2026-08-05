<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

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