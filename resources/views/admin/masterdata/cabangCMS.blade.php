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
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold mb-1.5 border border-amber-200/80">
          <i class="ri-map-pin-line"></i> Master Wilayah & Lokasi
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Master Data Wilayah</h1>
        <p class="text-xs text-slate-500 mt-0.5">Kelola daftar wilayah dan lokasi operasional gereja.</p>
      </div>
      <button onclick="document.getElementById('addCabangModal').style.display='flex'"
        class="btn-cms-primary">
        <i class="ri-add-line text-base"></i> Tambah Wilayah
      </button>
    </div>

    <!-- Tabel Data Wilayah -->
    <div class="table-modern-wrapper">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr
              class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
              <th class="p-4">No</th>
              <th class="p-4">Nama Wilayah</th>
              <th class="p-4">Alamat & Lokasi</th>
              <th class="p-4">Google Maps</th>
              <th class="p-4">Waktu Ibadah</th>
              <th class="p-4">Status</th>
              <th class="p-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
            @forelse($cabangs as $index => $item)
              <tr class="hover:bg-slate-50/50 transition">
                <td class="p-4 font-medium text-slate-500">{{ $index + 1 }}</td>
                <td class="p-4">
                  <div class="font-bold text-slate-800">{{ $item->nama_cabang }}</div>
                  <div class="text-[10.5px] text-slate-400">ID: #{{ $item->id }}</div>
                </td>
                <td class="p-4 text-slate-600 text-xs max-w-xs">{{ $item->lokasi ?? '-' }}</td>
                <td class="p-4">
                  @if($item->maps_url)
                    <a href="{{ $item->maps_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-semibold transition">
                      <i class="ri-map-pin-2-line text-blue-600"></i> Buka Maps
                    </a>
                  @else
                    <span class="text-[11px] text-slate-400 italic">Auto-query alamat</span>
                  @endif
                </td>
                <td class="p-4 text-xs text-slate-600 max-w-xs">
                  @if(!empty($item->jadwal_lines))
                    <div class="line-clamp-2 leading-relaxed">
                      {{ implode(' · ', $item->jadwal_lines) }}
                    </div>
                  @else
                    <span class="text-slate-400 italic">Jadwal default</span>
                  @endif
                </td>
                <td class="p-4">
                  @if ($item->isActive)
                    <span
                      class="bg-emerald-100 text-emerald-700 text-xs px-2.5 py-1 rounded-full font-semibold">Aktif</span>
                  @else
                    <span
                      class="bg-slate-100 text-slate-500 text-xs px-2.5 py-1 rounded-full font-semibold">Nonaktif</span>
                  @endif
                </td>
                <td class="p-4 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <!-- Edit Button -->
                    <button onclick="editCabang({{ json_encode($item) }})"
                      class="btn-action-edit" title="Edit">
                      <i class="ri-pencil-line"></i> Edit
                    </button>

                    <!-- Delete Button (Red Color) -->
                    <form action="{{ route('admin.cabang.destroy', $item->id) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus wilayah ini?')" class="inline m-0 p-0">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn-action-delete"
                        title="Hapus">
                        <i class="ri-delete-bin-line"></i> Hapus
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="p-8 text-center text-slate-400 text-xs">Belum ada data wilayah terdaftar.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- MODAL TAMBAH WILAYAH -->
  <div id="addCabangModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-2xl w-full max-w-lg shadow-2xl mx-4 max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
        <h5 class="font-bold text-slate-900 flex items-center gap-2">
          <i class="ri-building-line text-primary"></i> Tambah Wilayah Baru
        </h5>
        <button type="button" onclick="document.getElementById('addCabangModal').style.display='none'"
          class="text-gray-400 hover:text-gray-600 font-bold">✕</button>
      </div>

      <form action="{{ route('admin.cabang.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Wilayah <span class="text-red-500">*</span></label>
          <input type="text" name="nama_cabang" placeholder="Contoh: Darmo / Bromo / Gateway" required
            class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 outline-none focus:border-primary">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat / Lokasi Lengkap</label>
          <input type="text" name="lokasi" placeholder="Contoh: Jl. Raya Darmo Harapan No.PF-1, Surabaya"
            class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 outline-none focus:border-primary">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1 flex items-center gap-1.5">
            <i class="ri-map-pin-2-line text-blue-600"></i> Link Google Maps (Share Link / Petunjuk Arah)
          </label>
          <input type="text" name="maps_url" placeholder="https://maps.app.goo.gl/... atau https://maps.google.com/?q=..."
            class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 outline-none focus:border-primary">
          <p class="text-[11px] text-slate-400 mt-1">Salin link dari tombol "Bagikan / Share" di Google Maps.</p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1 flex items-center gap-1.5">
            <i class="ri-map-2-line text-blue-600"></i> Google Maps Embed (URL / Tag &lt;iframe&gt;)
          </label>
          <textarea name="maps_embed" rows="2" placeholder="Tempel URL embed (https://www.google.com/maps/embed?...) atau tag <iframe> dari Google Maps"
            class="w-full text-xs font-mono border border-slate-300 rounded-lg px-3 py-2 outline-none focus:border-primary"></textarea>
          <p class="text-[11px] text-slate-400 mt-1">Opsional. Jika dikosongkan, sistem otomatis membuat peta embed interaktif berdasarkan alamat wilayah.</p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1 flex items-center gap-1.5">
            <i class="ri-time-line text-amber-600"></i> Waktu / Jadwal Ibadah Wilayah
          </label>
          <textarea name="jadwal_ibadah" rows="3" placeholder="Tulis 1 baris per jadwal. Contoh:
Ibadah 1: Minggu 06.00 WIB
Ibadah 2: Minggu 08.00 WIB
Doa Tengah Minggu: Rabu 18.30 WIB"
            class="w-full text-xs border border-slate-300 rounded-lg px-3 py-2 outline-none focus:border-primary"></textarea>
        </div>
        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
          <button type="button" onclick="document.getElementById('addCabangModal').style.display='none'"
            class="px-4 py-2 rounded-lg bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200">Batal</button>
          <button type="submit"
            class="px-4 py-2 rounded-lg bg-primary text-white text-xs font-semibold hover:bg-primary-dark">Simpan Wilayah</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL EDIT WILAYAH -->
  <div id="editCabangModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-2xl w-full max-w-lg shadow-2xl mx-4 max-h-[90vh] overflow-y-auto">
      <!-- Header Modal -->
      <div class="flex justify-between items-center mb-5 border-b border-slate-100 pb-3">
        <h5 class="font-bold text-slate-800 text-base flex items-center gap-2">
          <i class="ri-pencil-line text-primary"></i> Edit Data & Maps Wilayah
        </h5>
        <button type="button" onclick="document.getElementById('editCabangModal').style.display='none'"
          class="text-slate-400 hover:text-slate-600 transition font-bold text-lg">✕</button>
      </div>

      <!-- Form Edit Wilayah -->
      <form id="editCabangForm" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Wilayah <span class="text-red-500">*</span></label>
          <input type="text" id="edit_nama_cabang" name="nama_cabang" required
            class="w-full text-sm border border-slate-300 rounded-lg px-3.5 py-2 text-slate-800 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat / Lokasi Lengkap</label>
          <input type="text" id="edit_lokasi" name="lokasi"
            class="w-full text-sm border border-slate-300 rounded-lg px-3.5 py-2 text-slate-800 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5 flex items-center gap-1.5">
            <i class="ri-map-pin-2-line text-blue-600"></i> Link Google Maps (Share Link / Petunjuk Arah)
          </label>
          <input type="text" id="edit_maps_url" name="maps_url" placeholder="https://maps.app.goo.gl/... atau https://maps.google.com/?q=..."
            class="w-full text-sm border border-slate-300 rounded-lg px-3.5 py-2 text-slate-800 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
          <p class="text-[11px] text-slate-400 mt-1">Salin link dari tombol "Bagikan / Share" di Google Maps.</p>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5 flex items-center gap-1.5">
            <i class="ri-map-2-line text-blue-600"></i> Google Maps Embed (URL / Tag &lt;iframe&gt;)
          </label>
          <textarea id="edit_maps_embed" name="maps_embed" rows="2" placeholder="Tempel URL embed (https://www.google.com/maps/embed?...) atau tag <iframe> dari Google Maps"
            class="w-full text-xs font-mono border border-slate-300 rounded-lg px-3.5 py-2 text-slate-800 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition"></textarea>
          <p class="text-[11px] text-slate-400 mt-1">Opsional. Jika dikosongkan, sistem otomatis membuat peta embed interaktif berdasarkan alamat wilayah.</p>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5 flex items-center gap-1.5">
            <i class="ri-time-line text-amber-600"></i> Waktu / Jadwal Ibadah Wilayah
          </label>
          <textarea id="edit_jadwal_ibadah" name="jadwal_ibadah" rows="3" placeholder="Tulis 1 baris per sesi ibadah"
            class="w-full text-xs border border-slate-300 rounded-lg px-3.5 py-2 text-slate-800 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition"></textarea>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status Wilayah</label>
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
            Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function editCabang(cabang) {
      document.getElementById('editCabangForm').action = "/admin/cabang/" + cabang.id;
      document.getElementById('edit_nama_cabang').value = cabang.nama_cabang || '';
      document.getElementById('edit_lokasi').value = cabang.lokasi || '';
      document.getElementById('edit_maps_url').value = cabang.maps_url || '';
      document.getElementById('edit_maps_embed').value = cabang.maps_embed || '';
      document.getElementById('edit_jadwal_ibadah').value = cabang.jadwal_ibadah || '';
      document.getElementById('edit_isActive').value = cabang.isActive;
      document.getElementById('editCabangModal').style.display = 'flex';
    }
  </script>
@endsection
