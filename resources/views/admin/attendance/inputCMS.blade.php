@extends('admin.layoutCMS')

@section('content')
  <div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
      <div>
        <h1 class="text-xl font-bold text-slate-800">Input Kehadiran</h1>
        <p class="text-xs text-slate-500 mt-0.5">Catat kehadiran jemaat per sesi ibadah</p>
      </div>
      <div class="text-right text-xs text-slate-400">
        <span class="font-semibold text-slate-700">Gereja Isa Almasih Surabaya</span>
        <div>{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</div>
      </div>
    </div>

    <!-- 1. Pengaturan Sesi Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
      <h5 class="font-bold text-slate-800 text-sm flex items-center gap-2">
        <i class="ri-calendar-event-line text-blue-600"></i> Pengaturan Sesi
      </h5>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-xs">
        <!-- Tanggal -->
        <div>
          <label class="block font-semibold text-slate-600 mb-1.5">Tanggal</label>
          <input type="date" id="session-date" value="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d') }}"
            onchange="updateSessionInfo()"
            class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 outline-none focus:border-blue-600 bg-white">
        </div>

        <!-- Jenis Ibadah -->
        <div>
          <label class="block font-semibold text-slate-600 mb-1.5">Jenis Ibadah</label>
          <select id="session-ibadah" name="nama_ibadah" onchange="updateSessionInfo()" required
            class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 outline-none focus:border-blue-600 bg-white">
            <option value="" disabled selected>-- Pilih Jenis Ibadah --</option>
            @foreach ($jenisIbadahs ?? [] as $ibadah)
              <option value="{{ $ibadah->nama_ibadah }}">{{ $ibadah->nama_ibadah }}</option>
            @endforeach
          </select>
        </div>

        <!-- Field Sesi (Pagi, Siang, Sore) -->
        <div>
          <label class="block font-semibold text-slate-600 mb-1.5">Sesi</label>
          <select id="session-waktu" name="nama_sesi" onchange="updateSessionInfo()" required
            class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 outline-none focus:border-blue-600 bg-white font-medium">
            <option value="Pagi" selected>Pagi</option>
            <option value="Siang">Siang</option>
            <option value="Sore">Sore</option>
            @foreach ($sesis ?? [] as $s)
              @if (!in_array($s->nama_sesi, ['Pagi', 'Siang', 'Sore']))
                <option value="{{ $s->nama_sesi }}">{{ $s->nama_sesi }}</option>
              @endif
            @endforeach
          </select>
        </div>

        <!-- Lokasi -->
        <div>
          <label class="block font-semibold text-slate-600 mb-1.5">Lokasi</label>
          <select id="session-lokasi" name="nama_cabang" onchange="updateSessionInfo()" required
            class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 outline-none focus:border-blue-600 bg-white">
            <option value="" disabled selected>-- Pilih Lokasi Cabang --</option>
            @foreach ($cabangs ?? [] as $cabang)
              <option value="{{ $cabang->nama_cabang }}" data-id="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
            @endforeach
          </select>
        </div>

        <!-- Nama Petugas -->
        <div>
          <label class="block font-semibold text-slate-600 mb-1.5">Nama Petugas</label>
          <input type="text" id="session-petugas" value="{{ Auth::user()->name ?? 'Christian' }}"
            onchange="updateSessionInfo()"
            placeholder="Nama petugas pencatat..."
            class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 bg-white outline-none focus:border-blue-600">
        </div>
      </div>
    </div>

    <!-- 2. Pencatatan Kehadiran Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">

      <!-- Header & Counter -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
        <div>
          <h5 class="font-bold text-slate-800 text-sm flex items-center gap-2">
            <i class="ri-user-add-line text-blue-600"></i> Pencatatan Kehadiran
          </h5>
          <p class="text-[11px] text-slate-400 mt-0.5">
            Ketik nama / alias → Enter → ketik lagi. Panah ↑↓ untuk memilih saran lain. Nama yang tidak dikenal bisa langsung ditambah sebagai tamu.
          </p>
        </div>

        <div class="text-right">
          <div id="total-count" class="text-3xl font-bold text-blue-900 leading-none">0</div>
          <div id="status-count" class="text-[11px] text-slate-400 font-medium mt-1">0 anggota + 0 tamu</div>
        </div>
      </div>

      <!-- Mode Sentuh Toggle -->
      <div class="flex items-center justify-between flex-wrap gap-3">
        <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
          <input type="checkbox" id="mode-sentuh" onchange="toggleTouchMode()" class="sr-only peer">
          <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 relative"></div>
          <span class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
            <i class="ri-tablet-line text-slate-500"></i>
            Mode Sentuh (tablet): tombol besar per wilayah
          </span>
        </label>
      </div>

      <!-- Input Search & Auto-Suggest Box -->
      <div class="relative">
        <div class="flex items-center w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-600/20 transition-all">
          <i class="ri-search-line text-slate-400 text-base mr-2.5 shrink-0"></i>
          <input type="text" id="search-jemaat" placeholder="Ketik nama atau alias, lalu tekan Enter..."
            autocomplete="off" class="w-full text-xs text-slate-800 bg-transparent outline-none border-none">
        </div>

        <!-- Dropdown Hasil Pencarian -->
        <div id="suggestions-box"
          class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-60 overflow-y-auto hidden divide-y divide-slate-100">
        </div>
      </div>

      <!-- MODE NORMAL: Container List Kehadiran Standar -->
      <div id="standard-mode-container" class="space-y-3">
        <div id="attendance-wrapper" class="border border-slate-200 rounded-xl overflow-hidden bg-white max-h-[420px] overflow-y-auto">
          <div id="attendance-list" class="divide-y divide-slate-100 min-h-[100px]">
            <!-- Empty State Default -->
            <div id="empty-state" class="py-12 text-center flex flex-col items-center justify-center text-slate-400 space-y-2">
              <i class="ri-user-search-line text-4xl text-slate-300"></i>
              <p class="text-xs">Belum ada yang dicatat. Ketik nama di kolom pencarian lalu tekan Enter.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- MODE SENTUH TABLET: Filter Wilayah & Grid Kartu Sentuh -->
      <div id="touch-mode-container" class="space-y-4 hidden">
        <!-- Wilayah Filter Tabs & Toggle Keluarga -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-100 pb-3">
          <!-- Wilayah Tabs -->
          <div class="flex items-center gap-1.5 overflow-x-auto pb-1 max-w-full text-xs" id="wilayah-tabs-container">
            <button type="button" onclick="selectWilayahTab('all')" data-wilayah="all"
              class="wilayah-tab px-4 py-2 rounded-xl font-bold bg-blue-900 text-white shadow-sm transition shrink-0">
              Semua Wilayah <span id="count-tab-all" class="ml-1 opacity-80 text-[10px]">0</span>
            </button>
            @foreach ($cabangs ?? [] as $cabang)
              <button type="button" onclick="selectWilayahTab('{{ $cabang->id }}')" data-wilayah="{{ $cabang->id }}"
                class="wilayah-tab px-3.5 py-2 rounded-xl font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition shrink-0">
                {{ $cabang->nama_cabang }} <span id="count-tab-{{ $cabang->id }}" class="ml-1 text-slate-400 text-[10px]">0</span>
              </button>
            @endforeach
            <button type="button" onclick="selectWilayahTab('none')" data-wilayah="none"
              class="wilayah-tab px-3.5 py-2 rounded-xl font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition shrink-0">
              Tanpa Wilayah <span id="count-tab-none" class="ml-1 text-slate-400 text-[10px]">0</span>
            </button>
          </div>

          <!-- Toggle Kelompokkan per Keluarga -->
          <label class="inline-flex items-center gap-2 cursor-pointer select-none shrink-0 self-end md:self-auto">
            <input type="checkbox" id="toggle-keluarga" checked onchange="renderTouchModeGrid()" class="sr-only peer">
            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600 relative"></div>
            <span class="text-xs font-semibold text-slate-700 flex items-center gap-1">
              <i class="ri-group-line text-slate-500"></i> Kelompokkan per keluarga
            </span>
          </label>
        </div>

        <!-- Touch Grid Container -->
        <div id="touch-cards-grid" class="min-h-[200px]">
          <!-- Di-render dinamis via JS -->
        </div>
      </div>

      <!-- Bottom Action Bar -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-100">
        <div class="flex items-center gap-2 w-full sm:w-auto">
          <button type="button" onclick="saveAndShowWAModal()"
            style="background-color: #1e3a8a !important; color: #ffffff !important;"
            class="px-5 py-2.5 rounded-xl text-white text-xs font-bold shadow-md hover:bg-blue-900 transition flex items-center gap-2 active:scale-95">
            <i class="ri-save-line text-sm"></i> Simpan Kehadiran
          </button>

          <button type="button" onclick="clearList()"
            class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition flex items-center gap-2 active:scale-95">
            <i class="ri-delete-bin-line text-slate-500 text-sm"></i> Bersihkan Daftar
          </button>
        </div>

        <!-- Running Session Info Text -->
        <div id="session-info-text" class="text-xs text-slate-500 font-medium text-right">
          <!-- Diperbarui secara otomatis -->
        </div>
      </div>

    </div>

  </div>

  <!-- MODAL RINGKASAN WHATSAPP -->
  <div id="waReportModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-3xl w-full max-w-lg shadow-2xl mx-4 relative">

      <!-- Header Modal -->
      <div class="flex justify-between items-start mb-1">
        <div class="flex items-center gap-2.5">
          <i class="ri-whatsapp-line text-emerald-500 text-2xl font-semibold"></i>
          <h3 class="font-bold text-slate-900 text-lg">Ringkasan untuk WhatsApp</h3>
        </div>
        <button type="button" onclick="closeWAModal()"
          class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
      </div>
      <p class="text-xs text-slate-500 mb-5 pl-8">Salin lalu tempel ke grup WhatsApp jemaat/pengurus.</p>

      <!-- Kirim Langsung Ke Box -->
      <div class="bg-emerald-50/60 border border-emerald-200/80 rounded-2xl p-4 mb-4 space-y-3">
        <label class="block text-xs font-bold text-emerald-950">Kirim langsung ke</label>
        <div class="flex items-center gap-2">
          <select id="wa-contact-select"
            class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 outline-none focus:border-emerald-500">
            <option value="6281703872525">Pengurus Ibadah · +62 81703872525</option>
            <option value="6281234567890">Admin Utama · +62 81234567890</option>
          </select>
          <button type="button" onclick="sendDirectWA()" style="background-color: #059669 !important;"
            class="shrink-0 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
            <i class="ri-send-plane-fill"></i> Kirim WA
          </button>
        </div>
        <p class="text-[11px] text-emerald-800/80 leading-tight">
          Pesan akan terbuka di WhatsApp dengan teks terisi; tekan kirim di WhatsApp.
        </p>
      </div>

      <!-- Toggle Switch Sertakan Daftar Nama -->
      <div class="flex items-center gap-3 mb-4 cursor-pointer select-none" onclick="toggleSwitchNames()">
        <div id="switch-bg"
          class="w-12 h-6 bg-slate-300 rounded-full p-1 transition-colors duration-200 flex items-center">
          <div id="switch-dot"
            class="w-4 h-4 bg-white rounded-full shadow-md transform transition-transform duration-200"></div>
        </div>
        <input type="checkbox" id="toggle-include-names" class="hidden" onchange="generateWAText()">
        <span class="text-xs font-semibold text-slate-700">Sertakan daftar nama yang hadir</span>
      </div>

      <!-- Textarea Pratinjau Teks Laporan WA -->
      <div class="mb-5">
        <textarea id="wa-text-preview" rows="7" readonly
          class="w-full text-xs font-mono bg-slate-50 border border-slate-200 rounded-2xl p-4 text-slate-800 outline-none resize-none"></textarea>
      </div>

      <!-- Footer Action Buttons -->
      <div class="flex items-center justify-end gap-2.5">
        <button type="button" onclick="closeWAModal()"
          class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
          Tutup
        </button>
        <button type="button" onclick="openWAPicker()"
          class="px-4 py-2.5 rounded-xl border border-emerald-500 text-emerald-700 bg-white hover:bg-emerald-50 text-xs font-semibold transition flex items-center gap-1.5">
          <i class="ri-whatsapp-line text-emerald-600 text-sm"></i> Pilih Kontak di WA
        </button>
        <button type="button" onclick="copyWAText()"
          class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition flex items-center gap-1.5">
          <i class="ri-file-copy-line text-slate-600 text-sm"></i> Salin
        </button>
      </div>

    </div>
  </div>

  <!-- JavaScript Interaktif Presensi + Mode Sentuh Tablet + Dynamic Session Draft -->
  <script>
    const masterJemaat = @json($jemaats ?? []);
    const masterCabang = @json($cabangs ?? []);

    // Elemen Form Sesi
    const sessionDateInput = document.getElementById('session-date');
    const sessionIbadahSelect = document.getElementById('session-ibadah');
    const sessionSesiSelect = document.getElementById('session-waktu');
    const sessionLokasiSelect = document.getElementById('session-lokasi');
    const sessionPetugasInput = document.getElementById('session-petugas');

    // Elemen Input Search & List Kehadiran
    const searchInput = document.getElementById('search-jemaat');
    const suggestionsBox = document.getElementById('suggestions-box');
    const attendanceList = document.getElementById('attendance-list');
    const emptyState = document.getElementById('empty-state');
    const totalCountEl = document.getElementById('total-count');
    const statusCountEl = document.getElementById('status-count');
    const sessionInfoText = document.getElementById('session-info-text');

    // Elemen Mode Sentuh
    const modeSentuhCheckbox = document.getElementById('mode-sentuh');
    const standardContainer = document.getElementById('standard-mode-container');
    const touchContainer = document.getElementById('touch-mode-container');
    const touchCardsGrid = document.getElementById('touch-cards-grid');
    const toggleKeluarga = document.getElementById('toggle-keluarga');

    let presentList = [];
    let activeWilayahTab = 'all';

    // 1. INSIALISASI: Load saved session state
    restoreSavedSession();

    function getSessionKey() {
      const date = sessionDateInput.value || 'nodate';
      const ibadah = sessionIbadahSelect.value || 'noibadah';
      const sesi = sessionSesiSelect ? sessionSesiSelect.value : 'Pagi';
      const lokasi = sessionLokasiSelect.value || 'nolokasi';
      return `attendance_draft_${date}_${ibadah}_${sesi}_${lokasi}`;
    }

    function saveSessionState() {
      const sessionMeta = {
        date: sessionDateInput.value,
        ibadah: sessionIbadahSelect.value,
        sesi: sessionSesiSelect ? sessionSesiSelect.value : 'Pagi',
        lokasi: sessionLokasiSelect.value,
        petugas: sessionPetugasInput.value
      };
      localStorage.setItem('active_attendance_session_meta', JSON.stringify(sessionMeta));

      const key = getSessionKey();
      localStorage.setItem(key, JSON.stringify(presentList));
    }

    function restoreSavedSession() {
      try {
        const savedMeta = localStorage.getItem('active_attendance_session_meta');
        if (savedMeta) {
          const meta = JSON.parse(savedMeta);
          if (meta.date) sessionDateInput.value = meta.date;
          if (meta.ibadah) sessionIbadahSelect.value = meta.ibadah;
          if (meta.sesi && sessionSesiSelect) sessionSesiSelect.value = meta.sesi;
          if (meta.lokasi) sessionLokasiSelect.value = meta.lokasi;
          if (meta.petugas) sessionPetugasInput.value = meta.petugas;
        }
      } catch (e) {
        console.error("Gagal membaca meta sesi:", e);
      }

      loadAttendanceForCurrentSession();
      updateSessionInfoTextOnly();
    }

    function loadAttendanceForCurrentSession() {
      const key = getSessionKey();

      // Cek draft lokal di LocalStorage
      try {
        const savedList = localStorage.getItem(key);
        if (savedList) {
          const parsed = JSON.parse(savedList);
          if (Array.isArray(parsed) && parsed.length > 0) {
            presentList = parsed;
            updateAttendanceListUI();
            return;
          }
        }
      } catch (e) {
        console.error("Gagal baca LocalStorage:", e);
      }

      // Jika kosong, cek ke database
      const dateVal = sessionDateInput.value;
      const ibadahVal = sessionIbadahSelect.value;
      const sesiVal = sessionSesiSelect ? sessionSesiSelect.value : 'Pagi';
      const lokasiVal = sessionLokasiSelect.value;

      if (dateVal && ibadahVal && lokasiVal) {
        fetch(`{{ route('admin.attendance.get-draft') }}?date=${dateVal}&ibadah=${encodeURIComponent(ibadahVal)}&sesi=${encodeURIComponent(sesiVal)}&lokasi=${encodeURIComponent(lokasiVal)}`)
          .then(res => res.json())
          .then(data => {
            if (data.success && data.details && data.details.length > 0) {
              presentList = data.details.map(d => ({
                id: d.jemaat_id || ('guest_' + d.id),
                nama_asli: d.nama_jemaat,
                status: d.status,
                alias_1: d.status === 'Tamu' ? 'Tamu' : '',
                alias_2: '',
                cabang_id: d.jemaat ? d.jemaat.cabang_id : null,
                cabang: d.jemaat ? d.jemaat.cabang : null
              }));
              saveSessionState();
            } else {
              presentList = [];
            }
            updateAttendanceListUI();
          })
          .catch(() => {
            presentList = [];
            updateAttendanceListUI();
          });
      } else {
        presentList = [];
        updateAttendanceListUI();
      }
    }

    function updateSessionInfoTextOnly() {
      const dateVal = sessionDateInput.value;
      const ibadahVal = sessionIbadahSelect.value || 'Ibadah Umum';
      const sesiVal = sessionSesiSelect && sessionSesiSelect.value ? ` (${sessionSesiSelect.value})` : '';
      const lokasiVal = sessionLokasiSelect.value || 'Lokasi';

      if (dateVal) {
        const dateObj = new Date(dateVal);
        const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
        const formattedDate = dateObj.toLocaleDateString('id-ID', options);
        sessionInfoText.textContent = `${formattedDate} • ${ibadahVal}${sesiVal} @ ${lokasiVal}`;
      }
    }

    function updateSessionInfo() {
      updateSessionInfoTextOnly();
      loadAttendanceForCurrentSession();
      saveSessionState();
    }

    // 2. TOGGLE MODE SENTUH TABLET
    function toggleTouchMode() {
      const isTouch = modeSentuhCheckbox.checked;
      if (isTouch) {
        standardContainer.classList.add('hidden');
        touchContainer.classList.remove('hidden');
        renderTouchModeGrid();
      } else {
        standardContainer.classList.remove('hidden');
        touchContainer.classList.add('hidden');
      }
    }

    function selectWilayahTab(wilayahId) {
      activeWilayahTab = String(wilayahId);
      document.querySelectorAll('.wilayah-tab').forEach(tab => {
        if (tab.getAttribute('data-wilayah') === activeWilayahTab) {
          tab.className = 'wilayah-tab px-4 py-2 rounded-xl font-bold bg-blue-900 text-white shadow-sm transition shrink-0';
        } else {
          tab.className = 'wilayah-tab px-3.5 py-2 rounded-xl font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition shrink-0';
        }
      });
      renderTouchModeGrid();
    }

    function updateTabCounts() {
      const totalAll = masterJemaat.length;
      const countAllEl = document.getElementById('count-tab-all');
      if (countAllEl) countAllEl.textContent = totalAll;

      masterCabang.forEach(c => {
        const cCount = masterJemaat.filter(j => String(j.cabang_id) === String(c.id)).length;
        const el = document.getElementById(`count-tab-${c.id}`);
        if (el) el.textContent = cCount;
      });

      const noneCount = masterJemaat.filter(j => !j.cabang_id).length;
      const noneEl = document.getElementById('count-tab-none');
      if (noneEl) noneEl.textContent = noneCount;
    }

    // RENDER TOUCH GRID
    function renderTouchModeGrid() {
      updateTabCounts();
      touchCardsGrid.innerHTML = '';

      // Filter jemaat berdasarkan tab wilayah
      let filteredJemaat = masterJemaat;
      if (activeWilayahTab !== 'all') {
        if (activeWilayahTab === 'none') {
          filteredJemaat = masterJemaat.filter(j => !j.cabang_id);
        } else {
          filteredJemaat = masterJemaat.filter(j => String(j.cabang_id) === String(activeWilayahTab));
        }
      }

      // Cek apakah filter search terisi
      const searchQuery = searchInput.value.trim().toLowerCase();
      if (searchQuery) {
        filteredJemaat = filteredJemaat.filter(j => 
          j.nama_asli.toLowerCase().includes(searchQuery) ||
          (j.alias_1 && j.alias_1.toLowerCase().includes(searchQuery)) ||
          (j.alias_2 && j.alias_2.toLowerCase().includes(searchQuery)) ||
          (j.keluarga && j.keluarga.toLowerCase().includes(searchQuery))
        );
      }

      const isGroupKeluarga = toggleKeluarga.checked;

      if (isGroupKeluarga) {
        // Kelompokkan per keluarga
        const grouped = {};
        filteredJemaat.forEach(j => {
          const kelName = j.keluarga && j.keluarga.trim() !== '' ? j.keluarga.trim() : 'Lainnya / Tanpa Keluarga';
          if (!grouped[kelName]) grouped[kelName] = [];
          grouped[kelName].push(j);
        });

        const gridWrapper = document.createElement('div');
        gridWrapper.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4';

        Object.keys(grouped).forEach(kelName => {
          const members = grouped[kelName];
          const attendedInFamily = members.filter(m => presentList.some(p => String(p.id) === String(m.id))).length;
          const allAttended = attendedInFamily === members.length && members.length > 0;

          const familyCard = document.createElement('div');
          familyCard.className = 'bg-white rounded-2xl border border-slate-200/90 shadow-xs p-4 flex flex-col justify-between space-y-3 hover:border-slate-300 transition';

          familyCard.innerHTML = `
            <div class="flex items-center justify-between gap-2 pb-2 border-b border-slate-100">
              <div class="flex items-center gap-1.5 truncate">
                <i class="ri-team-line text-blue-700 text-sm shrink-0"></i>
                <span class="font-bold text-xs text-slate-800 truncate" title="${kelName}">${kelName}</span>
              </div>
              <div class="flex items-center gap-2 shrink-0">
                <span class="text-[11px] font-semibold ${attendedInFamily > 0 ? 'text-blue-700' : 'text-slate-400'}">
                  ${attendedInFamily}/${members.length} hadir
                </span>
                <button type="button" onclick="toggleEntireFamily('${encodeURIComponent(kelName)}')"
                  class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition ${allAttended ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'}">
                  ${allAttended ? '✓ Semua hadir' : 'Semua hadir'}
                </button>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-2 members-grid"></div>
          `;

          const membersGrid = familyCard.querySelector('.members-grid');

          members.forEach(m => {
            const isPresent = presentList.some(p => String(p.id) === String(m.id));
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `p-3 rounded-xl border text-left flex flex-col justify-between min-h-[56px] transition active:scale-95 ${
              isPresent
                ? 'bg-blue-50 border-blue-600 ring-2 ring-blue-500/30 text-blue-950 font-bold shadow-xs'
                : 'bg-white border-slate-200 hover:border-slate-300 text-slate-800 font-semibold'
            }`;

            btn.onclick = () => togglePersonPresence(m);

            let aliasText = [m.alias_1, m.alias_2].filter(Boolean).join(' / ');
            btn.innerHTML = `
              <div class="text-xs truncate w-full flex items-center justify-between">
                <span class="truncate">${m.nama_asli}</span>
                ${isPresent ? '<i class="ri-check-line text-blue-700 text-sm font-bold"></i>' : ''}
              </div>
              <div class="text-[10px] ${isPresent ? 'text-blue-700' : 'text-slate-400'} truncate mt-1">
                ${aliasText || m.status}
              </div>
            `;
            membersGrid.appendChild(btn);
          });

          gridWrapper.appendChild(familyCard);
        });

        touchCardsGrid.appendChild(gridWrapper);
      } else {
        // Tampilan grid per individu (tanpa kelompok)
        const gridWrapper = document.createElement('div');
        gridWrapper.className = 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3';

        filteredJemaat.forEach(m => {
          const isPresent = presentList.some(p => String(p.id) === String(m.id));
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = `p-3 rounded-xl border text-left flex flex-col justify-between min-h-[64px] transition active:scale-95 ${
            isPresent
              ? 'bg-blue-50 border-blue-600 ring-2 ring-blue-500/30 text-blue-950 font-bold shadow-xs'
              : 'bg-white border-slate-200 hover:border-slate-300 text-slate-800 font-semibold'
          }`;

          btn.onclick = () => togglePersonPresence(m);

          let aliasText = [m.alias_1, m.alias_2].filter(Boolean).join(' / ');
          btn.innerHTML = `
            <div class="text-xs truncate w-full flex items-center justify-between">
              <span class="truncate">${m.nama_asli}</span>
              ${isPresent ? '<i class="ri-check-line text-blue-700 text-sm font-bold"></i>' : ''}
            </div>
            <div class="text-[10px] ${isPresent ? 'text-blue-700' : 'text-slate-400'} truncate mt-1">
              ${aliasText || (m.keluarga || m.status)}
            </div>
          `;
          gridWrapper.appendChild(btn);
        });

        touchCardsGrid.appendChild(gridWrapper);
      }
    }

    function togglePersonPresence(jemaat) {
      const idx = presentList.findIndex(p => String(p.id) === String(jemaat.id));
      if (idx >= 0) {
        presentList.splice(idx, 1);
      } else {
        presentList.unshift(jemaat);
      }
      saveSessionState();
      updateAttendanceListUI();
      if (modeSentuhCheckbox.checked) {
        renderTouchModeGrid();
      }
    }

    function toggleEntireFamily(encodedKelName) {
      const kelName = decodeURIComponent(encodedKelName);
      const familyMembers = masterJemaat.filter(j => {
        const k = j.keluarga && j.keluarga.trim() !== '' ? j.keluarga.trim() : 'Lainnya / Tanpa Keluarga';
        return k === kelName;
      });

      const allPresent = familyMembers.every(m => presentList.some(p => String(p.id) === String(m.id)));

      if (allPresent) {
        // Hapus semua anggota keluarga dari daftar hadir
        const memberIds = familyMembers.map(m => String(m.id));
        presentList = presentList.filter(p => !memberIds.includes(String(p.id)));
      } else {
        // Tambahkan semua anggota keluarga yang belum ada
        familyMembers.forEach(m => {
          if (!presentList.some(p => String(p.id) === String(m.id))) {
            presentList.unshift(m);
          }
        });
      }

      saveSessionState();
      updateAttendanceListUI();
      renderTouchModeGrid();
    }

    // 3. LIVE SEARCH & AUTO-SUGGEST
    searchInput.addEventListener('input', function() {
      const query = this.value.trim().toLowerCase();

      if (modeSentuhCheckbox.checked) {
        renderTouchModeGrid();
      }

      if (query.length === 0) {
        suggestionsBox.classList.add('hidden');
        return;
      }

      const filtered = masterJemaat.filter(j => {
        const matchName = j.nama_asli.toLowerCase().includes(query);
        const matchAlias1 = j.alias_1 && j.alias_1.toLowerCase().includes(query);
        const matchAlias2 = j.alias_2 && j.alias_2.toLowerCase().includes(query);
        const matchKeluarga = j.keluarga && j.keluarga.toLowerCase().includes(query);
        const notAddedYet = !presentList.some(p => String(p.id) === String(j.id));
        return (matchName || matchAlias1 || matchAlias2 || matchKeluarga) && notAddedYet;
      });

      renderSuggestions(filtered, query);
    });

    searchInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        const query = this.value.trim();
        if (query.length > 0) {
          const firstMatch = masterJemaat.find(j =>
            !presentList.some(p => String(p.id) === String(j.id)) &&
            (j.nama_asli.toLowerCase().includes(query.toLowerCase()) ||
              (j.alias_1 && j.alias_1.toLowerCase().includes(query.toLowerCase())))
          );

          if (firstMatch) addPerson(firstMatch);
          else addAsGuest(query);
        }
      }
    });

    function renderSuggestions(list, query) {
      suggestionsBox.innerHTML = '';

      if (list.length === 0) {
        suggestionsBox.innerHTML = `
          <div class="p-3 text-xs text-slate-500 flex justify-between items-center">
            <span>Nama "<strong>${query}</strong>" tidak terdaftar.</span>
            <button type="button" onclick="addAsGuest('${query.replace(/'/g, "\\'")}')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1 rounded-lg text-[11px] font-semibold transition">
              + Tambah sbg Tamu
            </button>
          </div>
        `;
      } else {
        list.slice(0, 10).forEach(item => {
          const div = document.createElement('div');
          div.className = 'p-3 hover:bg-blue-50 cursor-pointer flex items-center justify-between text-xs transition';

          let aliases = [item.alias_1, item.alias_2].filter(Boolean).join(' / ');
          let subtext = [aliases, item.cabang?.nama_cabang || 'Darmo'].filter(Boolean).join(' · ');

          div.innerHTML = `
            <div>
              <div class="font-bold text-slate-800">${item.nama_asli}</div>
              <div class="text-[11px] text-slate-400">${subtext || item.status}</div>
            </div>
            <span class="text-blue-600 font-semibold text-xs">+ Pilih</span>
          `;
          div.onclick = () => addPerson(item);
          suggestionsBox.appendChild(div);
        });
      }

      suggestionsBox.classList.remove('hidden');
    }

    function addPerson(jemaat) {
      presentList.unshift(jemaat);
      saveSessionState();
      updateAttendanceListUI();
      searchInput.value = '';
      suggestionsBox.classList.add('hidden');
      searchInput.focus();
      if (modeSentuhCheckbox.checked) renderTouchModeGrid();
    }

    function addAsGuest(guestName) {
      const trimmedName = (guestName || '').trim();
      if (!trimmedName) return;

      const cabangId = sessionLokasiSelect ? (sessionLokasiSelect.options[sessionLokasiSelect.selectedIndex]?.dataset?.id || null) : null;
      const tempId = 'guest_' + Date.now();

      const guestObj = {
        id: tempId,
        nama_asli: trimmedName,
        status: 'Tamu',
        kategori: 'Tamu',
        alias_1: 'Tamu',
        alias_2: '',
        cabang_id: cabangId,
        isGuest: true
      };

      presentList.unshift(guestObj);
      saveSessionState();
      updateAttendanceListUI();
      searchInput.value = '';
      suggestionsBox.classList.add('hidden');
      searchInput.focus();
      if (modeSentuhCheckbox.checked) renderTouchModeGrid();

      // Simpan langsung ke database jemaat dengan kategori Tamu
      fetch('{{ route('admin.attendance.storeGuest') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          nama_asli: trimmedName,
          cabang_id: cabangId
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success && data.jemaat) {
          const idx = presentList.findIndex(p => p.id === tempId);
          if (idx !== -1) {
            presentList[idx].id = data.jemaat.id;
            presentList[idx].kategori = data.jemaat.kategori || 'Tamu';
          }
          if (!masterJemaat.some(j => j.id === data.jemaat.id)) {
            masterJemaat.push(data.jemaat);
          }
          saveSessionState();
          updateAttendanceListUI();
        }
      })
      .catch(err => console.error('Gagal menyimpan tamu:', err));
    }

    function removePerson(id) {
      presentList = presentList.filter(p => String(p.id) !== String(id));
      saveSessionState();
      updateAttendanceListUI();
      if (modeSentuhCheckbox.checked) renderTouchModeGrid();
    }

    function clearList() {
      if (confirm('Apakah Anda yakin ingin membersihkan seluruh daftar pencatatan untuk sesi ini?')) {
        presentList = [];
        localStorage.removeItem(getSessionKey());
        updateAttendanceListUI();
        if (modeSentuhCheckbox.checked) renderTouchModeGrid();
      }
    }

    function updateAttendanceListUI() {
      const oldRows = attendanceList.querySelectorAll('.attendance-row');
      oldRows.forEach(r => r.remove());

      if (presentList.length === 0) {
        emptyState.style.display = 'flex';
      } else {
        emptyState.style.display = 'none';
      }

      let anggotaCount = 0;
      let tamuCount = 0;

      presentList.forEach((item, index) => {
        if (item.status === 'Tamu') tamuCount++;
        else anggotaCount++;

        let aliases = [item.alias_1, item.alias_2].filter(Boolean).join(' / ');
        let subtext = [aliases, item.cabang?.nama_cabang || 'Darmo'].filter(Boolean).join(' · ');

        const itemId = String(item.id).replace(/'/g, "\\'");

        const row = document.createElement('div');
        row.className = 'attendance-row px-5 py-3.5 flex items-center justify-between hover:bg-slate-50/70 transition';
        row.innerHTML = `
          <div class="flex items-center gap-4">
            <span class="text-xs text-slate-400 font-medium w-4">${presentList.length - index}.</span>
            <div>
              <div class="font-bold text-slate-800 text-sm flex items-center gap-2">
                ${item.nama_asli}
                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold ${item.status === 'Tamu' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800'}">
                  ${item.status ?? 'Anggota'}
                </span>
              </div>
              <div class="text-xs text-slate-400 mt-0.5">${subtext || item.status}</div>
            </div>
          </div>
          <button type="button" onclick="removePerson('${itemId}')" class="text-slate-300 hover:text-rose-600 transition text-sm font-bold p-2 hover:bg-slate-100 rounded-lg" title="Hapus">
            ✕
          </button>
        `;
        attendanceList.appendChild(row);
      });

      totalCountEl.textContent = presentList.length;
      statusCountEl.textContent = `${anggotaCount} anggota + ${tamuCount} tamu`;
    }

    // 4. MODAL LAPORAN WHATSAPP
    function saveAndShowWAModal() {
      if (presentList.length === 0) {
        alert('Daftar pencatatan kehadiran masih kosong!');
        return;
      }

      const payload = {
        _token: '{{ csrf_token() }}',
        tanggal: sessionDateInput.value,
        nama_ibadah: sessionIbadahSelect.value || 'Ibadah Umum',
        nama_sesi: sessionSesiSelect ? sessionSesiSelect.value : 'Pagi',
        nama_cabang: sessionLokasiSelect.value || 'Darmo',
        nama_petugas: sessionPetugasInput.value || 'Christian',
        present_list: presentList
      };

      fetch('{{ route('admin.attendance.store') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          saveSessionState();
          generateWAText();
          document.getElementById('waReportModal').style.display = 'flex';
        } else {
          alert('Gagal menyimpan: ' + data.message);
        }
      })
      .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan koneksi saat menyimpan data ke database.');
      });
    }

    function closeWAModal() {
      document.getElementById('waReportModal').style.display = 'none';
    }

    function generateWAText() {
      const dateVal = sessionDateInput.value;
      const ibadahVal = sessionIbadahSelect.value || 'Ibadah Umum';
      const sesiVal = sessionSesiSelect && sessionSesiSelect.value ? ` (${sessionSesiSelect.value})` : '';
      const lokasiVal = sessionLokasiSelect.value || 'Darmo';
      const petugasVal = sessionPetugasInput.value || 'Christian';
      const includeNames = document.getElementById('toggle-include-names').checked;

      let anggotaCount = presentList.filter(p => p.status !== 'Tamu').length;
      let tamuCount = presentList.filter(p => p.status === 'Tamu').length;

      let formattedDate = dateVal;
      if (dateVal) {
        const dateObj = new Date(dateVal);
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        formattedDate = dateObj.toLocaleDateString('id-ID', options);
      }

      let text = `*Laporan Kehadiran - Gereja Isa Almasih Surabaya*\n`;
      text += `Tanggal: ${formattedDate}\n`;
      text += `Ibadah: ${ibadahVal}${sesiVal} - ${lokasiVal}\n`;
      text += `Total Hadir: *${presentList.length} orang* (${anggotaCount} anggota, ${tamuCount} tamu)\n`;
      text += `Petugas: ${petugasVal}`;

      if (includeNames && presentList.length > 0) {
        text += `\n\n*Daftar Nama Hadir:*\n`;
        presentList.forEach((item, index) => {
          text += `${index + 1}. ${item.nama_asli} (${item.status})\n`;
        });
      }

      document.getElementById('wa-text-preview').value = text;
    }

    function toggleSwitchNames() {
      const checkbox = document.getElementById('toggle-include-names');
      const switchBg = document.getElementById('switch-bg');
      const switchDot = document.getElementById('switch-dot');

      checkbox.checked = !checkbox.checked;

      if (checkbox.checked) {
        switchBg.style.backgroundColor = '#10b981';
        switchDot.style.transform = 'translateX(24px)';
      } else {
        switchBg.style.backgroundColor = '#cbd5e1';
        switchDot.style.transform = 'translateX(0px)';
      }

      generateWAText();
    }

    function sendDirectWA() {
      const phone = document.getElementById('wa-contact-select').value;
      const text = encodeURIComponent(document.getElementById('wa-text-preview').value);
      window.open(`https://wa.me/${phone}?text=${text}`, '_blank');
    }

    function openWAPicker() {
      const text = encodeURIComponent(document.getElementById('wa-text-preview').value);
      window.open(`https://wa.me/?text=${text}`, '_blank');
    }

    function copyWAText() {
      const textarea = document.getElementById('wa-text-preview');
      textarea.select();
      document.execCommand('copy');
      alert('Teks ringkasan berhasil disalin ke clipboard!');
    }

    // Tutup auto-suggest saat klik di luar
    document.addEventListener('click', function(e) {
      if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
        suggestionsBox.classList.add('hidden');
      }
    });
  </script>
@endsection
