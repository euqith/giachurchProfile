@extends('admin.layoutCMS')

@section('content')
  <div class="space-y-6">

    <!-- Sesi Ibadah Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
      <h5 class="font-bold text-slate-800 text-sm flex items-center gap-2">
        <i class="ri-calendar-event-line text-blue-600"></i> Sesi Ibadah
      </h5>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
        <div>
          <label class="block font-semibold text-slate-600 mb-1.5">Tanggal</label>
          <input type="date" id="session-date" value="{{ date('Y-m-d') }}" onchange="updateSessionInfo()"
            class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 outline-none focus:border-blue-600 bg-white">
        </div>

        <div>
          <label class="block font-semibold text-slate-600 mb-1.5">Jenis Ibadah</label>
          <select id="session-ibadah" name="jenis_ibadah_id" onchange="updateSessionInfo()" required
            class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 outline-none focus:border-blue-600 bg-white">
            <option value="" disabled selected>-- Pilih Jenis Ibadah --</option>
            @foreach ($jenisIbadahs ?? [] as $ibadah)
              <option value="{{ $ibadah->nama_ibadah }}">{{ $ibadah->nama_ibadah }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block font-semibold text-slate-600 mb-1.5">Lokasi</label>
          <select id="session-lokasi" name="cabang_id" onchange="updateSessionInfo()" required
            class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 outline-none focus:border-blue-600 bg-white">
            <option value="" disabled selected>-- Pilih Lokasi Cabang --</option>
            @foreach ($cabangs ?? [] as $cabang)
              <option value="{{ $cabang->nama_cabang }}">{{ $cabang->nama_cabang }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block font-semibold text-slate-600 mb-1.5">Nama Petugas</label>
          <input type="text" id="session-petugas" value="{{ Auth::user()->name ?? 'Administrator' }}" readonly
            class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-500 bg-slate-50 outline-none">
        </div>
      </div>
    </div>

    <!-- Pencatatan Kehadiran Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">

      <!-- Header & Counter -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
        <div>
          <h5 class="font-bold text-slate-800 text-sm flex items-center gap-2">
            <i class="ri-user-add-line text-blue-600"></i> Pencatatan Kehadiran
          </h5>
          <p class="text-[11px] text-slate-400 mt-0.5">Ketik nama / alias → Enter → ketik lagi. Panah ↑↓ untuk memilih
            saran lain. Nama yang tidak dikenal bisa langsung ditambah sebagai tamu.</p>
        </div>

        <div class="text-right">
          <div id="total-count" class="text-3xl font-bold text-blue-900 leading-none">0</div>
          <div id="status-count" class="text-[11px] text-slate-400 font-medium mt-1">0 anggota + 0 tamu</div>
        </div>
      </div>

      <!-- Mode Toggle -->
      <div class="flex items-center gap-2 text-xs text-slate-500">
        <input type="checkbox" id="mode-sentuh" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
        <label for="mode-sentuh" class="cursor-pointer">Mode Sentuh (tablet): tombol besar per wilayah</label>
      </div>

      <!-- Input Search & Auto-Suggest Box -->
      <div class="relative">
        <div
          class="flex items-center w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-600/20 transition-all">
          <i class="ri-search-line text-slate-400 text-base mr-2.5 shrink-0"></i>
          <input type="text" id="search-jemaat" placeholder="Ketik nama atau alias, lalu tekan Enter..."
            autocomplete="off" class="w-full text-xs text-slate-800 bg-transparent outline-none border-none">
        </div>

        <!-- Dropdown Hasil Pencarian -->
        <div id="suggestions-box"
          class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-60 overflow-y-auto hidden divide-y divide-slate-100">
        </div>
      </div>

      <!-- Container List Kehadiran -->
      <div id="attendance-wrapper" class="border border-slate-200 rounded-xl overflow-hidden bg-white">
        <div id="attendance-list" class="divide-y divide-slate-100 min-h-[100px]">
          <!-- Empty State Default -->
          <div id="empty-state"
            class="py-10 text-center flex flex-col items-center justify-center text-slate-400 space-y-2">
            <i class="ri-user-search-line text-3xl text-slate-300"></i>
            <p class="text-xs">Belum ada yang dicatat. Ketik nama di kolom pencarian lalu tekan Enter.</p>
          </div>
        </div>
      </div>

      <!-- Bottom Action Bar -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3">
        <div class="flex items-center gap-2 w-full sm:w-auto">
          <button type="button" onclick="saveAndShowWAModal()"
            style="background-color: #1e40af !important; color: #ffffff !important;"
            class="px-5 py-2.5 rounded-xl text-white text-xs font-bold shadow-md hover:bg-blue-900 transition flex items-center gap-2">
            <i class="ri-save-line text-sm"></i> Simpan Kehadiran
          </button>

          <button type="button" onclick="clearList()"
            class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition flex items-center gap-2">
            <i class="ri-delete-bin-line text-slate-500 text-sm"></i> Bersihkan Daftar
          </button>
        </div>

        <!-- Running Session Info Text -->
        <div id="session-info-text" class="text-xs text-slate-400 font-medium text-right">
          Jumat, 25 Sep 2026 • Ibadah Umum @ Darmo Pagi
        </div>
      </div>

    </div>

  </div>

  <!-- MODAL RINGKASAN WHATSAPP -->
  <div id="waReportModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-3xl w-full max-w-lg shadow-2xl mx-4 relative animate-fade-in">

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
            <option value="6281703872525">Ariel · +62 81703872525</option>
            <option value="6281234567890">Pengurus Utama · +62 81234567890</option>
          </select>
          <button type="button" onclick="sendDirectWA()" style="background-color: #059669 !important;"
            class="shrink-0 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
            <i class="ri-send-plane-fill"></i> Kirim WA
          </button>
        </div>
        <p class="text-[11px] text-emerald-800/80 leading-tight">
          Pesan akan terbuka di WhatsApp dengan teks terisi; tekan kirim di WhatsApp. (WhatsApp tidak mengizinkan kirim
          otomatis langsung ke grup, jadi kirim ke pengurus yang meneruskan ke grup.)
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

  <!-- JavaScript Interaktif Presensi + Dynamic Session Persistence -->
  <script>
    const masterJemaat = @json($jemaats ?? []);

    // Elemen Form Sesi
    const sessionDateInput = document.getElementById('session-date');
    const sessionIbadahSelect = document.getElementById('session-ibadah');
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

    let presentList = [];

    // 1. INSIALISASI: RESTORE SESI SAAT PERTAMA KALI HALAMAN DIBUKA
    restoreSavedSession();

    function getSessionKey() {
      const date = sessionDateInput.value || 'nodate';
      const ibadah = sessionIbadahSelect.value || 'noibadah';
      const lokasi = sessionLokasiSelect.value || 'nolokasi';
      return `attendance_draft_${date}_${ibadah}_${lokasi}`;
    }

    function saveSessionState() {
      // Simpan pilihan filter Sesi Ibadah saat ini
      const sessionMeta = {
        date: sessionDateInput.value,
        ibadah: sessionIbadahSelect.value,
        lokasi: sessionLokasiSelect.value
      };
      localStorage.setItem('active_attendance_session_meta', JSON.stringify(sessionMeta));

      // Simpan daftar jemaat spesifik untuk kombinasi (Tanggal + Jenis Ibadah + Lokasi)
      const key = getSessionKey();
      localStorage.setItem(key, JSON.stringify(presentList));
    }

    function restoreSavedSession() {
      // A. Load Pilihan Sesi Terakhir (Tanggal, Jenis Ibadah, Lokasi)
      try {
        const savedMeta = localStorage.getItem('active_attendance_session_meta');
        if (savedMeta) {
          const meta = JSON.parse(savedMeta);
          if (meta.date) sessionDateInput.value = meta.date;
          if (meta.ibadah) sessionIbadahSelect.value = meta.ibadah;
          if (meta.lokasi) sessionLokasiSelect.value = meta.lokasi;
        }
      } catch (e) {
        console.error("Gagal memuat meta sesi:", e);
      }

      // B. Load Daftar Jemaat untuk Sesi Aktif
      loadAttendanceForCurrentSession();
      updateSessionInfoTextOnly();
    }

    function loadAttendanceForCurrentSession() {
      const key = getSessionKey();

      // 1. Cek dulu apakah ada draft lokal di LocalStorage
      try {
        const savedList = localStorage.getItem(key);
        if (savedList) {
          const parsed = JSON.parse(savedList);
          if (parsed.length > 0) {
            presentList = parsed;
            updateAttendanceListUI();
            return;
          }
        }
      } catch (e) {
        console.error("Gagal baca LocalStorage:", e);
      }

      // 2. Jika LocalStorage kosong, tarik data dari Database (jika sesi ini sudah pernah disimpan)
      const dateVal = sessionDateInput.value;
      const ibadahVal = sessionIbadahSelect.value;
      const lokasiVal = sessionLokasiSelect.value;

      if (dateVal && ibadahVal && lokasiVal) {
        fetch(
            `{{ route('admin.attendance.get-draft') }}?date=${dateVal}&ibadah=${encodeURIComponent(ibadahVal)}&lokasi=${encodeURIComponent(lokasiVal)}`
          )
          .then(res => res.json())
          .then(data => {
            if (data.success && data.details && data.details.length > 0) {
              presentList = data.details.map(d => ({
                id: d.jemaat_id || ('guest_' + d.id),
                nama_asli: d.nama_jemaat,
                status: d.status,
                alias_1: d.status === 'Tamu' ? 'Tamu' : '',
                alias_2: ''
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
      const lokasiVal = sessionLokasiSelect.value || 'Darmo Pagi';

      if (dateVal) {
        const dateObj = new Date(dateVal);
        const options = {
          weekday: 'long',
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        };
        const formattedDate = dateObj.toLocaleDateString('id-ID', options);
        sessionInfoText.textContent = `${formattedDate} • ${ibadahVal} @ ${lokasiVal}`;
      }
    }

    // Event Listener saat Tanggal, Jenis Ibadah, atau Lokasi diganti
    function updateSessionInfo() {
      updateSessionInfoTextOnly();
      loadAttendanceForCurrentSession();
      saveSessionState();
    }

    // 2. LIVE SEARCH EVENT
    searchInput.addEventListener('input', function() {
      const query = this.value.trim().toLowerCase();

      if (query.length === 0) {
        suggestionsBox.classList.add('hidden');
        return;
      }

      const filtered = masterJemaat.filter(j => {
        const matchName = j.nama_asli.toLowerCase().includes(query);
        const matchAlias1 = j.alias_1 && j.alias_1.toLowerCase().includes(query);
        const matchAlias2 = j.alias_2 && j.alias_2.toLowerCase().includes(query);
        const matchKeluarga = j.keluarga && j.keluarga.toLowerCase().includes(query);
        const notAddedYet = !presentList.some(p => p.id === j.id);
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
            !presentList.some(p => p.id === j.id) &&
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
                    <button type="button" onclick="addAsGuest('${query}')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1 rounded-lg text-[11px] font-semibold transition">
                        + Tambah sbg Tamu
                    </button>
                </div>
            `;
      } else {
        list.forEach(item => {
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
    }

    function addAsGuest(guestName) {
      const guestObj = {
        id: 'guest_' + Date.now(),
        nama_asli: guestName,
        status: 'Tamu',
        alias_1: 'Tamu',
        alias_2: '',
        isGuest: true
      };
      presentList.unshift(guestObj);
      saveSessionState();
      updateAttendanceListUI();
      searchInput.value = '';
      suggestionsBox.classList.add('hidden');
      searchInput.focus();
    }

    function removePerson(id) {
      // Gunakan perbandingan kendor (== / String(p.id) === String(id)) agar aman untuk tipe Number maupun String
      presentList = presentList.filter(p => String(p.id) !== String(id));

      // Simpan perubahan langsung ke LocalStorage
      saveSessionState();

      // Re-render tampilan UI
      updateAttendanceListUI();
    }

    function clearList() {
      if (confirm('Apakah Anda yakin ingin membersihkan seluruh daftar pencatatan untuk sesi ini?')) {
        presentList = [];
        localStorage.removeItem(getSessionKey());
        updateAttendanceListUI();
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

        // Escape ID agar aman dari karakter khusus
        const itemId = String(item.id).replace(/'/g, "\\'");

        const row = document.createElement('div');
        row.className =
        'attendance-row px-5 py-3.5 flex items-center justify-between hover:bg-slate-50/70 transition';
        row.innerHTML = `
            <div class="flex items-center gap-4">
                <span class="text-xs text-slate-400 font-medium w-4">${presentList.length - index}.</span>
                <div>
                    <div class="font-bold text-slate-800 text-sm">${item.nama_asli}</div>
                    <div class="text-xs text-slate-400 mt-0.5">${subtext || item.status}</div>
                </div>
            </div>
            <button type="button" onclick="removePerson('${itemId}')" class="text-slate-300 hover:text-slate-600 transition text-sm font-bold p-2 hover:bg-slate-100 rounded-lg">
                ✕
            </button>
        `;
        attendanceList.appendChild(row);
      });

      totalCountEl.textContent = presentList.length;
      statusCountEl.textContent = `${anggotaCount} anggota + ${tamuCount} tamu`;
    }

    // --- MANAJEMEN POP-UP REPORT WHATSAPP ---
    function saveAndShowWAModal() {
      if (presentList.length === 0) {
        alert('Daftar pencatatan kehadiran masih kosong!');
        return;
      }

      const payload = {
        _token: '{{ csrf_token() }}',
        tanggal: sessionDateInput.value,
        nama_ibadah: sessionIbadahSelect.value || 'Ibadah Umum',
        nama_cabang: sessionLokasiSelect.value || 'Darmo Pagi',
        present_list: presentList
      };

      // Kirim data ke Database Laravel via AJAX
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
            // Tetap simpan state lokal agar daftar nama tidak hilang di layar
            saveSessionState();

            // Tampilkan Modal Ringkasan WA
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
      const lokasiVal = sessionLokasiSelect.value || 'Darmo Pagi';
      const petugasVal = sessionPetugasInput.value || 'Administrator';
      const includeNames = document.getElementById('toggle-include-names').checked;

      let anggotaCount = presentList.filter(p => p.status !== 'Tamu').length;
      let tamuCount = presentList.filter(p => p.status === 'Tamu').length;

      let formattedDate = dateVal;
      if (dateVal) {
        const dateObj = new Date(dateVal);
        const options = {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        };
        formattedDate = dateObj.toLocaleDateString('id-ID', options);
      }

      let text = `*Laporan Kehadiran - Gereja Isa Almasih Surabaya*\n`;
      text += `Tanggal: ${formattedDate}\n`;
      text += `Ibadah: ${ibadahVal} - ${lokasiVal}\n`;
      text += `Total Hadir: *${presentList.length} orang* (${anggotaCount} anggota, ${tamuCount} tamu)\n`;
      text += `Petugas: ${petugasVal}`;

      if (includeNames && presentList.length > 0) {
        text += `\n\n*Daftar Nama Hadir:*\n`;
        presentList.forEach((item, index) => {
          text += `${index + 1}. ${item.nama_asli}\n`;
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

    // Ping Heartbeat Sesi Laravel Setiap 5 Menit
    setInterval(function() {
      fetch('/admin/attendance/input', {
          method: 'HEAD'
        })
        .then(() => console.log('Session Active'))
        .catch(err => console.warn('Offline Mode Active'));
    }, 5 * 60 * 1000);

    // Close Dropdown Outside
    document.addEventListener('click', function(e) {
      if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
        suggestionsBox.classList.add('hidden');
      }
    });
  </script>
@endsection
