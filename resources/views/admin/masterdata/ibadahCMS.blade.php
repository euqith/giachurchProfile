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
        setTimeout(dismissAlert, 3000);
      </script>
    @endif

    <!-- Header & Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl font-bold text-slate-800">Master Data Jenis Ibadah</h1>
        <p class="text-xs text-slate-500 mt-1">Kelola kategori dan jenis layanan ibadah gereja.</p>
      </div>
      <button onclick="document.getElementById('addIbadahModal').style.display='flex'"
        class="flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark text-white text-xs font-semibold px-4 py-2.5 rounded-lg shadow-sm transition">
        <i class="ri-add-line text-base"></i> Tambah Jenis Ibadah
      </button>
    </div>

    <!-- Tabel Data Jenis Ibadah -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr
              class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
              <th class="p-4">No</th>
              <th class="p-4">Nama Ibadah</th>
              <th class="p-4">Deskripsi</th>
              <th class="p-4">Status</th>
              <th class="p-4">Created By</th>
              <th class="p-4">Created Date</th>
              <th class="p-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
            @forelse($ibadahs as $index => $item)
              <tr class="hover:bg-slate-50/50 transition">
                <td class="p-4 font-medium text-slate-500">{{ $index + 1 }}</td>
                <td class="p-4 font-semibold text-slate-800">{{ $item->nama_ibadah }}</td>
                <td class="p-4 text-slate-600">{{ $item->deskripsi ?? '-' }}</td>
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
                    <button onclick="editIbadah({{ json_encode($item) }})"
                      class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
                      <i class="ri-pencil-line text-base"></i>
                    </button>

                    <!-- Delete Button -->
                    <form action="{{ route('admin.ibadah.destroy', $item->id) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis ibadah ini?')" class="inline">
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
                <td colspan="7" class="p-8 text-center text-slate-400 text-xs">Belum ada data jenis ibadah terdaftar.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- MODAL TAMBAH IBADAH -->
  <div id="addIbadahModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-2xl w-full max-w-md shadow-2xl mx-4">
      <div class="flex justify-between items-center mb-5 border-b border-slate-100 pb-3">
        <h5 class="font-bold text-slate-800 text-base flex items-center gap-2">
          <i class="ri-add-circle-line text-primary"></i> Tambah Jenis Ibadah
        </h5>
        <button type="button" onclick="document.getElementById('addIbadahModal').style.display='none'"
          class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
      </div>

      <form action="{{ route('admin.ibadah.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Ibadah</label>
          <input type="text" name="nama_ibadah" placeholder="Contoh: Ibadah Umum / Ibadah Pemuda" required
            class="w-full text-sm border border-slate-300 rounded-lg px-3.5 py-2 text-slate-800 outline-none focus:border-primary">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Deskripsi / Keterangan</label>
          <textarea name="deskripsi" rows="3" placeholder="Contoh: Ibadah setiap hari Minggu jam 09.00 WIB"
            class="w-full text-sm border border-slate-300 rounded-lg px-3.5 py-2 text-slate-800 outline-none focus:border-primary"></textarea>
        </div>
        <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 mt-5">
          <button type="button" onclick="document.getElementById('addIbadahModal').style.display='none'"
            class="px-4 py-2 rounded-lg bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200">Batal</button>
          <button type="submit"
            class="px-5 py-2 rounded-lg bg-primary hover:bg-primary-dark text-white text-xs font-semibold shadow-sm">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL EDIT IBADAH -->
  <div id="editIbadahModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-2xl w-full max-w-md shadow-2xl mx-4">
      <div class="flex justify-between items-center mb-5 border-b border-slate-100 pb-3">
        <h5 class="font-bold text-slate-800 text-base flex items-center gap-2">
          <i class="ri-pencil-line text-primary"></i> Edit Jenis Ibadah
        </h5>
        <button type="button" onclick="document.getElementById('editIbadahModal').style.display='none'"
          class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
      </div>

      <form id="editIbadahForm" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Ibadah</label>
          <input type="text" id="edit_nama_ibadah" name="nama_ibadah" required
            class="w-full text-sm border border-slate-300 rounded-lg px-3.5 py-2 text-slate-800 outline-none focus:border-primary">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Deskripsi / Keterangan</label>
          <textarea id="edit_deskripsi" name="deskripsi" rows="3"
            class="w-full text-sm border border-slate-300 rounded-lg px-3.5 py-2 text-slate-800 outline-none focus:border-primary"></textarea>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status Ibadah</label>
          <select id="edit_isActive" name="isActive"
            class="w-full text-sm border border-slate-300 rounded-lg px-3.5 py-2 text-slate-800 outline-none focus:border-primary bg-white">
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </select>
        </div>
        <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 mt-5">
          <button type="button" onclick="document.getElementById('editIbadahModal').style.display='none'"
            class="px-4 py-2 rounded-lg bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200">Batal</button>
          <button type="submit"
            class="px-5 py-2 rounded-lg bg-primary hover:bg-primary-dark text-white text-xs font-semibold shadow-sm">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function editIbadah(ibadah) {
      document.getElementById('editIbadahForm').action = "/admin/ibadah/" + ibadah.id;
      document.getElementById('edit_nama_ibadah').value = ibadah.nama_ibadah;
      document.getElementById('edit_deskripsi').value = ibadah.deskripsi || '';
      document.getElementById('edit_isActive').value = ibadah.isActive;
      document.getElementById('editIbadahModal').style.display = 'flex';
    }
  </script>
@endsection
