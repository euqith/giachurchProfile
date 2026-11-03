@extends('admin.layoutCMS')

@section('content')
  <div class="flex justify-between items-center mb-6">
    <div>
      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold mb-1.5 border border-amber-200/80">
        <i class="ri-article-line"></i> Publikasi Digital
      </div>
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Master Kontrol Warta Jemaat</h1>
      <p class="text-xs text-slate-500 mt-0.5">Kelola edisi mingguan dan unggah dokumen presentasi warta digital.</p>
    </div>
  </div>

  {{-- Alert Sukses --}}
  @if (session('success'))
    <div id="success-alert"
      style="background-color: #10b981 !important; color: white !important; border: none !important; opacity: 1 !important; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
      ✅ {{ session('success') }}
    </div>

    <script>
      setTimeout(() => {
        document.getElementById('success-alert').style.display = 'none';
      }, 3000);
    </script>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- KIRI: Form & Tabel --}}
    <div class="space-y-6">
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-100">
        <h5 class="text-slate-900 font-bold mb-4 flex items-center">
          <i class="fa-solid fa-folder-plus text-emerald-500 me-2"></i>Buat Edisi Baru
        </h5>
        <form action="{{ route('admin.warta.store') }}" method="POST" class="flex flex-wrap items-end gap-4">
          @csrf
          <div class="flex-1" style="min-width: 220px;">
            <label class="block text-slate-500 text-xs font-bold mb-1.5">Judul Edisi</label>
            <input type="text" name="judul_edisi" class="w-full text-sm border-gray-200 rounded px-3 py-2"
              placeholder="Edisi Minggu..." required>
          </div>
          <div class="w-40">
            <label class="block text-slate-500 text-xs font-bold mb-1.5">Tanggal</label>
            <input type="date" name="tanggal_rilis" class="w-full text-sm border-gray-200 rounded px-3 py-2" required>
          </div>
          <button type="submit"
            class="btn-cms-primary text-xs">Daftarkan</button>
        </form>
      </div>

      <div class="table-modern-wrapper">
        <table class="w-full text-sm text-left">
          <thead>
            <tr>
              <th class="px-4 py-3">Edisi</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($wartas as $w)
              <tr class="{{ isset($warta) && $warta->id == $w->id ? 'bg-amber-50/50' : '' }}">
                <td class="px-4 py-3 font-semibold text-slate-900">{{ $w->judul_edisi }}</td>

                {{-- Kolom Status --}}
                <td class="px-4 py-3">
                  <form action="{{ route('admin.warta.toggle', $w->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                      class="px-3 py-1 rounded-full text-[11px] font-bold border transition cursor-pointer {{ $w->isActive ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                      {{ $w->isActive ? '🟢 AKTIF' : '⚪ NON-AKTIF' }}
                    </button>
                  </form>
                </td>

                {{-- Kolom Aksi --}}
                <td class="px-4 py-3">
                  <div class="flex items-center justify-center gap-1.5">
                    <!-- Tombol Edit -->
                    <button type="button"
                      onclick="openEditModal('{{ $w->id }}', '{{ $w->judul_edisi }}', '{{ $w->tanggal_rilis }}')"
                      class="btn-action-edit">
                      <i class="ri-edit-line"></i> Edit
                    </button>

                    <!-- Tombol Slide -->
                    <a href="{{ url('admin/warta?warta_id=' . $w->id) }}"
                      class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-50 text-sky-700 hover:bg-sky-100 border border-sky-200 transition">
                      <i class="ri-slideshow-line"></i> Slide
                    </a>

                    <!-- Tombol Hapus -->
                    <form action="{{ route('admin.warta.destroy', $w->id) }}" method="POST"
                      onsubmit="return confirm('Hapus edisi ini?')" style="margin: 0; padding: 0;">
                      @csrf @method('DELETE')
                      <button type="submit"
                        class="btn-action-delete">
                        <i class="ri-delete-bin-line"></i> Hapus
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center p-4">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- KANAN: Panel Upload --}}
    <div class="space-y-6">
      @if (isset($warta))
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border-l-4 border-primary">
          <h5 class="text-slate-900 dark:text-slate-200 font-bold mb-4">
            <i class="fa-solid fa-file-pdf text-red-500 me-2"></i>Upload Materi: {{ $warta->judul_edisi }}
          </h5>

          {{-- 🎯 INFO FILE JIKA SUDAH PERNAH DIUPLOAD --}}
          @if ($warta->slides->count() > 0)
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-center gap-3">
              <i class="fa-solid fa-file-pdf text-2xl text-red-500"></i>
              <div class="overflow-hidden">
                <p class="text-xs font-bold text-blue-800 truncate">{{ $warta->slides->first()->file_gambar }}</p>
                <p class="text-[10px] text-blue-600">Dokumen aktif terpasang</p>
              </div>
            </div>
          @endif

          <form action="{{ route('admin.warta.slides.store', $warta->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
              <label class="block text-slate-500 text-xs font-bold mb-1.5">Pilih 1 File PDF</label>
              <input type="file" name="file_pdf" accept=".pdf"
                class="w-full text-sm border border-gray-200 rounded px-3 py-2" required>
            </div>
            <button type="submit"
              class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-2 rounded transition cursor-pointer relative z-20">
              {{ $warta->slides->count() > 0 ? 'GANTI DOKUMEN PDF' : 'UNGGAH DOKUMEN PDF' }}
            </button>
          </form>
        </div>
      @else
        <div class="bg-gray-50 border-2 border-dashed border-gray-300 p-10 text-center text-gray-400 rounded-lg">
          <i class="fa-solid fa-file-arrow-up text-4xl mb-3"></i>
          <p>Pilih edisi dari tabel kiri untuk mulai mengelola dokumen warta.</p>
        </div>
      @endif
    </div>
  </div>
@endsection
<div id="editWartaModal" class="hidden"
  style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
  <div class="bg-white p-6 rounded-lg w-96">
    <h5 class="font-bold mb-4">Edit Warta</h5>
    <form id="editForm" method="POST">
      @csrf @method('PUT')
      <input type="text" name="judul_edisi" id="edit_judul" class="w-full border p-2 mb-2" required>
      <input type="date" name="tanggal_rilis" id="edit_tanggal" class="w-full border p-2 mb-4" required>
      <div class="flex justify-end gap-2">
        <button type="button" onclick="document.getElementById('editWartaModal').style.display='none'"
          class="px-4 py-2 bg-gray-200 rounded">Batal</button>
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
  // Fungsi untuk membuka modal secara paksa
  function openEditModal(id, judul, tanggal) {
    const modal = document.getElementById('editWartaModal');
    const form = document.getElementById('editForm');

    form.action = `/admin/warta/${id}`;
    document.getElementById('edit_judul').value = judul;
    document.getElementById('edit_tanggal').value = tanggal;

    modal.style.display = 'flex'; // Paksa tampil
  }
</script>
