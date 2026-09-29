@extends('admin.layoutCMS')

@section('content')
  <div class="space-y-6">

    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl font-bold text-slate-800">Riwayat Sesi Ibadah</h1>
        <p class="text-xs text-slate-500 mt-1">Daftar rekapan sesi ibadah dan jumlah kehadiran jemaat.</p>
      </div>
      <a href="{{ route('admin.attendance.input') }}"
        class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition">
        <i class="ri-add-line text-sm"></i> Input Kehadiran Baru
      </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <div
      class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
      <form method="GET" action="{{ route('admin.attendance.history') }}" class="flex items-center gap-2 w-full sm:w-80">
        <div
          class="flex items-center w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 focus-within:bg-white focus-within:border-blue-600 transition">
          <i class="ri-search-2-line text-slate-400 text-sm mr-2 shrink-0"></i>
          <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Cari ibadah, cabang, petugas..." class="w-full text-xs bg-transparent outline-none border-none">
        </div>
      </form>
      <span class="text-xs text-slate-500 font-medium">{{ count($sessions) }} sesi recorded</span>
    </div>

    <!-- Tabel Riwayat Sesi -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600">
              <th class="p-4">Tanggal</th>
              <th class="p-4">Sesi Ibadah</th>
              <th class="p-4">Lokasi Cabang</th>
              <th class="p-4">Petugas</th>
              <th class="p-4 text-center">Anggota</th>
              <th class="p-4 text-center">Tamu</th>
              <th class="p-4 text-center">Total Hadir</th>
              <th class="p-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
            @if (count($sessions) > 0)
              @foreach ($sessions as $session)
                <tr class="hover:bg-slate-50/50 transition">
                  <td class="p-4 font-medium text-slate-800">
                    {{ \Carbon\Carbon::parse($session->tanggal)->format('d/m/Y') }}
                  </td>
                  <td class="p-4 font-bold text-slate-800">{{ $session->nama_ibadah }}</td>
                  <td class="p-4 text-slate-600">{{ $session->nama_cabang }}</td>
                  <td class="p-4 text-slate-500">{{ $session->nama_petugas }}</td>
                  <td class="p-4 text-center font-medium text-blue-700">{{ $session->total_anggota }}</td>
                  <td class="p-4 text-center font-medium text-amber-600">{{ $session->total_tamu }}</td>
                  <td class="p-4 text-center">
                    <span class="bg-blue-100 text-blue-900 font-bold px-2.5 py-1 rounded-full text-xs">
                      {{ $session->total_hadir }} Orang
                    </span>
                  </td>
                  <td class="p-4 text-center">
                    <button onclick="viewDetail({{ $session->id }})"
                      class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition font-semibold text-[11px] inline-flex items-center gap-1">
                      <i class="ri-eye-line text-xs"></i> Lihat Jemaat
                    </button>
                  </td>
                </tr>
              @endforeach
            @else
              <tr>
                <td colspan="8" class="p-8 text-center text-slate-400">Belum ada riwayat sesi ibadah yang tersimpan.
                </td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- MODAL DETAIL DAFTAR JEMAAT HADIR -->
  <div id="detailSessionModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-3xl w-full max-w-lg shadow-2xl mx-4 max-h-[85vh] overflow-y-auto">
      <div class="flex justify-between items-center mb-3 border-b pb-3">
        <div>
          <h3 id="modal-title" class="font-bold text-slate-900 text-base">Detail Kehadiran</h3>
          <p id="modal-subtitle" class="text-xs text-slate-500 mt-0.5"></p>
        </div>
        <button type="button" onclick="document.getElementById('detailSessionModal').style.display='none'"
          class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
      </div>

      <div id="modal-detail-list" class="divide-y divide-slate-100 text-xs">
        <p class="text-center py-4 text-slate-400">Memuat data...</p>
      </div>

      <div class="mt-5 pt-3 border-t flex justify-end">
        <button type="button" onclick="document.getElementById('detailSessionModal').style.display='none'"
          class="px-5 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 text-xs">
          Tutup
        </button>
      </div>
    </div>
  </div>

  <script>
    function viewDetail(sessionId) {
      document.getElementById('detailSessionModal').style.display = 'flex';
      const listContainer = document.getElementById('modal-detail-list');
      listContainer.innerHTML = '<p class="text-center py-4 text-slate-400">Memuat data...</p>';

      fetch(`/admin/attendance/history/${sessionId}`)
        .then(res => res.json())
        .then(data => {
          document.getElementById('modal-title').textContent = `${data.nama_ibadah} - ${data.nama_cabang}`;
          document.getElementById('modal-subtitle').textContent =
            `Total Hadir: ${data.total_hadir} orang (${data.total_anggota} anggota, ${data.total_tamu} tamu)`;

          listContainer.innerHTML = '';
          if (!data.details || data.details.length === 0) {
            listContainer.innerHTML = '<p class="text-center py-4 text-slate-400">Tidak ada rincian jemaat.</p>';
            return;
          }

          data.details.forEach((item, index) => {
            const row = document.createElement('div');
            row.className = 'py-2.5 flex items-center justify-between';
            row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <span class="text-slate-400 w-4 font-medium">${index + 1}.</span>
                            <span class="font-bold text-slate-800">${item.nama_jemaat}</span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold ${item.status === 'Tamu' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800'}">
                            ${item.status}
                        </span>
                    `;
            listContainer.appendChild(row);
          });
        })
        .catch(err => {
          listContainer.innerHTML = '<p class="text-center py-4 text-rose-500">Gagal memuat rincian jemaat.</p>';
        });
    }
  </script>
@endsection
