@extends('admin.layoutCMS')

@section('content')
  <div class="flex justify-between items-center mb-6">
    <div>
      <h4 class="text-slate-900 text-lg font-bold mb-1">Dashboard</h4>
      <p class="text-slate-400 text-sm">Selamat datang kembali di panel administrasi pusat gereja.</p>
    </div>
    <div class="bg-slate-800 text-white font-medium text-xs px-3 py-1.5 rounded-full shadow">
      📅 {{ date('l, d M Y') }}
    </div>
  </div>

  <div class="grid xl:grid-cols-3 md:grid-cols-2 gap-6 mb-6">
    <div
      class="bg-white rounded-lg shadow p-6 flex justify-between items-center border border-gray-100">
      <div>
        <p class="text-gray-400 font-semibold text-xs uppercase tracking-wider mb-1">Total Event Aktif</p>
        <h3 class="text-2xl font-bold text-gray-800 mb-0">{{ $totalEvent }}</h3>
      </div>
      <div class="w-12 h-12 bg-warning bg-opacity-10 rounded-full flex items-center justify-center text-warning text-xl">
        <i class="ri-calendar-check-line"></i>
      </div>
    </div>

    <div
      class="bg-white rounded-lg shadow p-6 flex justify-between items-center border border-gray-100">
      <div>
        <p class="text-gray-400 font-semibold text-xs uppercase tracking-wider mb-1">Cabang Terdaftar</p>
        <h3 class="text-2xl font-bold text-gray-800 mb-0">{{ $totalCabang }}</h3>
      </div>
      <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-full flex items-center justify-center text-primary text-xl">
        <i class="ri-git-branches-line"></i>
      </div>
    </div>
  </div>
@endsection
