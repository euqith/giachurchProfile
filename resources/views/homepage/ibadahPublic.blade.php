<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jadwal Ibadah & Liturgi Pelayanan - Gereja Isa Almasih Surabaya</title>
  <link rel="shortcut icon" href="{{ asset('homepage/assets/images/favicon.png') }}">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans min-h-screen flex flex-col justify-between selection:bg-blue-600 selection:text-white"
  x-data="publicScheduleApp({
    initialTab: '{{ $tab }}',
    initialJenis: '{{ $jenisFilter }}',
    initialLokasi: '{{ $lokasiFilter }}',
    initialSelectedId: '{{ $selectedSchedule ? $selectedSchedule->id : ($selectedId ?? '') }}',
    schedules: @js($schedules),
    countMendatang: {{ $countMendatang }},
    countArsip: {{ $countArsip }}
  })">

  <!-- ========================================== -->
  <!-- 1. TOP HEADER (MATCHING IMAGE-15 & IMAGE-17) -->
  <!-- ========================================== -->
  <header class="bg-white border-b border-slate-200/90 sticky top-0 z-40 shadow-xs">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
      
      <!-- Brand & Back Button -->
      <div class="flex items-center gap-3">
        <!-- Back Arrow Button (Visible in Detail View) -->
        <button type="button" x-show="viewMode === 'detail'" @click="closeDetail()"
          title="Kembali ke Daftar Jadwal"
          class="w-9 h-9 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 flex items-center justify-center transition">
          <i class="ri-arrow-left-line text-lg"></i>
        </button>

        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
          <!-- Church Blue Square Icon -->
          <div class="w-10 h-10 rounded-xl bg-blue-700 text-white flex items-center justify-center shadow-md shadow-blue-600/25 group-hover:bg-blue-800 transition">
            <i class="ri-government-line text-xl"></i>
          </div>
          <div>
            <span class="font-extrabold text-slate-900 text-sm sm:text-base tracking-tight block leading-tight">
              Gereja Isa Almasih Surabaya
            </span>
            <span class="text-[11px] text-slate-500 font-medium tracking-wide">
              Persiapan Ibadah & Liturgi
            </span>
          </div>
        </a>
      </div>

      <!-- Right Link: "Kembali ke aplikasi" (Image-15 & Image-17) -->
      <div class="flex items-center gap-4">
        <a href="{{ url('/') }}"
          class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 hover:text-blue-600 px-3 py-2 rounded-xl hover:bg-slate-100 transition">
          <i class="ri-layout-grid-line text-base text-blue-600"></i>
          <span>Kembali ke aplikasi</span>
        </a>
        
        @auth
          <a href="{{ route('admin.dashboard') }}"
            class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold transition">
            <i class="ri-shield-user-line"></i> CMS Panel
          </a>
        @endauth
      </div>
    </div>
  </header>

  <!-- ========================================== -->
  <!-- 2. MAIN CONTENT WRAPPER -->
  <!-- ========================================== -->
  <main class="flex-1 max-w-6xl mx-auto w-full px-4 sm:px-6 py-6 sm:py-8">

    <!-- ======================================================== -->
    <!-- VIEW A: LIST VIEW (IMAGE-15 & IMAGE-16)                  -->
    <!-- ======================================================== -->
    <div x-show="viewMode === 'list'" class="space-y-6">
      
      <!-- Page Title & Subtitle (Image-15) -->
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Jadwal Ibadah</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Tema, ayat, petugas pelayanan, dan daftar lagu untuk setiap ibadah</p>
      </div>

      <!-- Navigation Bar: Tabs & Filter Dropdowns (Image-15) -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        
        <!-- Left Tabs: Mendatang [X] | Arsip [Y] -->
        <div class="flex items-center gap-2">
          <button type="button" @click="setTab('mendatang')"
            :class="activeTab === 'mendatang' 
              ? 'bg-slate-900 text-white font-bold shadow-sm' 
              : 'bg-white text-slate-600 hover:bg-slate-100 font-medium border border-slate-200'"
            class="px-4 py-2 rounded-xl text-xs sm:text-sm transition flex items-center gap-2">
            <span>Mendatang</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold"
              :class="activeTab === 'mendatang' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'"
              x-text="countMendatang"></span>
          </button>

          <button type="button" @click="setTab('arsip')"
            :class="activeTab === 'arsip' 
              ? 'bg-slate-900 text-white font-bold shadow-sm' 
              : 'bg-white text-slate-600 hover:bg-slate-100 font-medium border border-slate-200'"
            class="px-4 py-2 rounded-xl text-xs sm:text-sm transition flex items-center gap-2">
            <span>Arsip</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold"
              :class="activeTab === 'arsip' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'"
              x-text="countArsip"></span>
          </button>
        </div>

        <!-- Right Dropdowns: Semua Jenis & Semua Lokasi (Image-15) -->
        <div class="flex items-center gap-2.5">
          <!-- Filter Jenis Ibadah -->
          <div class="relative">
            <select x-model="filterJenis" @change="applyFilters()"
              class="appearance-none bg-white border border-slate-200 rounded-xl px-3.5 py-2 pr-8 text-xs font-semibold text-slate-700 hover:border-slate-300 focus:outline-none focus:border-blue-500 shadow-2xs">
              <option value="">Semua Jenis</option>
              @foreach($jenisIbadahs as $j)
                <option value="{{ $j->nama_ibadah }}" {{ $jenisFilter === $j->nama_ibadah ? 'selected' : '' }}>
                  {{ $j->nama_ibadah }}
                </option>
              @endforeach
            </select>
            <i class="ri-arrow-down-s-line absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
          </div>

          <!-- Filter Cabang / Lokasi -->
          <div class="relative">
            <select x-model="filterLokasi" @change="applyFilters()"
              class="appearance-none bg-white border border-slate-200 rounded-xl px-3.5 py-2 pr-8 text-xs font-semibold text-slate-700 hover:border-slate-300 focus:outline-none focus:border-blue-500 shadow-2xs">
              <option value="">Semua Lokasi</option>
              @foreach($cabangs as $c)
                <option value="{{ $c->nama_cabang }}" {{ $lokasiFilter === $c->nama_cabang ? 'selected' : '' }}>
                  {{ $c->nama_cabang }}
                </option>
              @endforeach
            </select>
            <i class="ri-arrow-down-s-line absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
          </div>
        </div>

      </div>

      <!-- SCHEDULE CARDS GRID (IMAGE-16) -->
      <template x-if="filteredSchedules.length > 0">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
          <template x-for="item in filteredSchedules" :key="item.id">
            <div @click="openSchedule(item)"
              class="bg-white rounded-2xl border border-slate-200/90 hover:border-blue-500/70 p-5 sm:p-6 shadow-xs hover:shadow-md transition-all cursor-pointer group flex flex-col justify-between gap-4">
              
              <!-- Card Top: Date & Badges -->
              <div class="space-y-3">
                <div class="flex items-center justify-between gap-2">
                  <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100 flex items-center gap-1">
                      <i class="ri-calendar-line"></i>
                      <span x-text="item.nama_ibadah || 'Ibadah'"></span>
                    </span>
                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 text-slate-700 flex items-center gap-1">
                      <i class="ri-map-pin-2-line"></i>
                      <span x-text="item.nama_cabang || '-'"></span>
                    </span>
                  </div>

                  <!-- Date Text -->
                  <span class="text-xs font-bold text-slate-500" x-text="formatDateShort(item.tanggal)"></span>
                </div>

                <!-- Topic & Scripture (Image-16: Topic in Bold Italic) -->
                <div>
                  <h3 class="text-lg sm:text-xl font-bold text-slate-900 group-hover:text-blue-600 transition tracking-tight"
                    x-text="item.tema ? `\"${item.tema}\"` : (item.nama_ibadah || 'Jadwal Ibadah')"></h3>
                  <p class="text-xs font-medium text-slate-500 mt-1 flex items-center gap-1.5" x-show="item.ayat">
                    <i class="ri-book-open-line text-blue-600"></i>
                    <span x-text="item.ayat"></span>
                  </p>
                </div>
              </div>

              <!-- Card Bottom: Servants, Songs Count & Chevron (Image-16) -->
              <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                <div class="space-y-1">
                  <!-- Preacher & WL -->
                  <div class="flex items-center gap-3 text-[11px]">
                    <span class="flex items-center gap-1 text-slate-700">
                      <span class="text-slate-400">Pkh:</span>
                      <strong class="font-bold text-slate-800" x-text="item.pengkhotbah || '-'"></strong>
                    </span>
                    <span class="text-slate-300">|</span>
                    <span class="flex items-center gap-1 text-slate-700">
                      <span class="text-slate-400">WL:</span>
                      <strong class="font-bold text-slate-800" x-text="item.pemimpin_pujian || '-'"></strong>
                    </span>
                  </div>

                  <!-- Song count badge -->
                  <div class="flex items-center gap-1.5 text-blue-600 font-bold text-[11px]">
                    <i class="ri-music-2-line"></i>
                    <span x-text="`${item.schedule_songs ? item.schedule_songs.length : 0} lagu liturgi`"></span>
                  </div>
                </div>

                <!-- Chevron arrow -->
                <div class="w-8 h-8 rounded-full bg-slate-50 group-hover:bg-blue-600 group-hover:text-white text-slate-400 flex items-center justify-center transition shadow-2xs">
                  <i class="ri-arrow-right-s-line text-lg font-bold"></i>
                </div>
              </div>

            </div>
          </template>
        </div>
      </template>

      <!-- EMPTY STATE (IMAGE-15) -->
      <template x-if="filteredSchedules.length === 0">
        <div class="bg-white rounded-3xl border border-slate-200/90 p-12 sm:p-16 text-center space-y-3 shadow-2xs">
          <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-3xl">
            <i class="ri-calendar-2-line"></i>
          </div>
          <h3 class="text-base font-bold text-slate-800"
            x-text="activeTab === 'mendatang' ? 'Belum ada jadwal ibadah mendatang.' : 'Belum ada arsip jadwal ibadah.'"></h3>
          <p class="text-xs text-slate-500 max-w-sm mx-auto">
            Jadwal dan liturgi ibadah untuk cabang ini akan segera diperbarui oleh tim pelayanan dan majelis gereja.
          </p>
        </div>
      </template>

    </div>

    <!-- ======================================================== -->
    <!-- VIEW B: DETAIL VIEW (IMAGE-17 & IMAGE-18)                -->
    <!-- ======================================================== -->
    <div x-show="viewMode === 'detail' && activeSchedule" class="space-y-6">

      <!-- SUB-BAR: MODE BACA & SHARE BUTTONS (IMAGE-17) -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 sm:px-5 rounded-2xl border border-slate-200/90 shadow-2xs">
        
        <!-- Left: Toggle / Indicator Mode Baca -->
        <div class="flex items-center gap-3">
          <div class="inline-flex rounded-xl bg-slate-100 p-0.5 border border-slate-200">
            <span class="px-3 py-1 rounded-lg bg-white text-slate-900 text-xs font-bold shadow-xs inline-flex items-center gap-1.5">
              <i class="ri-eye-line text-blue-600"></i> Baca
            </span>
            @auth
              <a :href="`/admin/attendance/persiapan/schedule/${activeSchedule?.id}/edit`"
                class="px-3 py-1 rounded-lg text-slate-600 hover:text-slate-900 text-xs font-semibold inline-flex items-center gap-1.5 transition">
                <i class="ri-edit-line"></i> Edit
              </a>
            @endauth
          </div>
          <span class="text-xs text-slate-500 hidden md:inline">
            Mode Baca: tampilan persis seperti yang dilihat jemaat.
          </span>
        </div>

        <!-- Right Buttons: Bagikan WA & Salin Tautan (Image-17) -->
        <div class="flex items-center gap-2">
          <!-- Bagikan WA -->
          <button type="button" @click="shareWhatsApp()"
            class="px-3.5 py-2 rounded-xl border border-emerald-500/80 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold inline-flex items-center gap-1.5 transition cursor-pointer">
            <i class="ri-whatsapp-line text-base text-emerald-600"></i>
            <span>Bagikan WA</span>
          </button>

          <!-- Salin Tautan -->
          <button type="button" @click="copyLink()"
            class="px-3.5 py-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold inline-flex items-center gap-1.5 transition cursor-pointer">
            <i class="ri-file-copy-line text-base text-slate-600"></i>
            <span>Salin tautan</span>
          </button>
        </div>

      </div>

      <!-- HERO BANNER (IMAGE-17) -->
      <div class="bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-blue-950/20 relative overflow-hidden space-y-4">
        
        <!-- Badges: Jenis Ibadah & Cabang/Lokasi -->
        <div class="flex items-center gap-2 flex-wrap relative z-10">
          <span class="px-3 py-1 rounded-lg text-xs font-bold bg-white/10 text-blue-200 border border-white/10 flex items-center gap-1.5">
            <i class="ri-calendar-event-line"></i>
            <span x-text="activeSchedule?.nama_ibadah || 'Ibadah Umum'"></span>
          </span>
          <span class="px-3 py-1 rounded-lg text-xs font-bold bg-white/10 text-blue-200 border border-white/10 flex items-center gap-1.5">
            <i class="ri-map-pin-2-line"></i>
            <span x-text="activeSchedule?.nama_cabang || '-'"></span>
          </span>
          <template x-if="activeSchedule?.nama_sesi">
            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-blue-500/20 text-blue-300 border border-blue-400/20 flex items-center gap-1.5">
              <i class="ri-time-line"></i> Sesi: <span x-text="activeSchedule?.nama_sesi"></span>
            </span>
          </template>
        </div>

        <!-- Date Human Indonesian -->
        <div class="text-xs sm:text-sm font-semibold text-blue-200/90 relative z-10"
          x-text="formatDateFull(activeSchedule?.tanggal)"></div>

        <!-- Big Bold Topic -->
        <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white relative z-10 leading-tight"
          x-text="activeSchedule?.tema || activeSchedule?.nama_ibadah || 'Ibadah Raya'"></h2>

        <!-- Scripture Reference -->
        <div class="flex items-center gap-2 text-sm sm:text-base font-semibold text-blue-300 relative z-10" x-show="activeSchedule?.ayat">
          <i class="ri-book-read-line text-blue-400"></i>
          <span x-text="activeSchedule?.ayat"></span>
        </div>

        <!-- Subtle backdrop glow -->
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
      </div>

      <!-- CARD 1: PETUGAS PELAYANAN (IMAGE-17) -->
      <div class="bg-white rounded-3xl border border-slate-200/90 p-5 sm:p-7 shadow-xs space-y-5">
        
        <!-- Header -->
        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
          <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-base">
            <i class="ri-group-line"></i>
          </div>
          <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Petugas Pelayanan</h3>
        </div>

        <!-- 2-Column Grid of Roles (Image-17) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
          
          <!-- Box: Pengkhotbah -->
          <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 space-y-1">
            <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Pengkhotbah</span>
            <div class="text-sm font-bold text-slate-900" x-text="activeSchedule?.pengkhotbah || 'Belum ditentukan'"></div>
          </div>

          <!-- Box: Pemimpin Pujian -->
          <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 space-y-1">
            <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Pemimpin Pujian</span>
            <div class="text-sm font-bold text-slate-900" x-text="activeSchedule?.pemimpin_pujian || 'Belum ditentukan'"></div>
          </div>

          <!-- Box: Pemusik (Full width across 2 cols if on desktop) -->
          <div class="md:col-span-2 p-4 rounded-2xl border border-slate-100 bg-slate-50/50 space-y-2">
            <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider"
              x-text="`Pemusik (${getPemusikList().length})`"></span>
            
            <div class="flex flex-wrap gap-2">
              <template x-for="(m, mIdx) in getPemusikList()" :key="mIdx">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-800 shadow-2xs">
                  <span class="font-bold text-blue-700" x-text="m.instrumen ? `${m.instrumen}:` : ''"></span>
                  <span class="font-semibold" x-text="m.nama"></span>
                </span>
              </template>
              <template x-if="getPemusikList().length === 0">
                <span class="text-xs text-slate-400 italic">Belum ditentukan</span>
              </template>
            </div>
          </div>

          <!-- Box: Penerima Tamu -->
          <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 space-y-2">
            <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider"
              x-text="`Penerima Tamu (${getPenerimaTamuList().length})`"></span>
            
            <div class="flex flex-wrap gap-2">
              <template x-for="(t, tIdx) in getPenerimaTamuList()" :key="tIdx">
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-800 shadow-2xs"
                  x-text="t"></span>
              </template>
              <template x-if="getPenerimaTamuList().length === 0">
                <span class="text-xs text-slate-400 italic">Belum ditentukan</span>
              </template>
            </div>
          </div>

          <!-- Box: Soundman -->
          <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 space-y-2">
            <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider"
              x-text="`Soundman (${getSoundmanList().length})`"></span>
            
            <div class="flex flex-wrap gap-2">
              <template x-for="(s, sIdx) in getSoundmanList()" :key="sIdx">
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-800 shadow-2xs"
                  x-text="s"></span>
              </template>
              <template x-if="getSoundmanList().length === 0">
                <span class="text-xs text-slate-400 italic">Belum ditentukan</span>
              </template>
            </div>
          </div>

          <!-- Box: Petugas Kolekte -->
          <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 space-y-2">
            <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider"
              x-text="`Petugas Kolekte (${getKolekteList().length})`"></span>
            
            <div class="flex flex-wrap gap-2">
              <template x-for="(k, kIdx) in getKolekteList()" :key="kIdx">
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-800 shadow-2xs"
                  x-text="k"></span>
              </template>
              <template x-if="getKolekteList().length === 0">
                <span class="text-xs text-slate-400 italic">Belum ditentukan</span>
              </template>
            </div>
          </div>

          <!-- Box: Petugas Perjamuan Kudus -->
          <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 space-y-1">
            <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Petugas Perjamuan Kudus</span>
            <div class="text-xs font-semibold"
              :class="getPerjamuanText() ? 'text-slate-900' : 'text-slate-400 italic'"
              x-text="getPerjamuanText() || 'Belum ditentukan'"></div>
          </div>

          <!-- Box: Majelis Bertugas -->
          <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 space-y-2">
            <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider"
              x-text="`Majelis Bertugas (${getMajelisList().length})`"></span>
            
            <div class="flex flex-wrap gap-2">
              <template x-for="(m, mIdx) in getMajelisList()" :key="mIdx">
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-800 shadow-2xs"
                  x-text="m"></span>
              </template>
              <template x-if="getMajelisList().length === 0">
                <span class="text-xs text-slate-400 italic">Belum ditentukan</span>
              </template>
            </div>
          </div>

          <!-- Box: Petugas Multimedia -->
          <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 space-y-2">
            <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider"
              x-text="`Petugas Multimedia (${getMultimediaList().length})`"></span>
            
            <div class="flex flex-wrap gap-2">
              <template x-for="(mm, mmIdx) in getMultimediaList()" :key="mmIdx">
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-800 shadow-2xs"
                  x-text="mm"></span>
              </template>
              <template x-if="getMultimediaList().length === 0">
                <span class="text-xs text-slate-400 italic">Belum ditentukan</span>
              </template>
            </div>
          </div>

        </div>

      </div>

      <!-- CARD 2: PERSIAPAN PUJIAN & MUSIK (IMAGE-18) -->
      <div class="bg-white rounded-3xl border border-slate-200/90 p-5 sm:p-7 shadow-xs space-y-5">
        
        <!-- Header & Action: Buka Semua Video -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-base">
              <i class="ri-music-2-line"></i>
            </div>
            <div>
              <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Persiapan Pujian & Musik</h3>
              <p class="text-[11px] text-slate-500" x-text="getSongsSummarySubtitle()"></p>
            </div>
          </div>

          <!-- Button: Buka semua video (Image-18) -->
          <template x-if="getVideoCount() > 0">
            <button type="button" @click="openAllVideos()"
              class="px-3.5 py-1.5 rounded-xl border border-rose-400 hover:bg-rose-50 text-rose-600 text-xs font-bold inline-flex items-center gap-1.5 transition self-start sm:self-center cursor-pointer">
              <i class="ri-play-circle-line text-sm"></i>
              <span x-text="`Buka semua video (${getVideoCount()})`"></span>
            </button>
          </template>
        </div>

        <!-- Liturgi Section Filter Pills (Image-18) -->
        <div class="flex items-center gap-2 flex-wrap">
          <button type="button" @click="activeLiturgiFilter = 'semua'"
            :class="activeLiturgiFilter === 'semua' 
              ? 'bg-blue-600 text-white font-bold' 
              : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
            class="px-3 py-1 rounded-xl text-xs transition cursor-pointer">
            Semua
          </button>

          <template x-for="cat in getLiturgiCategories()" :key="cat.name">
            <button type="button" @click="activeLiturgiFilter = cat.name"
              :class="activeLiturgiFilter === cat.name 
                ? 'bg-blue-600 text-white font-bold' 
                : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
              class="px-3 py-1 rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer">
              <span x-text="cat.name"></span>
              <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-black/10" x-text="cat.count"></span>
            </button>
          </template>
        </div>

        <!-- Grouped Songs List (Image-18) -->
        <div class="space-y-6">
          <template x-for="group in getFilteredSongGroups()" :key="group.title">
            <div class="space-y-2.5">
              
              <!-- Group Header: e.g. PEMBUKAAN 1 lagu (Image-18) -->
              <div class="text-[11px] font-black tracking-wider uppercase text-slate-400 px-1">
                <span x-text="group.title"></span>
                <span class="text-slate-300">·</span>
                <span x-text="`${group.songs.length} lagu`"></span>
              </div>

              <!-- Song Cards -->
              <div class="space-y-2">
                <template x-for="(sItem, sIdx) in group.songs" :key="sItem.id || sIdx">
                  <div class="bg-white rounded-2xl border border-slate-200/80 hover:border-blue-400/80 p-3.5 sm:p-4 flex items-center justify-between gap-3 shadow-2xs transition">
                    
                    <!-- Left: Number badge & Info -->
                    <div class="flex items-center gap-3.5">
                      <div class="w-7 h-7 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0"
                        x-text="sItem.global_index"></div>
                      
                      <div>
                        <div class="flex items-center gap-2 flex-wrap">
                          <h4 class="text-sm font-bold text-slate-900" x-text="sItem.song?.judul_lagu || sItem.judul_lagu"></h4>
                          <template x-if="sItem.nada_dasar || sItem.song?.nada_dasar">
                            <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-bold"
                              x-text="`Key: ${sItem.nada_dasar || sItem.song?.nada_dasar}`"></span>
                          </template>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5" x-text="sItem.song?.artis_sumber || sItem.artis_sumber || '-'"></p>
                      </div>
                    </div>

                    <!-- Right: Action Icons (YouTube, Audio, Chord, Lyrics) (Image-18) -->
                    <div class="flex items-center gap-1.5 shrink-0">
                      <!-- YouTube Video Link -->
                      <template x-if="sItem.song?.youtube_url">
                        <a :href="sItem.song.youtube_url" target="_blank"
                          class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition"
                          title="Buka Video YouTube">
                          <i class="ri-youtube-line text-base"></i>
                        </a>
                      </template>

                      <!-- Audio / Spotify -->
                      <template x-if="sItem.song?.spotify_url">
                        <a :href="sItem.song.spotify_url" target="_blank"
                          class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition"
                          title="Buka Audio Spotify">
                          <i class="ri-spotify-line text-base"></i>
                        </a>
                      </template>

                      <!-- Chord Sheet -->
                      <template x-if="sItem.song?.chord_url">
                        <a :href="sItem.song.chord_url" target="_blank"
                          class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition"
                          title="Buka Lembar Chord">
                          <i class="ri-file-music-line text-base"></i>
                        </a>
                      </template>

                      <!-- Lyrics Modal Trigger -->
                      <button type="button" @click="viewLyrics(sItem.song || sItem)"
                        class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition cursor-pointer"
                        title="Buka Teks Lirik">
                        <i class="ri-file-text-line text-base"></i>
                      </button>
                    </div>

                  </div>
                </template>
              </div>

            </div>
          </template>

          <template x-if="getFilteredSongGroups().length === 0">
            <div class="p-8 text-center text-slate-400 bg-slate-50 rounded-2xl border border-slate-100 text-xs">
              Belum ada lagu yang didaftarkan untuk jadwal ini.
            </div>
          </template>
        </div>

      </div>

      <!-- CARD 3: BAHAN KHOTBAH & MEDIA (IMAGE-18) -->
      <div class="bg-white rounded-3xl border border-slate-200/90 p-5 sm:p-7 shadow-xs space-y-4">
        
        <!-- Header -->
        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
          <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-base">
            <i class="ri-slideshow-line"></i>
          </div>
          <div>
            <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Bahan Khotbah & Media</h3>
            <p class="text-[11px] text-slate-500">Slide PPT, catatan khotbah, dan bahan lain.</p>
          </div>
        </div>

        <!-- Media & Note Cards (Image-18) -->
        <div class="space-y-3">
          <!-- Catatan Khotbah / Singkat jika ada -->
          <template x-if="activeSchedule?.catatan_singkat">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Catatan Pelayanan</span>
              <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line" x-text="activeSchedule.catatan_singkat"></p>
            </div>
          </template>

          <!-- Media Files List -->
          <template x-for="(mFile, mfIdx) in getMediaFiles()" :key="mfIdx">
            <div class="p-4 rounded-2xl border border-slate-200/80 bg-white hover:border-blue-400 flex items-center justify-between gap-3 transition">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg">
                  <i class="ri-computer-line"></i>
                </div>
                <div>
                  <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-900" x-text="mFile.judul || 'Media Ibadah'"></span>
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-bold"
                      x-text="mFile.tipe || 'File'"></span>
                  </div>
                  <template x-if="mFile.url">
                    <a :href="mFile.url" target="_blank"
                      class="text-[11px] font-bold text-blue-600 hover:underline inline-flex items-center gap-1 mt-0.5">
                      <i class="ri-external-link-line"></i> Buka / Unduh
                    </a>
                  </template>
                </div>
              </div>
            </div>
          </template>

          <template x-if="!activeSchedule?.catatan_singkat && getMediaFiles().length === 0">
            <div class="p-4 text-center text-slate-400 text-xs italic bg-slate-50 rounded-2xl">
              Belum ada file materi atau catatan khusus yang dilampirkan.
            </div>
          </template>
        </div>

      </div>

      <!-- FOOTER TIMESTAMP (IMAGE-18) -->
      <div class="text-center text-[11px] text-slate-400 pt-2 pb-6">
        <span>Terakhir diperbarui oleh </span>
        <strong class="text-slate-600" x-text="activeSchedule?.updatedBy || activeSchedule?.createdBy || 'Administrator'"></strong>
        <span> pada </span>
        <span x-text="formatDateShort(activeSchedule?.updated_at || activeSchedule?.created_at)"></span>
      </div>

    </div>

  </main>

  <!-- ========================================== -->
  <!-- 3. LYRICS PREVIEW MODAL -->
  <!-- ========================================== -->
  <div x-show="isLyricsModalOpen" style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl border border-slate-200 overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150"
      @click.outside="isLyricsModalOpen = false">
      
      <!-- Modal Header -->
      <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
        <div>
          <h3 class="text-sm sm:text-base font-extrabold text-slate-900" x-text="activeSongLyrics.judul_lagu"></h3>
          <p class="text-xs text-slate-500" x-text="activeSongLyrics.artis_sumber || 'Pustaka Lagu Gereja'"></p>
        </div>
        <button type="button" @click="isLyricsModalOpen = false"
          class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 font-bold flex items-center justify-center transition">
          <i class="ri-close-line text-lg"></i>
        </button>
      </div>

      <!-- Modal Body: Lyrics Text -->
      <div class="p-6 overflow-y-auto max-h-[60vh] space-y-4">
        <template x-if="activeSongLyrics.lirik_teks">
          <pre class="text-xs sm:text-sm font-sans text-slate-800 whitespace-pre-wrap leading-relaxed tracking-wide bg-slate-50/80 p-4 rounded-2xl border border-slate-100"
            x-text="activeSongLyrics.lirik_teks"></pre>
        </template>
        <template x-if="!activeSongLyrics.lirik_teks">
          <div class="py-8 text-center text-slate-400 text-xs">
            Lirik teks belum dimasukkan ke sistem.
          </div>
        </template>
      </div>

      <!-- Modal Footer -->
      <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
        <span class="text-[11px] text-slate-400">GIA Surabaya Worship Liturgy</span>
        <button type="button" @click="isLyricsModalOpen = false"
          class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold transition">
          Tutup
        </button>
      </div>

    </div>
  </div>

  <!-- TOAST NOTIFICATION -->
  <div x-show="toast.visible" style="display: none;"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-y-3"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-3"
    class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-4 py-3 rounded-2xl shadow-xl flex items-center gap-2.5 text-xs font-semibold">
    <i class="ri-checkbox-circle-fill text-emerald-400 text-base"></i>
    <span x-text="toast.message"></span>
  </div>

  <!-- ========================================== -->
  <!-- 4. FOOTER -->
  <!-- ========================================== -->
  <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
    <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
      <p>&copy; {{ date('Y') }} Gereja Isa Almasih Surabaya. All Rights Reserved.</p>
      <div class="flex items-center gap-4 text-xs font-semibold text-slate-600">
        <a href="{{ url('/') }}" class="hover:text-blue-600">Home</a>
        <a href="{{ url('/#streaming') }}" class="hover:text-blue-600">Live Streaming</a>
        <a href="{{ url('/warta') }}" class="hover:text-blue-600">Warta Jemaat</a>
        <a href="{{ url('/event') }}" class="hover:text-blue-600">Event</a>
        <a href="{{ url('/adminlogin') }}" class="text-blue-600">CMS Login</a>
      </div>
    </div>
  </footer>

  <!-- ========================================== -->
  <!-- 5. JAVASCRIPT / ALPINE CONTROLLER -->
  <!-- ========================================== -->
  <script>
    function publicScheduleApp(config) {
      return {
        viewMode: config.initialSelectedId ? 'detail' : 'list',
        activeTab: config.initialTab || 'mendatang',
        filterJenis: config.initialJenis || '',
        filterLokasi: config.initialLokasi || '',
        schedules: config.schedules || [],
        countMendatang: config.countMendatang || 0,
        countArsip: config.countArsip || 0,
        activeSchedule: null,
        activeLiturgiFilter: 'semua',
        isLyricsModalOpen: false,
        activeSongLyrics: {},
        toast: { visible: false, message: '' },

        init() {
          if (config.initialSelectedId) {
            const found = this.schedules.find(s => String(s.id) === String(config.initialSelectedId));
            if (found) {
              this.activeSchedule = found;
              this.viewMode = 'detail';
            }
          }
        },

        get filteredSchedules() {
          return this.schedules.filter(s => {
            if (this.filterJenis && s.nama_ibadah !== this.filterJenis) return false;
            if (this.filterLokasi && s.nama_cabang !== this.filterLokasi) return false;
            return true;
          });
        },

        setTab(tab) {
          this.activeTab = tab;
          const url = new URL(window.location.href);
          url.searchParams.set('tab', tab);
          if (this.filterJenis) url.searchParams.set('jenis', this.filterJenis);
          if (this.filterLokasi) url.searchParams.set('lokasi', this.filterLokasi);
          url.searchParams.delete('id');
          window.location.href = url.toString();
        },

        applyFilters() {
          const url = new URL(window.location.href);
          url.searchParams.set('tab', this.activeTab);
          if (this.filterJenis) url.searchParams.set('jenis', this.filterJenis);
          else url.searchParams.delete('jenis');

          if (this.filterLokasi) url.searchParams.set('lokasi', this.filterLokasi);
          else url.searchParams.delete('lokasi');

          url.searchParams.delete('id');
          window.location.href = url.toString();
        },

        openSchedule(sched) {
          this.activeSchedule = sched;
          this.viewMode = 'detail';
          this.activeLiturgiFilter = 'semua';
          window.scrollTo({ top: 0, behavior: 'smooth' });

          const url = new URL(window.location.href);
          url.searchParams.set('id', sched.id);
          window.history.pushState({}, '', url.toString());
        },

        closeDetail() {
          this.viewMode = 'list';
          const url = new URL(window.location.href);
          url.searchParams.delete('id');
          window.history.pushState({}, '', url.toString());
        },

        getPemusikList() {
          if (!this.activeSchedule?.petugas_tambahan?.pemusik) return [];
          const list = this.activeSchedule.petugas_tambahan.pemusik;
          return Array.isArray(list) ? list.filter(p => p.nama) : [];
        },

        getPenerimaTamuList() {
          if (!this.activeSchedule?.petugas_tambahan?.penerima_tamu) return [];
          const list = this.activeSchedule.petugas_tambahan.penerima_tamu;
          return Array.isArray(list) ? list.map(x => x.nama || x).filter(Boolean) : [];
        },

        getSoundmanList() {
          if (!this.activeSchedule?.petugas_tambahan?.soundman) return [];
          const list = this.activeSchedule.petugas_tambahan.soundman;
          return Array.isArray(list) ? list.map(x => x.nama || x).filter(Boolean) : [];
        },

        getKolekteList() {
          if (!this.activeSchedule?.petugas_tambahan?.petugas_kolekte) return [];
          const list = this.activeSchedule.petugas_tambahan.petugas_kolekte;
          return Array.isArray(list) ? list.map(x => x.nama || x).filter(Boolean) : [];
        },

        getPerjamuanText() {
          const val = this.activeSchedule?.petugas_tambahan?.petugas_perjamuan_kudus;
          if (!val) return '';
          if (Array.isArray(val)) return val.map(x => x.nama || x).join(', ');
          return String(val);
        },

        getMajelisList() {
          if (!this.activeSchedule?.petugas_tambahan?.majelis_bertugas) return [];
          const list = this.activeSchedule.petugas_tambahan.majelis_bertugas;
          return Array.isArray(list) ? list.map(x => x.nama || x).filter(Boolean) : [];
        },

        getMultimediaList() {
          if (!this.activeSchedule?.petugas_tambahan?.petugas_multimedia) return [];
          const list = this.activeSchedule.petugas_tambahan.petugas_multimedia;
          return Array.isArray(list) ? list.map(x => x.nama || x).filter(Boolean) : [];
        },

        getMediaFiles() {
          if (!this.activeSchedule?.bahan_media) return [];
          const files = this.activeSchedule.bahan_media;
          return Array.isArray(files) ? files.filter(f => f.judul || f.url) : [];
        },

        getVideoCount() {
          const songs = this.activeSchedule?.schedule_songs || [];
          return songs.filter(s => s.song?.youtube_url).length;
        },

        getSongsSummarySubtitle() {
          const songs = this.activeSchedule?.schedule_songs || [];
          const cats = this.getLiturgiCategories();
          return `${songs.length} lagu · ${cats.length} bagian liturgi`;
        },

        getLiturgiCategories() {
          const songs = this.activeSchedule?.schedule_songs || [];
          const catMap = {};
          songs.forEach(s => {
            const cat = s.bagian_liturgi || 'Pujian & Penyembahan';
            catMap[cat] = (catMap[cat] || 0) + 1;
          });
          return Object.keys(catMap).map(k => ({ name: k, count: catMap[k] }));
        },

        getFilteredSongGroups() {
          const songs = (this.activeSchedule?.schedule_songs || []).map((s, idx) => ({
            ...s,
            global_index: idx + 1
          }));

          const groupMap = {};
          songs.forEach(s => {
            const part = s.bagian_liturgi || 'Pujian & Penyembahan';
            if (this.activeLiturgiFilter !== 'semua' && this.activeLiturgiFilter !== part) return;
            if (!groupMap[part]) groupMap[part] = [];
            groupMap[part].push(s);
          });

          return Object.keys(groupMap).map(key => ({
            title: key,
            songs: groupMap[key]
          }));
        },

        openAllVideos() {
          const songs = this.activeSchedule?.schedule_songs || [];
          const videoUrls = songs.map(s => s.song?.youtube_url).filter(Boolean);
          if (videoUrls.length === 0) {
            this.showToast('Tidak ada video untuk jadwal ini.');
            return;
          }
          videoUrls.forEach((u, i) => {
            setTimeout(() => window.open(u, '_blank'), i * 300);
          });
        },

        viewLyrics(song) {
          this.activeSongLyrics = song;
          this.isLyricsModalOpen = true;
        },

        shareWhatsApp() {
          if (!this.activeSchedule) return;
          const s = this.activeSchedule;
          let text = `*JADWAL & LITURGI IBADAH - GEREJA ISA ALMASIH SURABAYA*\n\n`;
          text += `📅 *Hari / Tanggal:* ${this.formatDateFull(s.tanggal)}\n`;
          text += `⛪ *Ibadah:* ${s.nama_ibadah || '-'} (${s.nama_cabang || '-'})\n`;
          if (s.nama_sesi) text += `⏰ *Sesi:* ${s.nama_sesi}\n`;
          if (s.tema) text += `📖 *Tema:* "${s.tema}"\n`;
          if (s.ayat) text += `📜 *Ayat Bacaan:* ${s.ayat}\n\n`;

          text += `*PETUGAS PELAYANAN:*\n`;
          text += `• Pengkhotbah: ${s.pengkhotbah || '-'}\n`;
          text += `• Pemimpin Pujian (WL): ${s.pemimpin_pujian || '-'}\n`;
          const musicians = this.getPemusikList();
          if (musicians.length > 0) {
            text += `• Pemusik: ` + musicians.map(m => `${m.nama} (${m.instrumen})`).join(', ') + `\n`;
          }

          const songs = s.schedule_songs || [];
          if (songs.length > 0) {
            text += `\n*SUSUNAN LAGU:*\n`;
            songs.forEach((item, idx) => {
              const title = item.song?.judul_lagu || '-';
              const key = item.nada_dasar || item.song?.nada_dasar ? ` (${item.nada_dasar || item.song?.nada_dasar})` : '';
              text += `${idx + 1}. [${item.bagian_liturgi}] ${title}${key}\n`;
            });
          }

          text += `\n🔗 *Lihat Liturgi Lengkap:* ${window.location.href}`;

          const waUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`;
          window.open(waUrl, '_blank');
        },

        copyLink() {
          navigator.clipboard.writeText(window.location.href).then(() => {
            this.showToast('Tautan jadwal berhasil disalin ke clipboard!');
          }).catch(() => {
            this.showToast('Gagal menyalin tautan.');
          });
        },

        showToast(msg) {
          this.toast.message = msg;
          this.toast.visible = true;
          setTimeout(() => { this.toast.visible = false; }, 3000);
        },

        formatDateShort(dStr) {
          if (!dStr) return '';
          const d = new Date(dStr);
          return d.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
        },

        formatDateFull(dStr) {
          if (!dStr) return '';
          const d = new Date(dStr);
          return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        }
      };
    }
  </script>
</body>
</html>
