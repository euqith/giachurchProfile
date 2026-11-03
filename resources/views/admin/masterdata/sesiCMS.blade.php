@extends('admin.layoutCMS')

@section('content')
  <div class="space-y-5 w-full">

    <!-- Flash Message Success (Auto Hide 3s) -->
    @if (session('success'))
      <div id="flash-success-alert" style="background-color: #10b981 !important; color: #ffffff !important;"
        class="px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between shadow-sm transition-all duration-500 ease-in-out border border-emerald-600">
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

    <!-- Flash Message Error -->
    @if (session('error'))
      <div id="flash-error-alert"
        class="px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between shadow-sm bg-rose-600 text-white border border-rose-700">
        <div class="flex items-center gap-2">
          <i class="ri-error-warning-line text-lg"></i>
          <span>{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-white font-bold leading-none">✕</button>
      </div>
    @endif

    <!-- Header Title & Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold mb-1.5 border border-amber-200/80">
          <i class="ri-time-line"></i> Master Sesi Waktu
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Master Data Sesi Waktu</h1>
        <p class="text-xs text-slate-500 mt-0.5">Kelola sesi waktu ibadah (misal: Pagi, Siang, Sore, Malam).</p>
      </div>
      <button type="button" onclick="document.getElementById('addSesiModal').style.display='flex'"
        class="btn-cms-primary">
        <i class="ri-add-line text-base"></i>
        <span>Tambah Sesi Baru</span>
      </button>
    </div>

    <!-- Container Utama Tabel Seamless -->
    <div class="table-modern-wrapper">
      <div class="overflow-x-auto w-full">
        <table class="w-full text-left border-collapse table-fixed">
          <thead>
            <tr
              class="bg-slate-50/90 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
              <th class="py-3.5 px-4 w-[10%] text-center">No</th>
              <th class="py-3.5 px-4 w-[60%]">Nama Sesi Waktu</th>
              <th class="py-3.5 px-4 w-[18%] text-center">Status Active</th>
              <th class="py-3.5 px-4 w-[12%] text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
            @forelse($sesis as $index => $item)
              <tr class="hover:bg-slate-50/80 transition-colors">

                <!-- Nomor -->
                <td class="py-3.5 px-4 text-center font-semibold text-slate-500 align-middle">
                  {{ $index + 1 }}
                </td>

                <!-- Nama Sesi Murni Tanpa Kata "Sesi" -->
                <td class="py-3.5 px-4 font-bold text-slate-800 align-middle break-words">
                  {{ $item->nama_sesi }}
                </td>

                <!-- Status Keaktifan -->
                <td class="py-3.5 px-4 text-center align-middle">
                  <form action="{{ route('admin.sesi.toggle', $item->id) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit"
                      class="px-3 py-1 rounded-full font-bold text-[11px] transition shadow-2xs {{ $item->isActive ? 'bg-emerald-50 text-emerald-600 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-400 border border-slate-200 hover:bg-slate-200' }}">
                      {{ $item->isActive ? 'Aktif' : 'Non-Aktif' }}
                    </button>
                  </form>
                </td>

                <!-- Aksi -->
                <td class="py-3.5 px-4 text-center align-middle">
                  <div class="flex items-center justify-center gap-1.5">
                    <button onclick="editSesi({{ json_encode($item) }})"
                      class="btn-action-edit"
                      title="Edit">
                      <i class="ri-pencil-line"></i> Edit
                    </button>
                    <form action="{{ route('admin.sesi.destroy', $item->id) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus sesi ini?')" class="inline m-0 p-0">
                      @csrf @method('DELETE')
                      <button type="submit"
                        class="btn-action-delete"
                        title="Hapus">
                        <i class="ri-delete-bin-line"></i> Hapus
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="p-8 text-center text-slate-400">Belum ada master data sesi waktu.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- MODAL TAMBAH SESI -->
  <div id="addSesiModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-2xl w-full max-w-md shadow-2xl mx-4 border border-slate-200">
      <div class="flex justify-between items-center mb-1 pb-2 border-b border-slate-100">
        <h3 class="font-bold text-slate-900 text-lg">Tambah Sesi Waktu</h3>
        <button type="button" onclick="document.getElementById('addSesiModal').style.display='none'"
          class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
      </div>

      <form action="{{ route('admin.sesi.store') }}" method="POST" class="space-y-4 text-xs mt-3">
        @csrf
        <div>
          <label class="block font-semibold text-slate-700 mb-1.5">Nama Sesi (misal: Pagi, Siang, Sore)</label>
          <input type="text" name="nama_sesi" required placeholder="Contoh: Pagi"
            class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 outline-none focus:border-blue-600 bg-slate-50 focus:bg-white text-sm">
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
          <button type="button" onclick="document.getElementById('addSesiModal').style.display='none'"
            class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition">Batal</button>
          <button type="submit" style="background-color: #2563eb !important; color: #ffffff !important;"
            class="px-6 py-2.5 rounded-xl hover:bg-blue-700 text-white font-semibold transition shadow-md">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL EDIT SESI -->
  <div id="editSesiModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-2xl w-full max-w-md shadow-2xl mx-4 border border-slate-200">
      <div class="flex justify-between items-center mb-1 pb-2 border-b border-slate-100">
        <h3 class="font-bold text-slate-900 text-lg">Edit Sesi Waktu</h3>
        <button type="button" onclick="document.getElementById('editSesiModal').style.display='none'"
          class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
      </div>

      <form id="editSesiForm" method="POST" class="space-y-4 text-xs mt-3">
        @csrf @method('PUT')
        <div>
          <label class="block font-semibold text-slate-700 mb-1.5">Nama Sesi (misal: Pagi, Siang, Sore)</label>
          <input type="text" id="edit_nama_sesi" name="nama_sesi" required
            class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 outline-none focus:border-blue-600 bg-slate-50 focus:bg-white text-sm">
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
          <button type="button" onclick="document.getElementById('editSesiModal').style.display='none'"
            class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition">Batal</button>
          <button type="submit" style="background-color: #2563eb !important; color: #ffffff !important;"
            class="px-6 py-2.5 rounded-xl hover:bg-blue-700 text-white font-semibold transition shadow-md">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function editSesi(sesi) {
      document.getElementById('editSesiForm').action = "/admin/sesi/" + sesi.id;
      document.getElementById('edit_nama_sesi').value = sesi.nama_sesi;
      document.getElementById('editSesiModal').style.display = 'flex';
    }
  </script>
@endsection
