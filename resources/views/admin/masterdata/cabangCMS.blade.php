@extends('admin.layoutCMS')

@section('content')
  <div class="space-y-6">

    <!-- Flash Message Success (Hijau Kebal Override & Auto Hide 3 Detik) -->
    @if (session('success'))
      <div id="flash-success-alert" style="background-color: #10b981 !important; color: #ffffff !important;"
        class="px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between shadow-lg transition-all duration-500 ease-in-out border border-emerald-600">
        <div class="flex items-center gap-2">
          <i class="ri-checkbox-circle-line text-lg"></i>
          <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="dismissAlert()"
          class="text-white hover:text-slate-200 font-bold transition text-base leading-none">✕</button>
      </div>

      <script>
        function dismissAlert() {
          const alert = document.getElementById('flash-success-alert');
          if (alert) {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
          }
        }

        // Otomatis hilang dalam 3 detik (3000ms)
        setTimeout(dismissAlert, 3000);
      </script>
    @endif

    <!-- Header & Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl font-bold text-slate-800">Master Data Cabang</h1>
        <p class="text-xs text-slate-500 mt-1">Kelola daftar cabang dan lokasi operasional gereja.</p>
      </div>
      <button onclick="document.getElementById('addCabangModal').style.display='flex'"
        class="flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark text-white text-xs font-semibold px-4 py-2.5 rounded-lg shadow-sm transition">
        <i class="ri-add-line text-base"></i> Tambah Cabang
      </button>
    </div>

    <!-- Tabel Data Cabang -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr
              class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
              <th class="p-4">No</th>
              <th class="p-4">Nama Cabang</th>
              <th class="p-4">Alamat</th>
              <th class="p-4">Status</th>
              <th class="p-4">Created By</th>
              <th class="p-4">Created Date</th>
              <th class="p-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
            @forelse($cabangs as $index => $item)
              <tr class="hover:bg-slate-50/50 transition">
                <td class="p-4 font-medium text-slate-500">{{ $index + 1 }}</td>
                <td class="p-4 font-semibold text-slate-800">{{ $item->nama_cabang }}</td>
                <td class="p-4 text-slate-600">{{ $item->lokasi ?? '-' }}</td>
                <td class="p-4">
                  @if ($item->isActive)
                    <span
                      class="bg-emerald-100 text-emerald-700 text-xs px-2.5 py-1 rounded-full font-semibold">Aktif</span>
                  @else
                    <span
                      class="bg-slate-100 text-slate-500 text-xs px-2.5 py-1 rounded-full font-semibold">Nonaktif</span>
                  @endif
                </td>
                <td class="p-4 text-xs text-slate-500">{{ $item->createdBy ?? '-' }}</td>
                <td class="p-4 text-xs text-slate-500">
                  {{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}
                </td>
                <td class="p-4 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <!-- Edit Button -->
                    <button onclick="editCabang({{ json_encode($item) }})"
                      class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
                      <i class="ri-pencil-line text-base"></i>
                    </button>

                    <!-- Delete Button (Red Color) -->
                    <form action="{{ route('admin.cabang.destroy', $item->id) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus cabang ini?')" class="inline">
                      @csrf @method('DELETE')
                      <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition"
                        title="Hapus">
                        <i class="ri-delete-bin-line text-base"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="p-8 text-center text-slate-400 text-xs">Belum ada data cabang terdaftar.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- MODAL TAMBAH CABANG -->
  <div id="addCabangModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-xl w-full max-w-md shadow-xl mx-4">
      <div class="flex justify-between items-center mb-4">
        <h5 class="font-bold text-slate-900 flex items-center gap-2">
          <i class="ri-building-line text-primary"></i> Tambah Cabang Baru
        </h5>
        <button type="button" onclick="document.getElementById('addCabangModal').style.display='none'"
          class="text-gray-400 hover:text-gray-600 font-bold">✕</button>
      </div>

      <form action="{{ route('admin.cabang.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Cabang</label>
          <input type="text" name="nama_cabang" placeholder="Contoh: Tengger / Gateway" required
            class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 outline-none focus:border-primary">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Alamat / Lokasi</label>
          <input type="text" name="lokasi" placeholder="Contoh: Jl. Raya Darmo No. 12"
            class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 outline-none focus:border-primary">
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="document.getElementById('addCabangModal').style.display='none'"
            class="px-4 py-2 rounded-lg bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200">Batal</button>
          <button type="submit"
            class="px-4 py-2 rounded-lg bg-primary text-white text-xs font-semibold hover:bg-primary-dark">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL EDIT CABANG -->
  <div id="editCabangModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-2xl w-full max-w-md shadow-2xl mx-4">
      <!-- Header Modal -->
      <div class="flex justify-between items-center mb-5 border-b border-slate-100 pb-3">
        <h5 class="font-bold text-slate-800 text-base flex items-center gap-2">
          <i class="ri-pencil-line text-primary"></i> Edit Data Cabang
        </h5>
        <button type="button" onclick="document.getElementById('editCabangModal').style.display='none'"
          class="text-slate-400 hover:text-slate-600 transition font-bold text-lg">✕</button>
      </div>

      <!-- Form Edit Cabang -->
      <form id="editCabangForm" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Cabang</label>
          <input type="text" id="edit_nama_cabang" name="nama_cabang" required
            class="w-full text-sm border border-slate-300 rounded-lg px-3.5 py-2 text-slate-800 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Alamat / Lokasi</label>
          <input type="text" id="edit_lokasi" name="lokasi"
            class="w-full text-sm border border-slate-300 rounded-lg px-3.5 py-2 text-slate-800 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status Cabang</label>
          <select id="edit_isActive" name="isActive"
            class="w-full text-sm border border-slate-300 rounded-lg px-3.5 py-2 text-slate-800 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition bg-white">
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </select>
        </div>

        <!-- Footer Action Buttons -->
        <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 mt-5">
          <button type="button" onclick="document.getElementById('editCabangModal').style.display='none'"
            class="px-4 py-2 rounded-lg bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200 transition">
            Batal
          </button>
          <button type="submit"
            class="px-5 py-2 rounded-lg bg-primary hover:bg-primary-dark text-white text-xs font-semibold shadow-sm transition">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function editCabang(cabang) {
      document.getElementById('editCabangForm').action = "/admin/cabang/" + cabang.id;
      document.getElementById('edit_nama_cabang').value = cabang.nama_cabang;
      document.getElementById('edit_lokasi').value = cabang.lokasi || '';
      document.getElementById('edit_isActive').value = cabang.isActive;
      document.getElementById('editCabangModal').style.display = 'flex';
    }
  </script>
@endsection
