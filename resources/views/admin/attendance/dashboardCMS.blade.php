@extends('admin.layoutCMS')

@section('content')
<div class="space-y-6 pb-12" x-data="attendanceDashboard()">

  <!-- Page Header -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold mb-2">
        <i class="ri-dashboard-3-line"></i> Analytics & Kehadiran
      </div>
      <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dashboard Kehadiran</h1>
      <p class="text-xs text-slate-500 mt-0.5">Ringkasan statistik, tren kehadiran jemaat, dan tindak lanjut pastoral.</p>
    </div>
    <div class="flex items-center gap-2.5">
      <a href="{{ route('admin.attendance.history') }}"
        class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
        <i class="ri-history-line text-sm"></i> Riwayat Sesi
      </a>
      <a href="{{ route('admin.attendance.input') }}"
        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition">
        <i class="ri-add-line text-sm"></i> Input Kehadiran
      </a>
    </div>
  </div>

  <!-- Filter & Periode Toolbar -->
  <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm">
    <form method="GET" action="{{ route('admin.attendance.report') }}" id="filterForm" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
      
      <!-- Segmented Control Mode Periode -->
      <div class="flex items-center gap-2 overflow-x-auto pb-1 lg:pb-0">
        <div class="inline-flex p-1 bg-slate-100 rounded-xl text-xs font-semibold text-slate-600 shrink-0">
          <button type="button" @click="setPeriod('tanggal')"
            :class="periodType === 'tanggal' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
            class="px-3 py-1.5 rounded-lg transition">Tanggal</button>
          <button type="button" @click="setPeriod('bulan')"
            :class="periodType === 'bulan' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
            class="px-3 py-1.5 rounded-lg transition">Bulan</button>
          <button type="button" @click="setPeriod('tahun')"
            :class="periodType === 'tahun' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
            class="px-3 py-1.5 rounded-lg transition">Tahun</button>
          <button type="button" @click="setPeriod('rentang')"
            :class="periodType === 'rentang' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
            class="px-3 py-1.5 rounded-lg transition">Rentang</button>
        </div>
        <input type="hidden" name="period_type" :value="periodType">

        <!-- Input Dynamic Periode -->
        <div class="flex items-center gap-2">
          <template x-if="periodType === 'tanggal'">
            <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
              class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:outline-none focus:border-blue-500 bg-white">
          </template>

          <template x-if="periodType === 'bulan'">
            <input type="month" name="month" value="{{ $selectedMonth }}" onchange="this.form.submit()"
              class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:outline-none focus:border-blue-500 bg-white">
          </template>

          <template x-if="periodType === 'tahun'">
            <select name="year" onchange="this.form.submit()"
              class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:outline-none focus:border-blue-500 bg-white">
              @for($y = date('Y') + 1; $y >= date('Y') - 5; $y--)
                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
              @endfor
            </select>
          </template>

          <template x-if="periodType === 'rentang'">
            <div class="flex items-center gap-1.5">
              <input type="date" name="start_date" value="{{ $startDateParam }}"
                class="px-2.5 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:outline-none focus:border-blue-500 bg-white">
              <span class="text-xs text-slate-400">-</span>
              <input type="date" name="end_date" value="{{ $endDateParam }}"
                class="px-2.5 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:outline-none focus:border-blue-500 bg-white">
              <button type="submit" class="p-1.5 bg-blue-600 text-white rounded-lg text-xs hover:bg-blue-700 transition">
                <i class="ri-check-line"></i>
              </button>
            </div>
          </template>
        </div>
      </div>

      <!-- Dropdown Filter: Jenis, Sesi, Lokasi -->
      <div class="flex flex-wrap items-center gap-2">
        <select name="jenis" onchange="this.form.submit()"
          class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:outline-none focus:border-blue-500 bg-white">
          <option value="all">Semua Jenis Ibadah</option>
          @foreach($jenisIbadahs as $j)
            <option value="{{ $j->nama_ibadah }}" {{ request('jenis') == $j->nama_ibadah ? 'selected' : '' }}>
              {{ $j->nama_ibadah }}
            </option>
          @endforeach
        </select>

        <select name="sesi" onchange="this.form.submit()"
          class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:outline-none focus:border-blue-500 bg-white">
          <option value="all">Semua Sesi</option>
          @foreach($sesis as $s)
            <option value="{{ $s->nama_sesi }}" {{ request('sesi') == $s->nama_sesi ? 'selected' : '' }}>
              {{ $s->nama_sesi }}
            </option>
          @endforeach
        </select>

        <select name="lokasi" onchange="this.form.submit()"
          class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:outline-none focus:border-blue-500 bg-white">
          <option value="all">Semua Lokasi</option>
          @foreach($cabangs as $c)
            <option value="{{ $c->nama_cabang }}" {{ request('lokasi') == $c->nama_cabang ? 'selected' : '' }}>
              {{ $c->nama_cabang }}
            </option>
          @endforeach
        </select>

        @if(request()->hasAny(['jenis', 'sesi', 'lokasi', 'period_type', 'date', 'month', 'year', 'start_date', 'end_date']))
          <a href="{{ route('admin.attendance.report') }}"
            class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition" title="Reset filter">
            <i class="ri-refresh-line text-sm"></i>
          </a>
        @endif
      </div>
    </form>
  </div>

  <!-- 6 KPI CARDS -->
  <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3.5">
    
    <!-- Total Jemaat -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-blue-300 transition">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-semibold text-slate-500">Total Jemaat</span>
        <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
          <i class="ri-team-line"></i>
        </div>
      </div>
      <div class="text-2xl font-extrabold text-slate-900">{{ number_format($totalJemaat) }}</div>
      <p class="text-[10px] text-slate-500 mt-1 truncate">
        {{ $totalJemaat - $uniqueTamuHadir }} anggota · {{ $uniqueTamuHadir }} tamu
      </p>
    </div>

    <!-- Total Hadir -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-emerald-300 transition">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-semibold text-slate-500">Total Hadir</span>
        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
          <i class="ri-user-follow-line"></i>
        </div>
      </div>
      <div class="text-2xl font-extrabold text-emerald-600">{{ number_format($totalHadir) }}</div>
      <p class="text-[10px] text-slate-500 mt-1 truncate">
        {{ $totalHadirAnggota }} anggota · {{ $uniqueTamuHadir }} tamu
      </p>
    </div>

    <!-- Total Tidak Hadir -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-rose-300 transition">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-semibold text-slate-500">Tidak Hadir</span>
        <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
          <i class="ri-user-unfollow-line"></i>
        </div>
      </div>
      <div class="text-2xl font-extrabold text-rose-600">{{ number_format($totalTidakHadir) }}</div>
      <p class="text-[10px] text-slate-500 mt-1 truncate">
        anggota terdaftar
      </p>
    </div>

    <!-- Persentase Kehadiran -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-indigo-300 transition">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-semibold text-slate-500">% Kehadiran</span>
        <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
          <i class="ri-pie-chart-2-line"></i>
        </div>
      </div>
      <div class="text-2xl font-extrabold text-indigo-600">{{ $persentaseKehadiran }}%</div>
      <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
        <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, $persentaseKehadiran) }}%"></div>
      </div>
    </div>

    <!-- Jumlah Sesi -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-amber-300 transition">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-semibold text-slate-500">Jumlah Sesi</span>
        <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
          <i class="ri-calendar-event-line"></i>
        </div>
      </div>
      <div class="text-2xl font-extrabold text-slate-900">{{ $jumlahSesi }} <span class="text-xs font-semibold text-slate-500">Sesi</span></div>
      <p class="text-[10px] text-slate-500 mt-1 truncate">
        Rata-rata {{ $rataRataPerSesi }} org/sesi
      </p>
    </div>

    <!-- Tamu Baru -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden group hover:border-purple-300 transition">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-semibold text-slate-500">Tamu Baru</span>
        <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xs">
          <i class="ri-user-star-line"></i>
        </div>
      </div>
      <div class="text-2xl font-extrabold text-purple-600">{{ $tamuBaruCount }} <span class="text-xs font-semibold text-slate-500">Org</span></div>
      <p class="text-[10px] text-slate-500 mt-1 truncate">
        Pertama kali hadir
      </p>
    </div>

  </div>

  <!-- ==================== LAPORAN KEHADIRAN BERDASARKAN KATEGORI (ANAK, REMAJA, PEMUDA, DEWASA, LANSIA, TAMU) ==================== -->
  <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
          <i class="ri-pie-chart-line"></i>
        </div>
        <div>
          <h3 class="font-bold text-slate-900 text-sm">Laporan Kehadiran Berdasarkan Kategori</h3>
          <p class="text-xs text-slate-500 mt-0.5">Analisis dan data distribusi kehadiran jemaat menurut kelompok kategori usia pada periode {{ $periodLabel }}</p>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
          Total Hadir: <strong class="text-indigo-600">{{ number_format($totalHadir) }}</strong>
        </span>
      </div>
    </div>

    <!-- 6 Category Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
      @foreach($kategoriStats as $catKey => $cat)
        <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-slate-300 hover:shadow-xs transition space-y-3">
          <div class="flex items-center justify-between">
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $cat['badge_class'] }}">
              {{ $cat['nama'] }}
            </span>
            <span class="text-[10px] font-bold text-slate-400">
              {{ $cat['share_total'] }}%
            </span>
          </div>

          <div>
            <div class="text-xl font-extrabold text-slate-900">
              {{ number_format($cat['total_hadir']) }}
              <span class="text-[11px] font-medium text-slate-400">/ {{ number_format($cat['total_terdaftar']) }}</span>
            </div>
            <div class="text-[10px] text-slate-500 font-medium truncate mt-0.5">
              {{ $cat['label'] }}
            </div>
          </div>

          <!-- Progress Bar -->
          <div class="space-y-1">
            <div class="flex items-center justify-between text-[10px]">
              <span class="text-slate-400">Tingkat Hadir</span>
              <span class="font-bold text-slate-700">{{ $cat['persen_kehadiran'] }}%</span>
            </div>
            <div class="w-full bg-slate-200/70 rounded-full h-1.5 overflow-hidden">
              <div class="h-1.5 rounded-full transition-all duration-500"
                style="width: {{ min(100, $cat['persen_kehadiran']) }}%; background-color: {{ $cat['color'] }};"></div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <!-- Visual Category Distribution Bar & Summary Table -->
    <div class="space-y-4 pt-2">
      <!-- Stacked Distribution Bar -->
      <div class="space-y-1.5">
        <div class="flex items-center justify-between text-xs">
          <span class="font-bold text-slate-700">Komposisi Kehadiran Kategori:</span>
          <span class="text-slate-400 text-[11px]">100% Kontribusi Jemaat Hadir</span>
        </div>
        <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden flex">
          @foreach($kategoriStats as $catKey => $cat)
            @if($cat['share_total'] > 0)
              <div class="h-full transition-all"
                style="width: {{ $cat['share_total'] }}%; background-color: {{ $cat['color'] }};"
                title="{{ $cat['label'] }}: {{ $cat['total_hadir'] }} org ({{ $cat['share_total'] }}%)"></div>
            @endif
          @endforeach
        </div>
        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 pt-1 text-[11px] text-slate-500">
          @foreach($kategoriStats as $catKey => $cat)
            <div class="flex items-center gap-1.5">
              <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $cat['color'] }};"></span>
              <span>{{ $cat['nama'] }}: <strong class="text-slate-800">{{ $cat['total_hadir'] }}</strong> ({{ $cat['share_total'] }}%)</span>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Data Table Kategori -->
      <div class="overflow-x-auto rounded-xl border border-slate-200">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 font-semibold text-slate-600">
              <th class="py-2.5 px-4">Kategori Jemaat</th>
              <th class="py-2.5 px-4">Rentang Usia</th>
              <th class="py-2.5 px-4 text-center">Terdaftar</th>
              <th class="py-2.5 px-4 text-center">Hadir</th>
              <th class="py-2.5 px-4 text-center">% Tingkat Kehadiran</th>
              <th class="py-2.5 px-4 text-center">Share Kehadiran</th>
              <th class="py-2.5 px-4 text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-slate-700">
            @foreach($kategoriStats as $catKey => $cat)
              <tr class="hover:bg-slate-50/60 transition">
                <td class="py-2.5 px-4 font-bold text-slate-900 flex items-center gap-2">
                  <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $cat['color'] }};"></span>
                  <span>{{ $cat['nama'] }}</span>
                </td>
                <td class="py-2.5 px-4 text-slate-500 font-medium">{{ $cat['label'] }}</td>
                <td class="py-2.5 px-4 text-center font-semibold text-slate-800">{{ number_format($cat['total_terdaftar']) }}</td>
                <td class="py-2.5 px-4 text-center font-bold text-slate-900">{{ number_format($cat['total_hadir']) }}</td>
                <td class="py-2.5 px-4 text-center">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                    style="background-color: {{ $cat['color'] }}20; color: {{ $cat['color'] }};">
                    {{ $cat['persen_kehadiran'] }}%
                  </span>
                </td>
                <td class="py-2.5 px-4 text-center font-semibold text-slate-700">{{ $cat['share_total'] }}%</td>
                <td class="py-2.5 px-4 text-center">
                  @if($cat['persen_kehadiran'] >= 75)
                    <span class="text-emerald-600 font-bold text-[11px] inline-flex items-center gap-1">
                      <i class="ri-checkbox-circle-fill"></i> Sangat Baik
                    </span>
                  @elseif($cat['persen_kehadiran'] >= 50)
                    <span class="text-blue-600 font-bold text-[11px] inline-flex items-center gap-1">
                      <i class="ri-checkbox-circle-line"></i> Baik
                    </span>
                  @elseif($cat['persen_kehadiran'] > 0)
                    <span class="text-amber-600 font-bold text-[11px] inline-flex items-center gap-1">
                      <i class="ri-alert-line"></i> Perlu Ditingkatkan
                    </span>
                  @else
                    <span class="text-slate-400 font-medium text-[11px]">-</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- 2-COLUMN SECTION: GRAFIK TREN & PERLU DIKUNJUNGI -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- Kolom Kiri: Grafik Tren Kehadiran (8 Cols) -->
    <div class="lg:col-span-8 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
      <div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
          <div>
            <h3 class="font-bold text-slate-900 text-sm">Grafik Tren Kehadiran</h3>
            <p class="text-xs text-slate-500 mt-0.5">Tren statistik kehadiran per minggu dalam periode {{ $periodLabel }}</p>
          </div>
          <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-medium">
              <span class="w-2 h-2 rounded-full bg-blue-600"></span> Target: <strong>{{ $targetMingguan }} org</strong>
            </span>
          </div>
        </div>

        <!-- Line SVG Graph -->
        <div class="w-full relative py-2">
          @php
            $maxData = max(max(array_column($trenWeeks, 'count') ?: [0]), $targetMingguan, 10);
            $chartHeight = 160;
            $chartWidth = 600;
            $countWeeks = count($trenWeeks);
            $points = [];
            
            foreach($trenWeeks as $idx => $tw) {
              $x = $countWeeks > 1 ? ($idx / ($countWeeks - 1)) * ($chartWidth - 40) + 20 : $chartWidth / 2;
              $y = $chartHeight - 30 - (($tw['count'] / $maxData) * ($chartHeight - 50));
              $points[] = ['x' => $x, 'y' => $y, 'data' => $tw];
            }
            
            $pathStr = '';
            $areaStr = '';
            if(count($points) > 0) {
              $pathStr = "M " . $points[0]['x'] . " " . $points[0]['y'];
              for($i = 0; $i < count($points) - 1; $i++) {
                $p0 = $points[$i];
                $p1 = $points[$i+1];
                $mx = ($p0['x'] + $p1['x']) / 2;
                $pathStr .= " C {$mx} {$p0['y']}, {$mx} {$p1['y']}, {$p1['x']} {$p1['y']}";
              }
              $lastP = end($points);
              $firstP = reset($points);
              $areaStr = $pathStr . " L " . $lastP['x'] . " " . ($chartHeight - 20) . " L " . $firstP['x'] . " " . ($chartHeight - 20) . " Z";
            }
            $targetY = $chartHeight - 30 - (($targetMingguan / $maxData) * ($chartHeight - 50));
          @endphp

          <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="w-full h-48 overflow-visible">
            <defs>
              <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.25"/>
                <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0"/>
              </linearGradient>
            </defs>

            <!-- Grid Lines -->
            <line x1="15" y1="{{ $chartHeight - 20 }}" x2="{{ $chartWidth - 15 }}" y2="{{ $chartHeight - 20 }}" stroke="#e2e8f0" stroke-width="1" />
            <line x1="15" y1="{{ $chartHeight / 2 }}" x2="{{ $chartWidth - 15 }}" y2="{{ $chartHeight / 2 }}" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="4" />
            
            <!-- Target Line -->
            <line x1="15" y1="{{ $targetY }}" x2="{{ $chartWidth - 15 }}" y2="{{ $targetY }}" stroke="#94a3b8" stroke-width="1" stroke-dasharray="4" />
            <text x="{{ $chartWidth - 10 }}" y="{{ $targetY - 4 }}" fill="#94a3b8" font-size="9" text-anchor="end" font-weight="600">Target ({{ $targetMingguan }})</text>

            <!-- Area Fill -->
            @if(!empty($areaStr))
              <path d="{{ $areaStr }}" fill="url(#chartGradient)" />
            @endif

            <!-- Line Path -->
            @if(!empty($pathStr))
              <path d="{{ $pathStr }}" fill="none" stroke="#2563eb" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
            @endif

            <!-- Data Circles & Labels -->
            @foreach($points as $p)
              <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="5" fill="#ffffff" stroke="#2563eb" stroke-width="2.5" class="transition-all hover:r-7 cursor-pointer">
                <title>{{ $p['data']['label'] }}: {{ $p['data']['count'] }} Orang</title>
              </circle>
              <text x="{{ $p['x'] }}" y="{{ $p['y'] - 10 }}" text-anchor="middle" font-size="10" font-weight="bold" fill="#1e293b">
                {{ $p['data']['count'] }}
              </text>
              <text x="{{ $p['x'] }}" y="{{ $chartHeight - 5 }}" text-anchor="middle" font-size="9" font-weight="500" fill="#64748b">
                {{ $p['data']['label'] }}
              </text>
            @endforeach
          </svg>
        </div>
      </div>

      <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between text-xs text-slate-500 gap-2">
        <span>Rata-rata kehadiran mingguan: <strong class="text-slate-800">{{ $rataRataTren }} orang</strong></span>
        <span class="text-[11px] text-slate-400">Data otomatis terupdate dari catatan absensi</span>
      </div>
    </div>

    <!-- Kolom Kanan: Panel Perlu Dikunjungi (4 Cols) -->
    <div class="lg:col-span-4 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-2">
            <div class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></div>
            <h3 class="font-bold text-slate-900 text-sm">Perlu Dikunjungi</h3>
          </div>
          <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 font-bold text-[11px]">
            {{ count($perluDikunjungi) }} orang
          </span>
        </div>
        <p class="text-xs text-slate-500 mb-4 leading-relaxed">
          Anggota yang tidak hadir 3 minggu berturut-turut untuk dijangkau tim pastoral.
        </p>

        <!-- List Anggota Perlu Dikunjungi -->
        <div class="space-y-2.5 max-h-[280px] overflow-y-auto pr-1">
          @forelse(array_slice($perluDikunjungi, 0, 6) as $p)
            <div class="p-2.5 rounded-xl border border-slate-100 hover:border-slate-200 bg-slate-50/50 flex items-center justify-between gap-3 transition">
              <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold text-xs flex items-center justify-center shrink-0">
                  {{ strtoupper(substr($p['nama_asli'], 0, 1)) }}
                </div>
                <div class="min-w-0">
                  <h4 class="font-bold text-xs text-slate-800 truncate">{{ $p['nama_asli'] }}</h4>
                  <p class="text-[10px] text-slate-500">
                    {{ $p['wilayah'] }} · Terakhir: {{ $p['terakhir_hadir'] }}
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-1 shrink-0">
                @if($p['telepon'] && $p['telepon'] !== '-')
                  @php
                    $cleanPhone = preg_replace('/[^0-9]/', '', $p['telepon']);
                    if (str_starts_with($cleanPhone, '0')) {
                      $cleanPhone = '62' . substr($cleanPhone, 1);
                    }
                  @endphp
                  <a href="https://wa.me/{{ $cleanPhone }}?text=Shalom%20{{ urlencode($p['nama_asli']) }},%20salam%20hangat%20dari%20GIA.%20Kami%20rindu%20beribadah%20bersama%20Anda."
                    target="_blank"
                    class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center text-xs transition" title="Hubungi WhatsApp">
                    <i class="ri-whatsapp-line"></i>
                  </a>
                @endif
              </div>
            </div>
          @empty
            <div class="py-8 text-center text-slate-400 text-xs">
              <i class="ri-checkbox-circle-line text-2xl text-emerald-500 mb-1 block"></i>
              Semua anggota aktif telah beribadah dalam 3 minggu terakhir.
            </div>
          @endforelse
        </div>
      </div>

      <div class="mt-4 pt-3 border-t border-slate-100">
        <a href="{{ route('admin.attendance.exportKunjungan') }}"
          class="w-full py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center justify-center gap-2 transition">
          <i class="ri-download-2-line text-sm"></i> Ekspor Daftar Kunjungan (CSV)
        </a>
      </div>
    </div>

  </div>

  <!-- WIDGET ULANG TAHUN JEMAAT -->
  <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
          <i class="ri-cake-2-line"></i>
        </div>
        <div>
          <h3 class="font-bold text-slate-900 text-sm">Ulang Tahun Jemaat</h3>
          <p class="text-xs text-slate-500 mt-0.5">Jadwal hari ulang tahun untuk salam pastoral & doa</p>
        </div>
      </div>

      <!-- Toggle Minggu Ini vs Bulan Ini -->
      <div class="inline-flex p-1 bg-slate-100 rounded-xl text-xs font-semibold text-slate-600 self-start sm:self-auto">
        <a href="{{ request()->fullUrlWithQuery(['ultah_mode' => 'minggu']) }}"
          class="px-3 py-1 rounded-lg transition {{ $filterUltah === 'minggu' ? 'bg-white text-blue-700 shadow-sm' : 'hover:text-slate-900' }}">
          Minggu Ini
        </a>
        <a href="{{ request()->fullUrlWithQuery(['ultah_mode' => 'bulan']) }}"
          class="px-3 py-1 rounded-lg transition {{ $filterUltah === 'bulan' ? 'bg-white text-blue-700 shadow-sm' : 'hover:text-slate-900' }}">
          Bulan Ini
        </a>
      </div>
    </div>

    <!-- Cards Grid Ultah -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
      @forelse($ulangTahunList as $ultah)
        <div class="p-3.5 rounded-xl border {{ $ultah['is_today'] ? 'border-amber-300 bg-amber-50/50 ring-1 ring-amber-200' : 'border-slate-100 bg-slate-50/40' }} flex items-center gap-3 transition">
          <div class="w-11 h-11 rounded-xl {{ $ultah['is_today'] ? 'bg-amber-500 text-white shadow-sm' : 'bg-slate-200 text-slate-700' }} flex flex-col items-center justify-center shrink-0">
            <span class="text-[9px] font-bold uppercase leading-none">{{ explode(' ', $ultah['tanggal'])[1] ?? '' }}</span>
            <span class="text-sm font-extrabold leading-none mt-0.5">{{ explode(' ', $ultah['tanggal'])[0] ?? '' }}</span>
          </div>
          <div class="min-w-0 flex-1">
            <div class="flex items-center gap-1.5">
              <h4 class="font-bold text-xs text-slate-800 truncate">{{ $ultah['nama_asli'] }}</h4>
              @if($ultah['is_today'])
                <span class="px-1.5 py-0.2 rounded bg-amber-200 text-amber-800 text-[9px] font-bold">Hari ini</span>
              @endif
            </div>
            <p class="text-[10px] text-slate-500 mt-0.5">
              Ulang tahun ke-{{ $ultah['usia'] }} · {{ $ultah['wilayah'] }}
            </p>
          </div>
        </div>
      @empty
        <div class="col-span-full py-6 text-center text-slate-400 text-xs">
          Tidak ada jemaat yang berulang tahun pada periode {{ $filterUltah === 'minggu' ? 'minggu ini' : 'bulan ini' }}.
        </div>
      @endforelse
    </div>
  </div>

  <!-- RINCIAN KEHADIRAN JEMAAT (DATA TABLE + EXPORT) -->
  <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm" x-data="rincianTable()">
    
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-5">
      <div>
        <h3 class="font-bold text-slate-900 text-sm">Rincian Kehadiran Jemaat</h3>
        <p class="text-xs text-slate-500 mt-0.5">Rekapitulasi kehadiran jemaat individu pada sesi ibadah {{ $periodLabel }}</p>
      </div>

      <!-- Action Buttons: Print & Export -->
      <div class="flex items-center gap-2">
        <button type="button" @click="printTable()"
          class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
          <i class="ri-printer-line text-sm"></i> Cetak / PDF
        </button>
        <button type="button" @click="exportCSV()"
          class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition">
          <i class="ri-file-excel-2-line text-sm text-emerald-400"></i> Ekspor CSV / Excel
        </button>
      </div>
    </div>

    <!-- Tabs & Filter Toolbar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
      <!-- Tabs Hadir / Tidak Hadir / Tamu Baru -->
      <div class="inline-flex p-1 bg-slate-100 rounded-xl text-xs font-semibold text-slate-600 self-start">
        <button type="button" @click="activeTab = 'hadir'"
          :class="activeTab === 'hadir' ? 'bg-white text-blue-700 shadow-sm' : 'hover:text-slate-900'"
          class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
          <span>Hadir</span>
          <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="activeTab === 'hadir' ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-600'">
            {{ count($listHadir) }}
          </span>
        </button>

        <button type="button" @click="activeTab = 'tidak_hadir'"
          :class="activeTab === 'tidak_hadir' ? 'bg-white text-blue-700 shadow-sm' : 'hover:text-slate-900'"
          class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
          <span>Tidak Hadir</span>
          <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="activeTab === 'tidak_hadir' ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-600'">
            {{ count($listTidakHadir) }}
          </span>
        </button>

        <button type="button" @click="activeTab = 'tamu_baru'"
          :class="activeTab === 'tamu_baru' ? 'bg-white text-blue-700 shadow-sm' : 'hover:text-slate-900'"
          class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
          <span>Tamu Baru</span>
          <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="activeTab === 'tamu_baru' ? 'bg-purple-100 text-purple-800' : 'bg-slate-200 text-slate-600'">
            {{ count($listTamuBaru) }}
          </span>
        </button>
      </div>

      <!-- Controls: Filter Lokasi, Filter Kategori Usia, Live Search & Reset -->
      <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
        <!-- Filter Lokasi Kehadiran -->
        <div class="relative">
          <select x-model="filterLokasi"
            class="pl-3 pr-8 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white focus:outline-none focus:border-blue-500 shadow-2xs">
            <option value="all">Semua Lokasi / Wilayah</option>
            @foreach($cabangs as $c)
              <option value="{{ $c->nama_cabang }}">{{ $c->nama_cabang }}</option>
            @endforeach
          </select>
        </div>

        <!-- Filter Kategori Usia -->
        <div class="relative">
          <select x-model="filterKategori"
            class="pl-3 pr-8 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white focus:outline-none focus:border-blue-500 shadow-2xs">
            <option value="all">Semua Kategori Usia</option>
            <option value="Anak">Anak (0-12 th)</option>
            <option value="Remaja">Remaja (13-20 th)</option>
            <option value="Pemuda">Pemuda (21-30 th)</option>
            <option value="Dewasa">Dewasa (31-59 th)</option>
            <option value="Lansia">Lansia (≥ 60 th)</option>
            <option value="Tamu">Tamu</option>
          </select>
        </div>

        <!-- Live Search Box -->
        <div class="relative flex-1 sm:w-56 min-w-[150px]">
          <i class="ri-search-2-line text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 text-xs"></i>
          <input type="text" x-model="searchQuery"
            placeholder="Cari nama, alias..."
            class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:outline-none focus:border-blue-500 bg-white shadow-2xs">
        </div>

        <!-- Reset Filter Button -->
        <button type="button" x-show="filterLokasi !== 'all' || filterKategori !== 'all' || searchQuery"
          @click="resetFilters()"
          class="p-1.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-500 hover:text-slate-800 text-xs transition inline-flex items-center gap-1 shadow-2xs"
          title="Reset filter & pencarian">
          <i class="ri-refresh-line"></i>
          <span class="hidden sm:inline">Reset</span>
        </button>
      </div>
    </div>

    <!-- Active Filter Indicator Banner -->
    <div class="flex items-center justify-between text-[11px] text-slate-500 mb-3 px-1"
      x-show="filterLokasi !== 'all' || filterKategori !== 'all' || searchQuery">
      <span class="inline-flex items-center gap-1.5 flex-wrap">
        <span>Menampilkan <strong class="text-blue-600 font-bold" x-text="filteredRows.length"></strong> dari <span x-text="currentList.length"></span> jemaat</span>
        <template x-if="filterLokasi !== 'all'">
          <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-semibold">
            Lokasi: <span x-text="filterLokasi"></span>
          </span>
        </template>
        <template x-if="filterKategori !== 'all'">
          <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 font-semibold">
            Kategori: <span x-text="filterKategori"></span>
          </span>
        </template>
      </span>
      <button type="button" @click="resetFilters()" class="text-blue-600 hover:underline font-semibold shrink-0">
        Hapus Filter
      </button>
    </div>

    <!-- Table Container -->
    <div class="overflow-x-auto rounded-xl border border-slate-200">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 font-semibold text-slate-600">
            <th class="py-3 px-4 w-12 text-center">No</th>
            <th class="py-3 px-4">Nama Jemaat</th>
            <th class="py-3 px-4">Kategori Usia</th>
            <th class="py-3 px-4">Status</th>
            <th class="py-3 px-4">Alias</th>
            <th class="py-3 px-4">Wilayah</th>
            <th class="py-3 px-4 text-center">Total Kehadiran</th>
            <th class="py-3 px-4 text-center">Terakhir Hadir</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
          <template x-for="(item, idx) in filteredRows" :key="item.id || idx">
            <tr class="hover:bg-slate-50/60 transition">
              <td class="py-3 px-4 text-center font-medium text-slate-400" x-text="idx + 1"></td>
              <td class="py-3 px-4">
                <button type="button" @click="openJemaatDetail(item.id, item.nama)"
                  class="font-bold text-blue-700 hover:text-blue-900 hover:underline text-left inline-flex items-center gap-1.5 group cursor-pointer">
                  <span x-text="item.nama"></span>
                  <i class="ri-information-line text-xs opacity-0 group-hover:opacity-100 transition text-blue-500"></i>
                </button>
              </td>
              <td class="py-3 px-4">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
                  :class="getCategoryBadgeClass(item.kategori)"
                  x-text="item.kategori ? `${item.kategori} (${item.usia})` : '-'"></span>
              </td>
              <td class="py-3 px-4">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold"
                  :class="item.status === 'Tamu' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800'"
                  x-text="item.status"></span>
              </td>
              <td class="py-3 px-4 text-slate-500" x-text="item.alias"></td>
              <td class="py-3 px-4 text-slate-600" x-text="item.wilayah"></td>
              <td class="py-3 px-4 text-center font-bold"
                :class="item.kehadiran === '0x' ? 'text-slate-400' : 'text-blue-600'"
                x-text="item.kehadiran"></td>
              <td class="py-3 px-4 text-center text-slate-500" x-text="item.terakhir_hadir"></td>
            </tr>
          </template>
          <tr x-show="filteredRows.length === 0">
            <td colspan="8" class="py-8 text-center text-slate-400 text-xs">
              Tidak ada data jemaat yang sesuai kriteria.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- ==================== MODAL DETAIL JEMAAT & 10 KEDATANGAN TERAKHIR ==================== -->
    <div x-show="showJemaatModal" style="display: none;"
      class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]"
        @click.outside="showJemaatModal = false">
        
        <!-- Modal Header -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white font-black text-base flex items-center justify-center shadow-sm">
              <span x-text="activeJemaat ? (activeJemaat.nama_asli ? activeJemaat.nama_asli.charAt(0).toUpperCase() : '?') : '?'"></span>
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h3 class="text-base font-bold text-slate-900" x-text="activeJemaat ? activeJemaat.nama_asli : 'Detail Jemaat'"></h3>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                  :class="activeJemaat && activeJemaat.status === 'Tamu' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800'"
                  x-text="activeJemaat ? activeJemaat.status : ''"></span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                  :class="activeJemaat ? getCategoryBadgeClass(activeJemaat.kategori) : ''"
                  x-text="activeJemaat && activeJemaat.kategori ? activeJemaat.kategori : ''"></span>
              </div>
              <p class="text-xs text-slate-500 mt-0.5">
                Profil jemaat dan catatan 10 riwayat kehadiran terakhir.
              </p>
            </div>
          </div>
          <button type="button" @click="showJemaatModal = false" class="text-slate-400 hover:text-slate-600 font-bold p-1">✕</button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6 overflow-y-auto max-h-[75vh] text-xs">
          
          <!-- Loading State -->
          <div x-show="loadingJemaat" class="py-12 text-center text-slate-400 space-y-2">
            <i class="ri-loader-4-line text-2xl animate-spin text-blue-600"></i>
            <p>Memuat profil dan riwayat kehadiran...</p>
          </div>

          <!-- Content when loaded -->
          <div x-show="!loadingJemaat && activeJemaat" class="space-y-6">
            
            <!-- Profil Info Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">
              <div>
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Usia & Tgl Lahir</span>
                <span class="font-bold text-slate-800 text-xs"
                  x-text="activeJemaat.usia ? `${activeJemaat.usia} th (${activeJemaat.tgl_lahir || '-'})` : (activeJemaat.tgl_lahir || '-')"></span>
              </div>
              <div>
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Tempat Lahir</span>
                <span class="font-bold text-slate-800 text-xs" x-text="activeJemaat.tempat_lahir || '-'"></span>
              </div>
              <div>
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Wilayah / Cabang</span>
                <span class="font-bold text-slate-800 text-xs" x-text="activeJemaat.cabang || '-'"></span>
              </div>
              <div>
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Telepon</span>
                <div class="flex items-center gap-1.5 mt-0.5">
                  <span class="font-bold text-slate-800 text-xs" x-text="activeJemaat.telepon || '-'"></span>
                  <template x-if="activeJemaat.telepon && activeJemaat.telepon !== '-'">
                    <a :href="`https://wa.me/${getCleanPhone(activeJemaat.telepon)}`" target="_blank"
                      class="text-emerald-600 hover:text-emerald-700 text-sm" title="Chat WhatsApp">
                      <i class="ri-whatsapp-line"></i>
                    </a>
                  </template>
                </div>
              </div>
              <div>
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Fokus Ibadah</span>
                <span class="font-bold text-slate-800 text-xs" x-text="activeJemaat.fokus_ibadah || '-'"></span>
              </div>
              <div>
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Total Kehadiran</span>
                <span class="font-extrabold text-blue-600 text-sm" x-text="`${activeJemaat.total_kehadiran || 0}x Sesi`"></span>
              </div>
              <div class="col-span-2 sm:col-span-3 pt-2 border-t border-slate-200/60">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Alamat</span>
                <span class="font-medium text-slate-700 text-xs" x-text="activeJemaat.alamat || '-'"></span>
              </div>
            </div>

            <!-- 10 History Kehadiran Table -->
            <div class="space-y-2.5">
              <div class="flex items-center justify-between">
                <h4 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                  <i class="ri-history-line text-blue-600"></i>
                  <span>10 Riwayat Kedatangan Terakhir</span>
                </h4>
                <span class="text-[10px] text-slate-400" x-text="`Ditemukan ${activeHistory.length} sesi`"></span>
              </div>

              <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left border-collapse text-xs">
                  <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 font-semibold text-slate-600 text-[11px]">
                      <th class="py-2.5 px-3 w-10 text-center">No</th>
                      <th class="py-2.5 px-3">Tanggal & Hari</th>
                      <th class="py-2.5 px-3">Ibadah & Sesi</th>
                      <th class="py-2.5 px-3">Lokasi</th>
                      <th class="py-2.5 px-3 text-center">Status</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                    <template x-for="(h, hIdx) in activeHistory" :key="hIdx">
                      <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-2 px-3 text-center text-slate-400 font-semibold" x-text="hIdx + 1"></td>
                        <td class="py-2 px-3">
                          <span class="font-bold text-slate-900 block" x-text="h.tanggal"></span>
                          <span class="text-[10px] text-slate-400" x-text="h.hari"></span>
                        </td>
                        <td class="py-2 px-3">
                          <span class="font-semibold text-slate-800 block" x-text="h.nama_ibadah"></span>
                          <span class="text-[10px] text-blue-600 font-semibold" x-text="`Sesi: ${h.nama_sesi || '-'}`"></span>
                        </td>
                        <td class="py-2 px-3 text-slate-600" x-text="h.nama_cabang"></td>
                        <td class="py-2 px-3 text-center">
                          <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800" x-text="h.status || 'Hadir'"></span>
                        </td>
                      </tr>
                    </template>
                    <tr x-show="activeHistory.length === 0">
                      <td colspan="5" class="py-6 text-center text-slate-400 text-xs">
                        Belum ada riwayat kehadiran tercatat.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>

        </div>

        <!-- Modal Footer -->
        <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
          <span class="text-[11px] text-slate-400">Data bersumber dari arsip absensi jemaat.</span>
          <button type="button" @click="showJemaatModal = false"
            class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs">
            Tutup
          </button>
        </div>

      </div>
    </div>

  </div>

</div>

<!-- Alpine Script for Filter & Table Management -->
<script>
  function attendanceDashboard() {
    return {
      periodType: '{{ $periodType }}',
      setPeriod(type) {
        this.periodType = type;
        const form = document.getElementById('filterForm');
        // biarkan Alpine re-render template sebelum submit
        setTimeout(() => {
          form.submit();
        }, 50);
      }
    };
  }

  function rincianTable() {
    return {
      activeTab: 'hadir',
      searchQuery: '',
      filterLokasi: 'all',
      filterKategori: 'all',
      listHadir: @json($listHadir),
      listTidakHadir: @json($listTidakHadir),
      listTamuBaru: @json($listTamuBaru),

      // Modal Detail Jemaat & 10 History Kedatangan
      showJemaatModal: false,
      loadingJemaat: false,
      activeJemaat: null,
      activeHistory: [],

      openJemaatDetail(id, fallbackName) {
        this.showJemaatModal = true;
        this.loadingJemaat = true;
        this.activeJemaat = null;
        this.activeHistory = [];

        if (typeof id === 'string' && id.startsWith('tamu_')) {
          this.activeJemaat = {
            id: id,
            nama_asli: fallbackName || 'Tamu',
            status: 'Tamu',
            kategori: 'Tamu',
            usia: '-',
            tgl_lahir: '-',
            tempat_lahir: '-',
            telepon: '-',
            alamat: 'Data tamu belum tercatat sebagai anggota tetap.',
            cabang: '-',
            fokus_ibadah: 'Ibadah Raya',
            total_kehadiran: 1
          };
          this.activeHistory = [];
          this.loadingJemaat = false;
          return;
        }

        fetch(`{{ url('/admin/attendance/jemaat') }}/${id}/detail`, {
          headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            this.activeJemaat = data.jemaat;
            this.activeHistory = data.history || [];
          } else {
            alert('Tidak dapat memuat data jemaat.');
            this.showJemaatModal = false;
          }
          this.loadingJemaat = false;
        })
        .catch(err => {
          alert('Terjadi kesalahan memuat data jemaat: ' + err.message);
          this.loadingJemaat = false;
          this.showJemaatModal = false;
        });
      },

      getCategoryBadgeClass(cat) {
        if (!cat) return 'bg-slate-100 text-slate-700 border border-slate-200';
        const c = cat.toLowerCase();
        if (c.includes('anak')) return 'bg-amber-100 text-amber-800 border border-amber-200';
        if (c.includes('remaja')) return 'bg-emerald-100 text-emerald-800 border border-emerald-200';
        if (c.includes('pemuda')) return 'bg-blue-100 text-blue-800 border border-blue-200';
        if (c.includes('dewasa')) return 'bg-indigo-100 text-indigo-800 border border-indigo-200';
        if (c.includes('lansia')) return 'bg-purple-100 text-purple-800 border border-purple-200';
        if (c.includes('tamu')) return 'bg-rose-100 text-rose-800 border border-rose-200';
        return 'bg-slate-100 text-slate-700 border border-slate-200';
      },

      getCleanPhone(phone) {
        if (!phone) return '';
        let clean = phone.replace(/[^0-9]/g, '');
        if (clean.startsWith('0')) {
          clean = '62' + clean.substring(1);
        }
        return clean;
      },

      get currentList() {
        if (this.activeTab === 'hadir') return this.listHadir;
        if (this.activeTab === 'tidak_hadir') return this.listTidakHadir;
        return this.listTamuBaru;
      },

      get filteredRows() {
        const q = this.searchQuery.toLowerCase().trim();
        const fLokasi = (this.filterLokasi || 'all').toLowerCase().trim();
        const fKategori = (this.filterKategori || 'all').toLowerCase().trim();

        return this.currentList.filter(item => {
          // Filter Lokasi
          if (fLokasi !== 'all') {
            const loc = (item.wilayah || '').toLowerCase();
            if (loc !== fLokasi) {
              return false;
            }
          }

          // Filter Kategori Usia
          if (fKategori !== 'all') {
            const kat = (item.kategori || '').toLowerCase();
            if (kat !== fKategori) {
              return false;
            }
          }

          // Live Search Query
          if (q) {
            const matchName = item.nama && item.nama.toLowerCase().includes(q);
            const matchKategori = item.kategori && item.kategori.toLowerCase().includes(q);
            const matchAlias = item.alias && item.alias.toLowerCase().includes(q);
            const matchWilayah = item.wilayah && item.wilayah.toLowerCase().includes(q);
            if (!matchName && !matchKategori && !matchAlias && !matchWilayah) {
              return false;
            }
          }

          return true;
        });
      },

      resetFilters() {
        this.filterLokasi = 'all';
        this.filterKategori = 'all';
        this.searchQuery = '';
      },

      printTable() {
        window.print();
      },

      exportCSV() {
        const rows = [
          ['No', 'Nama Jemaat', 'Kategori Usia', 'Status', 'Alias', 'Wilayah', 'Total Kehadiran', 'Terakhir Hadir']
        ];
        this.filteredRows.forEach((item, idx) => {
          rows.push([
            idx + 1,
            item.nama,
            item.kategori ? `${item.kategori} (${item.usia})` : '-',
            item.status,
            item.alias,
            item.wilayah,
            item.kehadiran,
            item.terakhir_hadir
          ]);
        });

        let csvContent = "data:text/csv;charset=utf-8,\uFEFF" 
          + rows.map(e => e.map(val => `"${String(val).replace(/"/g, '""')}"`).join(",")).join("\n");

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `rincian-kehadiran-${this.activeTab}-{{ date('Y-m-d') }}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      }
    };
  }
</script>
@endsection

