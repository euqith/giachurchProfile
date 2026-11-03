@extends('admin.layoutCMS')

@section('content')
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold mb-1.5 border border-amber-200/80">
        <i class="ri-dashboard-line"></i> Ringkasan Sistem
      </div>
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dashboard Administrasi</h1>
      <p class="text-xs text-slate-500 mt-0.5">Selamat datang kembali di panel administrasi pusat gereja.</p>
    </div>
    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-900 text-white text-xs font-semibold shadow-sm">
      <i class="ri-calendar-line text-amber-400"></i>
      <span>{{ date('l, d M Y') }}</span>
    </div>
  </div>

  <div class="grid xl:grid-cols-3 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-sm p-6 flex justify-between items-center border border-slate-200/80 hover:shadow-md transition">
      <div>
        <p class="text-slate-400 font-bold text-xs uppercase tracking-wider mb-1">Total Event Aktif</p>
        <h3 class="text-3xl font-extrabold text-slate-900 mb-0">{{ $totalEvent }}</h3>
      </div>
      <div class="w-14 h-14 bg-amber-50 border border-amber-200/80 rounded-2xl flex items-center justify-center text-amber-700 text-2xl shadow-2xs">
        <i class="ri-calendar-check-line"></i>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 flex justify-between items-center border border-slate-200/80 hover:shadow-md transition">
      <div>
        <p class="text-slate-400 font-bold text-xs uppercase tracking-wider mb-1">Wilayah Terdaftar</p>
        <h3 class="text-3xl font-extrabold text-slate-900 mb-0">{{ $totalCabang }}</h3>
      </div>
      <div class="w-14 h-14 bg-amber-50 border border-amber-200/80 rounded-2xl flex items-center justify-center text-amber-700 text-2xl shadow-2xs">
        <i class="ri-map-pin-2-line"></i>
      </div>
    </div>
  </div>
@endsection
