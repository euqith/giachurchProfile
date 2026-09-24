@extends('admin.layoutCMS')

@section('content')
  <div class="space-y-6">

    <!-- 1. CARD SESI IBADAH (1 BARIS KESAMPING) -->
    <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-sm">
      <div class="flex items-center gap-2 mb-4">
        <i class="ri-calendar-event-line text-primary text-lg"></i>
        <h2 class="text-base font-bold text-slate-800">Sesi Ibadah</h2>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Tanggal -->
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal</label>
          <input type="date" id="session_date" value="{{ date('Y-m-d') }}"
            class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-700 outline-none focus:border-primary transition bg-white">
        </div>

        <!-- Jenis Ibadah -->
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jenis Ibadah</label>
          <select name="jenis_ibadah_id" required
            class="w-full text-sm border border-slate-300 rounded-lg px-3.5 py-2 text-slate-800 outline-none focus:border-primary bg-white">
            <option value="" disabled selected>-- Pilih Jenis Ibadah --</option>
            @foreach ($jenisIbadahs ?? [] as $ibadah)
              <option value="{{ $ibadah->id }}">{{ $ibadah->nama_ibadah }}</option>
            @endforeach
          </select>
        </div>

        <!-- Lokasi (Cabang) -->
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Lokasi</label>
          <select name="cabang_id" required
            class="w-full text-sm border border-slate-300 rounded-lg px-3.5 py-2 text-slate-800 outline-none focus:border-primary bg-white">
            <option value="" disabled selected>-- Pilih Lokasi Cabang --</option>
            @foreach ($cabangs ?? [] as $cabang)
              <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
            @endforeach
          </select>
        </div>

        <!-- Nama Petugas -->
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Petugas</label>
          <input type="text" value="{{ Auth::user()->name }}" readonly
            class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-700 bg-slate-50 outline-none cursor-not-allowed">
        </div>
      </div>
    </div>

    <!-- 2. CARD PENCATATAN KEHADIRAN -->
    <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-sm space-y-5">
      <!-- Header & Counter -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 border-b border-slate-100 pb-4">
        <div>
          <div class="flex items-center gap-2">
            <i class="ri-user-add-line text-primary text-lg"></i>
            <h2 class="text-base font-bold text-slate-800">Pencatatan Kehadiran</h2>
          </div>
          <p class="text-xs text-slate-400 mt-1">
            Ketik nama / alias &rarr; Enter &rarr; ketik lagi. Panah &uarr;&darr; untuk memilih saran lain. Nama yang
            tidak dikenal bisa langsung ditambah sebagai tamu.
          </p>
        </div>

        <!-- Standalone Counter Badge -->
        <div class="text-right">
          <span class="text-3xl font-black text-slate-800 tracking-tight" id="total_count">0</span>
          <p class="text-[11px] font-semibold text-slate-400" id="detail_count">0 anggota + 0 tamu</p>
        </div>
      </div>

      <!-- Toggle Mode Sentuh / Tablet -->
      <div class="flex items-center gap-3">
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" id="touch_mode_toggle" class="sr-only peer">
          <div
            class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary">
          </div>
        </label>
        <span class="text-xs font-medium text-slate-600 flex items-center gap-1.5">
          <i class="ri-keyboard-line text-slate-400"></i> Mode Sentuh (tablet): tombol besar per wilayah
        </span>
      </div>

      <!-- Input Search Bar -->
      <div class="relative">
        <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base"></i>
        <input type="text" id="member_search" placeholder="Ketik nama atau alias, lalu tekan Enter..."
          class="w-full text-sm border border-slate-300 rounded-lg pl-10 pr-4 py-2.5 text-slate-800 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
      </div>

      <!-- Empty State Container (Tempat Hasil Input & Tabel Kehadiran) -->
      <div id="attendance_list_container" class="border-2 border-dashed border-slate-200 rounded-xl p-12 text-center">
        <div class="flex flex-col items-center justify-center space-y-2">
          <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-300">
            <i class="ri-user-shared-line text-2xl"></i>
          </div>
          <p class="text-xs font-medium text-slate-400">
            Belum ada yang dicatat. Ketik nama di kolom pencarian lalu tekan Enter.
          </p>
        </div>
      </div>

      <!-- Footer Action & Live Session Tag -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
        <div class="flex items-center gap-2.5 w-full sm:w-auto">
          <button type="button" id="btn_save"
            class="flex-1 sm:flex-none flex items-center justify-center gap-2 bg-primary/80 hover:bg-primary text-white text-xs font-semibold px-4 py-2.5 rounded-lg shadow-sm transition">
            <i class="ri-save-line text-sm"></i> Simpan Kehadiran
          </button>
          <button type="button" id="btn_clear"
            class="flex-1 sm:flex-none flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-4 py-2.5 rounded-lg border border-slate-200/60 transition">
            <i class="ri-delete-bin-line text-sm"></i> Bersihkan Daftar
          </button>
        </div>

        <!-- Dynamic Session Tag -->
        <div class="text-xs text-slate-400 font-medium text-center sm:text-right" id="live_session_info">
          {{ \Carbon\Carbon::now()->isoFormat('D MMM YYYY') }} &middot; Ibadah Umum @ Darmo Pagi
        </div>
      </div>
    </div>

  </div>
@endsection
