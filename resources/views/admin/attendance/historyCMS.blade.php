@extends('admin.layoutCMS')

@section('content')
<div class="space-y-6 pb-12" x-data="historyManager()">

  <!-- Page Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold mb-2">
        <i class="ri-history-line"></i> Rekapitulasi Sesi
      </div>
      <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Riwayat Sesi Ibadah</h1>
      <p class="text-xs text-slate-500 mt-0.5">Kelola, periksa, dan koreksi data kehadiran per sesi ibadah.</p>
    </div>
    <div class="flex items-center gap-2.5">
      <a href="{{ route('admin.attendance.report') }}"
        class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
        <i class="ri-dashboard-3-line text-sm"></i> Dashboard Absensi
      </a>
      <a href="{{ route('admin.attendance.input') }}"
        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition">
        <i class="ri-add-line text-sm"></i> Input Kehadiran Baru
      </a>
    </div>
  </div>

  <!-- Filter & Search Toolbar -->
  <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
    <form method="GET" action="{{ route('admin.attendance.history') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-3">
      
      <!-- Search Box -->
      <div class="relative w-full md:w-80">
        <i class="ri-search-2-line text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 text-xs"></i>
        <input type="text" name="search" value="{{ request('search') }}"
          placeholder="Cari ibadah, sesi, lokasi, petugas..."
          class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:outline-none focus:border-blue-500 bg-white">
      </div>

      <!-- Filter Dropdowns -->
      <div class="flex flex-wrap items-center gap-2">
        <select name="jenis" onchange="this.form.submit()"
          class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:outline-none focus:border-blue-500 bg-white">
          <option value="all">Semua Jenis</option>
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

        <button type="submit" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition" title="Terapkan filter">
          <i class="ri-filter-3-line text-xs"></i>
        </button>

        @if(request()->hasAny(['search', 'jenis', 'sesi', 'lokasi']))
          <a href="{{ route('admin.attendance.history') }}"
            class="p-2 text-rose-500 hover:text-rose-700 rounded-xl hover:bg-rose-50 transition" title="Hapus filter">
            <i class="ri-close-circle-line text-sm"></i>
          </a>
        @endif

        <span class="text-xs text-slate-400 font-medium ml-2">
          {{ $sessions->total() }} sesi ditemukan
        </span>
      </div>
    </form>
  </div>

  <!-- Tabel Riwayat Sesi -->
  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 font-semibold text-slate-600">
            <th class="py-3.5 px-4">TANGGAL</th>
            <th class="py-3.5 px-4">SESI IBADAH</th>
            <th class="py-3.5 px-4">LOKASI</th>
            <th class="py-3.5 px-4">PETUGAS</th>
            <th class="py-3.5 px-4 text-center">HADIR</th>
            <th class="py-3.5 px-4 text-center w-28">AKSI</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
          @forelse($sessions as $session)
            <tr class="hover:bg-slate-50/70 transition">
              <!-- Tanggal -->
              <td class="py-4 px-4 font-semibold text-slate-800 whitespace-nowrap">
                <div class="flex items-center gap-2">
                  <i class="ri-calendar-line text-slate-400"></i>
                  <span>{{ \Carbon\Carbon::parse($session->tanggal)->translatedFormat('l, d M Y') }}</span>
                </div>
              </td>

              <!-- Sesi Ibadah -->
              <td class="py-4 px-4">
                <div class="font-bold text-slate-900 text-sm">{{ $session->nama_ibadah }}</div>
                @if($session->nama_sesi)
                  <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/60">
                    Sesi: {{ $session->nama_sesi }}
                  </span>
                @endif
              </td>

              <!-- Lokasi Cabang -->
              <td class="py-4 px-4 text-slate-600 font-medium">
                <div class="flex items-center gap-1.5">
                  <i class="ri-map-pin-2-line text-slate-400 text-xs"></i>
                  <span>{{ $session->nama_cabang }}</span>
                </div>
              </td>

              <!-- Petugas -->
              <td class="py-4 px-4 text-slate-500">
                <div class="flex items-center gap-1.5">
                  <i class="ri-user-line text-slate-400 text-xs"></i>
                  <span>{{ $session->nama_petugas ?: 'Administrator' }}</span>
                </div>
              </td>

              <!-- Total Hadir + Breakout -->
              <td class="py-4 px-4 text-center">
                <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-900 font-extrabold text-sm">
                  {{ $session->total_hadir }}
                </div>
                <div class="text-[10px] text-slate-400 mt-1">
                  {{ $session->total_anggota }} anggota · {{ $session->total_tamu }} tamu
                </div>
              </td>

              <!-- Aksi -->
              <td class="py-4 px-4 text-center">
                <button type="button" @click="openDetail({{ $session->id }})"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-semibold text-xs border border-slate-200/60 transition">
                  <i class="ri-eye-line text-xs"></i> Detail
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="py-12 text-center text-slate-400">
                <i class="ri-inbox-line text-3xl mb-2 block text-slate-300"></i>
                Belum ada rekapan sesi ibadah yang sesuai filter.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    @if($sessions->hasPages())
      <div class="p-4 border-t border-slate-100">
        {{ $sessions->links() }}
      </div>
    @endif
  </div>

  <!-- INTERACTIVE DETAIL & CORRECTION MODAL (IMAGE-5) -->
  <div x-show="isModalOpen" style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    
    <div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]"
      @click.outside="closeModal()">

      <!-- Modal Header -->
      <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <div>
          <h3 class="text-base font-bold text-slate-900">Detail Sesi Ibadah</h3>
          <p class="text-xs text-slate-500 mt-0.5">Koreksi data atau lengkapi kehadiran yang terlewat</p>
        </div>
        <button type="button" @click="closeModal()" class="w-8 h-8 rounded-full bg-slate-200/70 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition">
          <i class="ri-close-line text-lg"></i>
        </button>
      </div>

      <!-- Modal Body (Scrollable) -->
      <div class="p-5 sm:p-6 overflow-y-auto space-y-6">

        <!-- Form Metadata Sesi (5 Fields) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
          
          <!-- Tanggal -->
          <div>
            <label class="block text-[11px] font-bold text-slate-600 mb-1">Tanggal</label>
            <input type="date" x-model="modalData.tanggal"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500">
          </div>

          <!-- Jenis Ibadah -->
          <div>
            <label class="block text-[11px] font-bold text-slate-600 mb-1">Jenis Ibadah</label>
            <select x-model="modalData.nama_ibadah"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500 bg-white">
              @foreach($jenisIbadahs as $j)
                <option value="{{ $j->nama_ibadah }}">{{ $j->nama_ibadah }}</option>
              @endforeach
            </select>
          </div>

          <!-- Sesi -->
          <div>
            <label class="block text-[11px] font-bold text-slate-600 mb-1">Sesi</label>
            <select x-model="modalData.nama_sesi"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500 bg-white">
              <option value="">-- Tanpa Sesi --</option>
              @foreach($sesis as $s)
                <option value="{{ $s->nama_sesi }}">{{ $s->nama_sesi }}</option>
              @endforeach
            </select>
          </div>

          <!-- Lokasi Cabang -->
          <div>
            <label class="block text-[11px] font-bold text-slate-600 mb-1">Lokasi</label>
            <select x-model="modalData.nama_cabang"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500 bg-white">
              @foreach($cabangs as $c)
                <option value="{{ $c->nama_cabang }}">{{ $c->nama_cabang }}</option>
              @endforeach
            </select>
          </div>

          <!-- Nama Petugas -->
          <div class="sm:col-span-2">
            <label class="block text-[11px] font-bold text-slate-600 mb-1">Petugas / Pencatat</label>
            <input type="text" x-model="modalData.nama_petugas"
              placeholder="Nama pencatat kehadiran"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500">
          </div>

        </div>

        <!-- Section: Daftar Hadir & Tambah Nama yang Terlewat -->
        <div>
          <div class="flex items-center justify-between mb-3">
            <h4 class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
              <span>Daftar Hadir</span>
              <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-extrabold" x-text="modalData.present_list.length"></span>
            </h4>
          </div>

          <!-- Input Tambah Cepat Nama Terlewat -->
          <div class="flex items-center gap-2 mb-3">
            <div class="relative flex-1">
              <i class="ri-user-add-line text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 text-xs"></i>
              <input type="text" x-model="searchAddQuery" @input="filterCandidate()"
                placeholder="Tambah nama yang terlewat..."
                class="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500">
              
              <!-- Dropdown Saran Kandidat -->
              <div x-show="showCandidateDropdown && candidateList.length > 0"
                class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-48 overflow-y-auto z-20 divide-y divide-slate-100 text-xs">
                <template x-for="cand in candidateList" :key="cand.id">
                  <div @click="selectCandidate(cand)"
                    class="p-2.5 hover:bg-blue-50 cursor-pointer flex items-center justify-between transition">
                    <div>
                      <span class="font-bold text-slate-800" x-text="cand.nama_asli"></span>
                      <span class="text-[10px] text-slate-500 ml-1.5" x-text="cand.wilayah"></span>
                    </div>
                    <span class="text-[10px] px-1.5 py-0.2 rounded font-semibold"
                      :class="cand.status === 'Tamu' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800'"
                      x-text="cand.status"></span>
                  </div>
                </template>
              </div>
            </div>

            <!-- Tombol Tambah Tamu Bebas -->
            <button type="button" @click="addManualGuest()"
              class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold shrink-0 transition">
              + Tamu
            </button>
          </div>

          <!-- Table List Attendees in Modal -->
          <div class="border border-slate-200 rounded-xl overflow-hidden max-h-60 overflow-y-auto">
            <table class="w-full text-left text-xs border-collapse">
              <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 sticky top-0">
                <tr>
                  <th class="py-2.5 px-3 w-8 text-center">NO</th>
                  <th class="py-2.5 px-3">NAMA</th>
                  <th class="py-2.5 px-3">STATUS</th>
                  <th class="py-2.5 px-3 w-10 text-center">AKSI</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-slate-700">
                <template x-for="(person, pIdx) in modalData.present_list" :key="person.id || pIdx">
                  <tr class="hover:bg-slate-50/50 transition">
                    <td class="py-2 px-3 text-center text-slate-400 font-medium" x-text="pIdx + 1"></td>
                    <td class="py-2 px-3 font-bold text-slate-800" x-text="person.nama_asli || person.nama_jemaat"></td>
                    <td class="py-2 px-3">
                      <span class="px-2 py-0.2 rounded-full text-[10px] font-semibold"
                        :class="person.status === 'Tamu' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800'"
                        x-text="person.status"></span>
                    </td>
                    <td class="py-2 px-3 text-center">
                      <button type="button" @click="removePerson(pIdx)"
                        class="w-6 h-6 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition"
                        title="Hapus kehadiran jemaat ini">
                        <i class="ri-close-line text-sm"></i>
                      </button>
                    </td>
                  </tr>
                </template>
                <tr x-show="modalData.present_list.length === 0">
                  <td colspan="4" class="py-6 text-center text-slate-400 text-xs">
                    Belum ada nama yang tercatat hadir.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>

      </div>

      <!-- Modal Footer (Image-5 Actions) -->
      <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
        <!-- Tombol Hapus Sesi -->
        <button type="button" @click="confirmDeleteSession()"
          class="w-full sm:w-auto px-4 py-2 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold inline-flex items-center justify-center gap-1.5 transition">
          <i class="ri-delete-bin-line text-sm"></i> Hapus Sesi
        </button>

        <!-- Tombol Batal & Simpan Perubahan -->
        <div class="flex items-center gap-2 w-full sm:w-auto">
          <button type="button" @click="closeModal()"
            class="flex-1 sm:flex-initial px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
            Batal
          </button>
          <button type="button" @click="saveChanges()"
            :disabled="isSaving"
            class="flex-1 sm:flex-initial px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-xs font-bold shadow-md shadow-blue-500/20 inline-flex items-center justify-center gap-1.5 transition">
            <i class="ri-check-line text-sm" x-show="!isSaving"></i>
            <span x-text="isSaving ? 'Menyimpan...' : '✓ Simpan Perubahan'"></span>
          </button>
        </div>
      </div>

    </div>

  </div>

</div>

<!-- Alpine Script for History Modal & Editing -->
<script>
  function historyManager() {
    return {
      isModalOpen: false,
      isSaving: false,
      currentSessionId: null,
      allMasterJemaat: @json($jemaats),
      searchAddQuery: '',
      candidateList: [],
      showCandidateDropdown: false,

      modalData: {
        id: null,
        tanggal: '',
        nama_ibadah: '',
        nama_sesi: '',
        nama_cabang: '',
        nama_petugas: '',
        present_list: []
      },

      openDetail(sessionId) {
        this.currentSessionId = sessionId;
        this.searchAddQuery = '';
        this.candidateList = [];
        this.showCandidateDropdown = false;

        // Fetch session details from backend
        fetch(`/admin/attendance/history/${sessionId}`)
          .then(res => res.json())
          .then(data => {
            this.modalData = {
              id: data.id,
              tanggal: data.tanggal,
              nama_ibadah: data.nama_ibadah,
              nama_sesi: data.nama_sesi || '',
              nama_cabang: data.nama_cabang,
              nama_petugas: data.nama_petugas,
              present_list: (data.details || []).map(d => ({
                id: d.jemaat_id || ('tamu_' + Math.random()),
                nama_asli: d.nama_jemaat,
                status: d.status,
                wilayah: d.jemaat?.cabang?.nama_cabang || '-'
              }))
            };
            this.isModalOpen = true;
          })
          .catch(err => {
            alert('Gagal memuat detail sesi ibadah: ' + err.message);
          });
      },

      closeModal() {
        this.isModalOpen = false;
      },

      filterCandidate() {
        const q = this.searchAddQuery.toLowerCase().trim();
        if (!q) {
          this.candidateList = [];
          this.showCandidateDropdown = false;
          return;
        }

        const presentIds = new Set(this.modalData.present_list.map(p => p.id));
        this.candidateList = this.allMasterJemaat
          .filter(j => !presentIds.has(j.id) && (
            j.nama_asli.toLowerCase().includes(q) ||
            (j.alias_1 && j.alias_1.toLowerCase().includes(q))
          ))
          .slice(0, 8)
          .map(j => ({
            id: j.id,
            nama_asli: j.nama_asli,
            status: j.status || 'Anggota',
            wilayah: j.cabang?.nama_cabang || '-'
          }));

        this.showCandidateDropdown = true;
      },

      selectCandidate(cand) {
        this.modalData.present_list.push(cand);
        this.searchAddQuery = '';
        this.candidateList = [];
        this.showCandidateDropdown = false;
      },

      addManualGuest() {
        const name = prompt('Masukkan nama tamu baru:');
        if (name && name.trim()) {
          this.modalData.present_list.push({
            id: 'tamu_' + Date.now(),
            nama_asli: name.trim(),
            status: 'Tamu',
            wilayah: '-'
          });
        }
      },

      removePerson(index) {
        this.modalData.present_list.splice(index, 1);
      },

      saveChanges() {
        if (this.modalData.present_list.length === 0) {
          alert('Daftar hadir tidak boleh kosong!');
          return;
        }

        this.isSaving = true;

        fetch(`/admin/attendance/history/${this.modalData.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            tanggal: this.modalData.tanggal,
            nama_ibadah: this.modalData.nama_ibadah,
            nama_sesi: this.modalData.nama_sesi,
            nama_cabang: this.modalData.nama_cabang,
            nama_petugas: this.modalData.nama_petugas,
            present_list: this.modalData.present_list
          })
        })
        .then(res => res.json())
        .then(data => {
          this.isSaving = false;
          if (data.success) {
            alert('Perubahan sesi kehadiran berhasil disimpan!');
            window.location.reload();
          } else {
            alert('Gagal menyimpan: ' + (data.message || 'Terjadi kesalahan.'));
          }
        })
        .catch(err => {
          this.isSaving = false;
          alert('Kesalahan jaringan: ' + err.message);
        });
      },

      confirmDeleteSession() {
        if (!confirm('Apakah Anda yakin ingin menghapus sesi ibadah ini? Data yang dihapus tidak akan muncul di laporan.')) {
          return;
        }

        fetch(`/admin/attendance/history/${this.modalData.id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          }
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('Sesi ibadah berhasil dihapus!');
            window.location.reload();
          } else {
            alert('Gagal menghapus: ' + (data.message || 'Terjadi kesalahan.'));
          }
        })
        .catch(err => {
          alert('Kesalahan jaringan: ' + err.message);
        });
      }
    };
  }
</script>
@endsection
