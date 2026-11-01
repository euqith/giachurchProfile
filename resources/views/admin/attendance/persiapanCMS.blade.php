@extends('admin.layoutCMS')

@section('content')
<div class="space-y-8 pb-16" x-data="worshipPreparation()">

  <!-- Page Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold mb-2">
        <i class="ri-music-2-line"></i> Liturgi & Pelayanan
      </div>
      <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Persiapan Ibadah & Pustaka Lagu</h1>
      <p class="text-xs text-slate-500 mt-0.5">Atur jadwal ibadah, penugasan pelayan firman/musik, dan susunan lagu pujian.</p>
    </div>
    <div class="flex items-center gap-2.5">
      <a href="{{ route('admin.attendance.createSchedule') }}"
        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition">
        <i class="ri-add-line text-sm"></i> Tambah Jadwal Ibadah
      </a>
    </div>
  </div>

  <!-- TOP BANNER: TAUTAN PUBLIK /ibadah (IMAGE-6) -->
  <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white p-5 sm:p-6 rounded-3xl shadow-xl shadow-blue-950/10 flex flex-col md:flex-row md:items-center justify-between gap-5 relative overflow-hidden">
    <div class="space-y-1 z-10">
      <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-500/30 text-blue-200 text-[11px] font-semibold border border-blue-400/20">
        <i class="ri-link text-xs"></i> Tautan Publik Jadwal Ibadah
      </div>
      <h3 class="text-base font-bold text-white tracking-tight">Halaman Akses Publik & Liturgi Pelayan</h3>
      <p class="text-xs text-blue-200/80 max-w-xl leading-relaxed">
        Jadwal ibadah dan susunan lagu dapat diakses langsung oleh jemaat dan tim pelayanan tanpa perlu login melalui link publik.
      </p>
    </div>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 z-10 shrink-0">
      <div class="bg-white/10 backdrop-blur-md px-3.5 py-2 rounded-xl text-xs font-mono text-blue-100 border border-white/15 select-all">
        {{ url('/ibadah') }}
      </div>
      <button type="button" @click="copyPublicUrl('{{ url('/ibadah') }}')"
        class="px-3.5 py-2 rounded-xl bg-white text-blue-900 hover:bg-blue-50 font-bold text-xs inline-flex items-center justify-center gap-1.5 shadow-sm transition">
        <i class="ri-file-copy-line text-sm"></i> Salin Tautan
      </button>
      <a href="{{ url('/ibadah') }}" target="_blank"
        class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs inline-flex items-center justify-center gap-1.5 shadow-sm transition">
        <i class="ri-external-link-line text-sm"></i> Buka Halaman
      </a>
    </div>

    <!-- Background glowing accents -->
    <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
  </div>

  <!-- SECTION 1: JADWAL IBADAH (IMAGE-6) -->
  <div class="space-y-4">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
      <div>
        <h2 class="text-lg font-bold text-slate-900">Jadwal Ibadah</h2>
        <p class="text-xs text-slate-500 mt-0.5">Susunan petugas, khotbah, dan liturgi musik ibadah.</p>
      </div>

      <!-- Controls: Tabs Mendatang / Arsip + Search + Excel -->
      <div class="flex flex-wrap items-center gap-2.5">
        <!-- Segmented Tab -->
        <div class="inline-flex p-1 bg-slate-100 rounded-xl text-xs font-semibold text-slate-600">
          <a href="{{ route('admin.attendance.persiapan', ['tab' => 'mendatang']) }}"
            class="px-3 py-1.5 rounded-lg transition {{ $tabJadwal !== 'arsip' ? 'bg-white text-blue-700 shadow-sm' : 'hover:text-slate-900' }}">
            Mendatang ({{ $tabJadwal !== 'arsip' ? count($schedules) : '•' }})
          </a>
          <a href="{{ route('admin.attendance.persiapan', ['tab' => 'arsip']) }}"
            class="px-3 py-1.5 rounded-lg transition {{ $tabJadwal === 'arsip' ? 'bg-white text-blue-700 shadow-sm' : 'hover:text-slate-900' }}">
            Arsip ({{ $tabJadwal === 'arsip' ? count($schedules) : '•' }})
          </a>
        </div>

        <!-- Search Jadwal -->
        <form method="GET" action="{{ route('admin.attendance.persiapan') }}" class="relative w-48 sm:w-60">
          <input type="hidden" name="tab" value="{{ $tabJadwal }}">
          <i class="ri-search-2-line text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 text-xs"></i>
          <input type="text" name="search_jadwal" value="{{ request('search_jadwal') }}"
            placeholder="Cari tema, pengkhotbah..."
            class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:outline-none focus:border-blue-500 bg-white">
        </form>

        <button type="button" @click="exportScheduleCSV()"
          class="px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center gap-1.5 transition">
          <i class="ri-download-2-line text-xs"></i> Ekspor Excel
        </button>
      </div>
    </div>

    <!-- Schedule Cards Grid / List -->
    <div class="space-y-3.5">
      @forelse($schedules as $sched)
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:border-blue-300 transition flex flex-col lg:flex-row lg:items-center justify-between gap-5">
          
          <div class="flex items-start sm:items-center gap-4">
            <!-- Date Box -->
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-blue-50 border border-blue-100 text-blue-700 flex flex-col items-center justify-center shrink-0">
              <span class="text-xs font-bold uppercase tracking-wider text-blue-600">
                {{ \Carbon\Carbon::parse($sched->tanggal)->translatedFormat('M') }}
              </span>
              <span class="text-2xl sm:text-3xl font-extrabold text-blue-900 leading-none my-0.5">
                {{ \Carbon\Carbon::parse($sched->tanggal)->format('d') }}
              </span>
              <span class="text-[10px] font-semibold text-blue-600">
                {{ \Carbon\Carbon::parse($sched->tanggal)->translatedFormat('D') }}
              </span>
            </div>

            <!-- Main Info -->
            <div class="space-y-1.5">
              <div class="flex flex-wrap items-center gap-2">
                <span class="font-extrabold text-slate-900 text-base">{{ $sched->nama_ibadah }}</span>
                @if($sched->nama_sesi)
                  <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-semibold">
                    {{ $sched->nama_sesi }}
                  </span>
                @endif
                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-medium flex items-center gap-1">
                  <i class="ri-map-pin-2-line text-slate-400"></i> {{ $sched->nama_cabang }}
                </span>
              </div>

              <!-- Tema & Ayat -->
              @if($sched->tema)
                <div class="text-xs font-semibold text-slate-800 flex items-center gap-1.5">
                  <i class="ri-book-open-line text-blue-600"></i>
                  <span>"{{ $sched->tema }}"</span>
                  @if($sched->ayat)
                    <span class="text-slate-500 font-normal">({{ $sched->ayat }})</span>
                  @endif
                </div>
              @endif

              <!-- Petugas Pelayan -->
              <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600">
                @if($sched->pengkhotbah)
                  <span class="inline-flex items-center gap-1">
                    <span class="text-slate-400">Khotbah:</span>
                    <strong class="text-slate-800">{{ $sched->pengkhotbah }}</strong>
                  </span>
                @endif
                @if($sched->pemimpin_pujian)
                  <span class="inline-flex items-center gap-1">
                    <span class="text-slate-400">WL:</span>
                    <strong class="text-slate-800">{{ $sched->pemimpin_pujian }}</strong>
                  </span>
                @endif
              </div>

              <!-- Susunan Lagu Preview Chips -->
              @if($sched->songs->isNotEmpty())
                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                  @foreach($sched->songs as $sIdx => $song)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-50 text-slate-700 border border-slate-200 text-[11px] font-medium">
                      <span class="text-blue-600 font-bold">{{ $sIdx + 1 }}.</span>
                      <span>{{ $song->judul_lagu }}</span>
                      @if($song->pivot->nada_dasar || $song->nada_dasar)
                        <span class="text-[10px] text-amber-700 bg-amber-50 px-1 rounded font-bold">
                          {{ $song->pivot->nada_dasar ?: $song->nada_dasar }}
                        </span>
                      @endif
                    </span>
                  @endforeach
                </div>
              @endif
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-2 self-end lg:self-center shrink-0">
            <a href="{{ route('admin.attendance.editSchedule', $sched->id) }}"
              class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs inline-flex items-center gap-1.5 transition">
              <i class="ri-edit-line text-xs"></i> Edit
            </a>
            <form method="POST" action="{{ route('admin.attendance.destroySchedule', $sched->id) }}"
              onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ibadah ini?')">
              @csrf
              @method('DELETE')
              <button type="submit"
                class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition" title="Hapus Jadwal">
                <i class="ri-delete-bin-line text-sm"></i>
              </button>
            </form>
          </div>

        </div>
      @empty
        <div class="bg-white p-12 rounded-2xl border border-slate-200/80 text-center text-slate-400 text-xs">
          <i class="ri-calendar-event-line text-3xl mb-2 block text-slate-300"></i>
          Belum ada jadwal ibadah {{ $tabJadwal === 'arsip' ? 'dalam arsip' : 'mendatang' }}.
        </div>
      @endforelse
    </div>
  </div>

  <!-- SECTION 2: PUSTAKA LAGU (IMAGE-7) -->
  <div class="space-y-4 pt-6 border-t border-slate-200/80">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <div class="flex items-center gap-2">
          <h2 class="text-lg font-bold text-slate-900">Pustaka Lagu</h2>
          <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-bold text-xs">
            {{ count($songs) }} Lagu Tersimpan
          </span>
        </div>
        <p class="text-xs text-slate-500 mt-0.5">Database lagu pujian dan penyembahan beserta nada dasar dan tautan referensi.</p>
      </div>

      <div class="flex items-center gap-2.5">
        <!-- Search Lagu -->
        <form method="GET" action="{{ route('admin.attendance.persiapan') }}" class="relative w-56 sm:w-72">
          <input type="hidden" name="tab" value="{{ $tabJadwal }}">
          <i class="ri-search-2-line text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 text-xs"></i>
          <input type="text" name="search_lagu" value="{{ request('search_lagu') }}"
            placeholder="Cari judul lagu, artis, nada..."
            class="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:outline-none focus:border-blue-500 bg-white">
        </form>

        <button type="button" @click="openAddSongModal()"
          class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-sm transition">
          <i class="ri-add-line text-sm"></i> Tambah Lagu
        </button>
      </div>
    </div>

    <!-- Table Pustaka Lagu (Image-7) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 font-semibold text-slate-600">
              <th class="py-3 px-4">JUDUL LAGU</th>
              <th class="py-3 px-4 text-center">NADA DASAR</th>
              <th class="py-3 px-4">BAGIAN TERAKHIR</th>
              <th class="py-3 px-4 text-center">DIPAKAI</th>
              <th class="py-3 px-4 text-center">TERAKHIR</th>
              <th class="py-3 px-4 text-center">TAUTAN</th>
              <th class="py-3 px-4 text-center w-24">AKSI</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-slate-700">
            @forelse($songs as $song)
              <tr class="hover:bg-slate-50/60 transition">
                <!-- Judul Lagu & Artis -->
                <td class="py-3 px-4">
                  <div class="font-bold text-slate-900 text-sm">{{ $song->judul_lagu }}</div>
                  @if($song->artis_sumber)
                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $song->artis_sumber }}</div>
                  @endif
                </td>

                <!-- Nada Dasar -->
                <td class="py-3 px-4 text-center">
                  <span class="inline-block px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 font-extrabold text-xs border border-amber-200/60">
                    {{ $song->nada_dasar ?: '-' }}
                  </span>
                </td>

                <!-- Bagian Terakhir -->
                <td class="py-3 px-4">
                  <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">
                    {{ $song->bagian_terakhir ?: 'Pujian & Penyembahan' }}
                  </span>
                </td>

                <!-- Dipakai Counter -->
                <td class="py-3 px-4 text-center">
                  <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700">
                    {{ $song->dipakai_count }}x
                  </span>
                </td>

                <!-- Terakhir Dipakai -->
                <td class="py-3 px-4 text-center text-slate-500 whitespace-nowrap">
                  {{ $song->terakhir_dipakai ?: '-' }}
                </td>

                <!-- Tautan (YouTube, Chord, Lirik) -->
                <td class="py-3 px-4 text-center">
                  <div class="inline-flex items-center gap-1.5">
                    @if($song->youtube_url)
                      <a href="{{ $song->youtube_url }}" target="_blank"
                        class="w-6 h-6 rounded-md bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition" title="Lihat di YouTube">
                        <i class="ri-youtube-line text-xs"></i>
                      </a>
                    @endif
                    @if($song->chord_url)
                      <a href="{{ $song->chord_url }}" target="_blank"
                        class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition" title="Chord Musik">
                        <i class="ri-file-music-line text-xs"></i>
                      </a>
                    @endif
                    @if($song->lirik_teks || $song->lirik_url)
                      <button type="button" @click="viewLyrics(@json($song))"
                        class="w-6 h-6 rounded-md bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition" title="Lihat Lirik">
                        <i class="ri-file-text-line text-xs"></i>
                      </button>
                    @endif
                  </div>
                </td>

                <!-- Aksi -->
                <td class="py-3 px-4 text-center">
                  <div class="inline-flex items-center gap-1">
                    <button type="button" @click="openEditSongModal(@json($song))"
                      class="p-1 text-slate-400 hover:text-blue-600 transition" title="Edit Lagu">
                      <i class="ri-edit-line text-sm"></i>
                    </button>
                    <form method="POST" action="{{ route('admin.attendance.destroySong', $song->id) }}"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus lagu ini dari pustaka?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition" title="Hapus Lagu">
                        <i class="ri-delete-bin-line text-sm"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="py-10 text-center text-slate-400 text-xs">
                  Belum ada lagu yang tersimpan di pustaka.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ==================== MODAL TAMBAH / EDIT JADWAL ==================== -->
  <div x-show="isScheduleModalOpen" style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]"
      @click.outside="isScheduleModalOpen = false">
      
      <form :action="scheduleFormMode === 'edit' ? `/admin/attendance/persiapan/schedule/${scheduleFormData.id}` : '{{ route('admin.attendance.storeSchedule') }}'" method="POST">
        @csrf
        <template x-if="scheduleFormMode === 'edit'">
          <input type="hidden" name="_method" value="PUT">
        </template>

        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <div>
            <h3 class="text-base font-bold text-slate-900" x-text="scheduleFormMode === 'edit' ? 'Edit Jadwal Ibadah' : 'Tambah Jadwal Ibadah Baru'"></h3>
            <p class="text-xs text-slate-500 mt-0.5">Tentukan waktu ibadah, tema khotbah, dan susunan lagu liturgi</p>
          </div>
          <button type="button" @click="isScheduleModalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
        </div>

        <div class="p-5 sm:p-6 overflow-y-auto space-y-4 max-h-[70vh]">
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] font-bold text-slate-600 mb-1">Tanggal Ibadah *</label>
              <input type="date" name="tanggal" x-model="scheduleFormData.tanggal" required
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500">
            </div>

            <div>
              <label class="block text-[11px] font-bold text-slate-600 mb-1">Jenis Ibadah *</label>
              <select name="nama_ibadah" x-model="scheduleFormData.nama_ibadah" required
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500 bg-white">
                @foreach($jenisIbadahs as $j)
                  <option value="{{ $j->nama_ibadah }}">{{ $j->nama_ibadah }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-[11px] font-bold text-slate-600 mb-1">Sesi Ibadah</label>
              <select name="nama_sesi" x-model="scheduleFormData.nama_sesi"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500 bg-white">
                <option value="">-- Tanpa Sesi --</option>
                @foreach($sesis as $s)
                  <option value="{{ $s->nama_sesi }}">{{ $s->nama_sesi }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-[11px] font-bold text-slate-600 mb-1">Lokasi Cabang *</label>
              <select name="nama_cabang" x-model="scheduleFormData.nama_cabang" required
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500 bg-white">
                @foreach($cabangs as $c)
                  <option value="{{ $c->nama_cabang }}">{{ $c->nama_cabang }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-100">
            <div>
              <label class="block text-[11px] font-bold text-slate-600 mb-1">Tema Khotbah</label>
              <input type="text" name="tema" x-model="scheduleFormData.tema" placeholder="cth: Kasih yang Memulihkan"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500">
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-600 mb-1">Ayat Alkitab</label>
              <input type="text" name="ayat" x-model="scheduleFormData.ayat" placeholder="cth: Yohanes 3:16"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500">
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-600 mb-1">Pelayan Firman / Pengkhotbah</label>
              <input type="text" name="pengkhotbah" x-model="scheduleFormData.pengkhotbah" placeholder="Nama pendeta / pembicara"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500">
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-600 mb-1">Pemimpin Pujian / Worship Leader</label>
              <input type="text" name="pemimpin_pujian" x-model="scheduleFormData.pemimpin_pujian" placeholder="Nama WL"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500">
            </div>
          </div>

          <!-- Dynamic Song Liturgy Selection -->
          <div class="pt-3 border-t border-slate-100">
            <div class="flex items-center justify-between mb-2">
              <label class="block text-[11px] font-bold text-slate-700">Susunan Lagu Pujian (Liturgi)</label>
              <button type="button" @click="addSongRow()" class="text-xs text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1">
                <i class="ri-add-circle-line"></i> + Tambah Lagu
              </button>
            </div>

            <div class="space-y-2">
              <template x-for="(sRow, rIdx) in scheduleFormData.songs" :key="rIdx">
                <div class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center gap-2">
                  <span class="text-xs font-bold text-slate-400 w-5 text-center" x-text="rIdx + 1"></span>
                  
                  <select :name="`songs[${rIdx}][song_id]`" x-model="sRow.song_id"
                    class="flex-1 w-full sm:w-auto px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs bg-white">
                    <option value="">-- Pilih Lagu dari Pustaka --</option>
                    @foreach($songs as $sg)
                      <option value="{{ $sg->id }}">{{ $sg->judul_lagu }} ({{ $sg->nada_dasar ?: '-' }})</option>
                    @endforeach
                  </select>

                  <select :name="`songs[${rIdx}][bagian_liturgi]`" x-model="sRow.bagian_liturgi"
                    class="w-full sm:w-36 px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs bg-white">
                    <option value="Pujian Pembuka">Pujian Pembuka</option>
                    <option value="Penyembahan">Penyembahan</option>
                    <option value="Firman Tuhan">Firman Tuhan</option>
                    <option value="Persembahan">Persembahan</option>
                    <option value="Penutup / Berkat">Penutup / Berkat</option>
                  </select>

                  <input type="text" :name="`songs[${rIdx}][nada_dasar]`" x-model="sRow.nada_dasar" placeholder="Nada (C)"
                    class="w-full sm:w-20 px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs bg-white text-center">

                  <button type="button" @click="removeSongRow(rIdx)"
                    class="w-7 h-7 rounded-lg text-rose-500 hover:bg-rose-50 flex items-center justify-center">
                    <i class="ri-delete-bin-line text-xs"></i>
                  </button>
                </div>
              </template>
            </div>
          </div>

        </div>

        <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-2">
          <button type="button" @click="isScheduleModalOpen = false"
            class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold">
            Batal
          </button>
          <button type="submit"
            class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20">
            Simpan Jadwal
          </button>
        </div>

      </form>
    </div>
  </div>

  <!-- ==================== MODAL TAMBAH / EDIT LAGU ==================== -->
  <div x-show="isSongModalOpen" style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl border border-slate-200 overflow-hidden flex flex-col"
      @click.outside="isSongModalOpen = false">
      
      <form :action="songFormMode === 'edit' ? `/admin/attendance/persiapan/song/${songFormData.id}` : '{{ route('admin.attendance.storeSong') }}'" method="POST">
        @csrf
        <template x-if="songFormMode === 'edit'">
          <input type="hidden" name="_method" value="PUT">
        </template>

        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <div>
            <h3 class="text-base font-bold text-slate-900" x-text="songFormMode === 'edit' ? 'Edit Lagu' : 'Tambah Lagu Baru'"></h3>
            <p class="text-xs text-slate-500 mt-0.5">Informasi judul, artis, nada dasar, dan tautan lagu</p>
          </div>
          <button type="button" @click="isSongModalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
        </div>

        <div class="p-5 sm:p-6 space-y-3.5 max-h-[75vh] overflow-y-auto">
          <div>
            <label class="block text-[11px] font-bold text-slate-600 mb-1">Judul Lagu *</label>
            <input type="text" name="judul_lagu" x-model="songFormData.judul_lagu" required placeholder="cth: Sentuh Hatiku"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500">
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] font-bold text-slate-600 mb-1">Artis / Pencipta</label>
              <input type="text" name="artis_sumber" x-model="songFormData.artis_sumber" placeholder="cth: NDC Worship"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500">
            </div>

            <div>
              <label class="block text-[11px] font-bold text-slate-600 mb-1">Nada Dasar (Default)</label>
              <input type="text" name="nada_dasar" x-model="songFormData.nada_dasar" placeholder="cth: G / C"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500">
            </div>
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-600 mb-1">Bagian Liturgi Default</label>
            <select name="bagian_terakhir" x-model="songFormData.bagian_terakhir"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500 bg-white">
              <option value="Pujian Pembuka">Pujian Pembuka</option>
              <option value="Penyembahan">Penyembahan</option>
              <option value="Firman Tuhan">Firman Tuhan</option>
              <option value="Persembahan">Persembahan</option>
              <option value="Penutup / Berkat">Penutup / Berkat</option>
            </select>
          </div>

          <div class="space-y-2 pt-2 border-t border-slate-100">
            <label class="block text-[11px] font-bold text-slate-700">Tautan & Referensi</label>
            
            <input type="url" name="youtube_url" x-model="songFormData.youtube_url" placeholder="https://youtube.com/watch?v=..."
              class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500">

            <input type="url" name="chord_url" x-model="songFormData.chord_url" placeholder="https://chords.com/... atau link chord chart"
              class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500">
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-600 mb-1">Teks Lirik (Opsional)</label>
            <textarea name="lirik_teks" x-model="songFormData.lirik_teks" rows="4" placeholder="Tuliskan lirik lagu di sini..."
              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500"></textarea>
          </div>
        </div>

        <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-2">
          <button type="button" @click="isSongModalOpen = false"
            class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold">
            Batal
          </button>
          <button type="submit"
            class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20">
            Simpan Lagu
          </button>
        </div>

      </form>
    </div>
  </div>

  <!-- ==================== MODAL PREVIEW LIRIK ==================== -->
  <div x-show="isLyricsModalOpen" style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl border border-slate-200 overflow-hidden flex flex-col"
      @click.outside="isLyricsModalOpen = false">
      <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <div>
          <h3 class="text-sm font-bold text-slate-900" x-text="activeSongLyrics.judul_lagu"></h3>
          <p class="text-xs text-slate-500" x-text="activeSongLyrics.artis_sumber"></p>
        </div>
        <button type="button" @click="isLyricsModalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
      </div>
      <div class="p-5 overflow-y-auto max-h-96">
        <pre class="text-xs font-sans text-slate-700 whitespace-pre-wrap leading-relaxed" x-text="activeSongLyrics.lirik_teks || 'Belum ada teks lirik untuk lagu ini.'"></pre>
      </div>
      <div class="p-3 border-t border-slate-100 bg-slate-50 flex justify-end">
        <button type="button" @click="isLyricsModalOpen = false"
          class="px-4 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold">Tutup</button>
      </div>
    </div>
  </div>

</div>

<!-- Alpine Script for Worship Preparation -->
<script>
  function worshipPreparation() {
    return {
      isScheduleModalOpen: false,
      scheduleFormMode: 'create',
      scheduleFormData: {
        id: null,
        tanggal: '{{ date('Y-m-d') }}',
        nama_ibadah: '{{ $jenisIbadahs->first()->nama_ibadah ?? 'Ibadah Raya 1' }}',
        nama_sesi: 'Pagi',
        nama_cabang: '{{ $cabangs->first()->nama_cabang ?? 'Darmo' }}',
        tema: '',
        ayat: '',
        pengkhotbah: '',
        pemimpin_pujian: '',
        songs: []
      },

      isSongModalOpen: false,
      songFormMode: 'create',
      songFormData: {
        id: null,
        judul_lagu: '',
        artis_sumber: '',
        nada_dasar: 'G',
        bagian_terakhir: 'Pujian Pembuka',
        youtube_url: '',
        chord_url: '',
        lirik_teks: ''
      },

      isLyricsModalOpen: false,
      activeSongLyrics: {},

      copyPublicUrl(url) {
        navigator.clipboard.writeText(url).then(() => {
          alert('Tautan halaman publik ibadah berhasil disalin:\n' + url);
        });
      },

      openAddScheduleModal() {
        this.scheduleFormMode = 'create';
        this.scheduleFormData = {
          id: null,
          tanggal: '{{ date('Y-m-d') }}',
          nama_ibadah: '{{ $jenisIbadahs->first()->nama_ibadah ?? 'Ibadah Raya 1' }}',
          nama_sesi: 'Pagi',
          nama_cabang: '{{ $cabangs->first()->nama_cabang ?? 'Darmo' }}',
          tema: '',
          ayat: '',
          pengkhotbah: '',
          pemimpin_pujian: '',
          songs: [
            { song_id: '', bagian_liturgi: 'Pujian Pembuka', nada_dasar: '' },
            { song_id: '', bagian_liturgi: 'Penyembahan', nada_dasar: '' }
          ]
        };
        this.isScheduleModalOpen = true;
      },

      openEditScheduleModal(scheduleId) {
        this.scheduleFormMode = 'edit';
        fetch(`/admin/attendance/persiapan/schedule/${scheduleId}`)
          .then(res => res.json())
          .then(data => {
            this.scheduleFormData = {
              id: data.id,
              tanggal: data.tanggal,
              nama_ibadah: data.nama_ibadah,
              nama_sesi: data.nama_sesi || '',
              nama_cabang: data.nama_cabang,
              tema: data.tema || '',
              ayat: data.ayat || '',
              pengkhotbah: data.pengkhotbah || '',
              pemimpin_pujian: data.pemimpin_pujian || '',
              songs: (data.schedule_songs || []).map(s => ({
                song_id: s.song_id,
                bagian_liturgi: s.bagian_liturgi,
                nada_dasar: s.nada_dasar || ''
              }))
            };
            this.isScheduleModalOpen = true;
          });
      },

      addSongRow() {
        this.scheduleFormData.songs.push({
          song_id: '',
          bagian_liturgi: 'Pujian & Penyembahan',
          nada_dasar: ''
        });
      },

      removeSongRow(idx) {
        this.scheduleFormData.songs.splice(idx, 1);
      },

      openAddSongModal() {
        this.songFormMode = 'create';
        this.songFormData = {
          id: null,
          judul_lagu: '',
          artis_sumber: '',
          nada_dasar: 'G',
          bagian_terakhir: 'Pujian Pembuka',
          youtube_url: '',
          chord_url: '',
          lirik_teks: ''
        };
        this.isSongModalOpen = true;
      },

      openEditSongModal(song) {
        this.songFormMode = 'edit';
        this.songFormData = {
          id: song.id,
          judul_lagu: song.judul_lagu,
          artis_sumber: song.artis_sumber || '',
          nada_dasar: song.nada_dasar || '',
          bagian_terakhir: song.bagian_terakhir || 'Pujian Pembuka',
          youtube_url: song.youtube_url || '',
          chord_url: song.chord_url || '',
          lirik_teks: song.lirik_teks || ''
        };
        this.isSongModalOpen = true;
      },

      viewLyrics(song) {
        this.activeSongLyrics = song;
        this.isLyricsModalOpen = true;
      },

      exportScheduleCSV() {
        const rows = [
          ['Tanggal', 'Jenis Ibadah', 'Sesi', 'Lokasi Cabang', 'Tema', 'Ayat', 'Pengkhotbah', 'WL / Pemimpin Pujian']
        ];
        @foreach($schedules as $sc)
          rows.push([
            "{{ $sc->tanggal }}",
            "{{ $sc->nama_ibadah }}",
            "{{ $sc->nama_sesi ?: '-' }}",
            "{{ $sc->nama_cabang }}",
            "{{ $sc->tema ?: '-' }}",
            "{{ $sc->ayat ?: '-' }}",
            "{{ $sc->pengkhotbah ?: '-' }}",
            "{{ $sc->pemimpin_pujian ?: '-' }}"
          ]);
        @endforeach

        let csvContent = "data:text/csv;charset=utf-8,\uFEFF" 
          + rows.map(e => e.map(val => `"${String(val).replace(/"/g, '""')}"`).join(",")).join("\n");

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `jadwal-ibadah-{{ $tabJadwal }}-{{ date('Y-m-d') }}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      }
    };
  }
</script>
@endsection

