@extends('admin.layoutCMS')

@section('content')
  <div class="space-y-6">

    <!-- Header Title & Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl font-bold text-slate-800">Master Data Sesi Waktu</h1>
        <p class="text-xs text-slate-500 mt-1">Kelola sesi waktu ibadah (misal: Pagi, Siang, Sore, Malam).</p>
      </div>
      <button type="button" onclick="openAddModal()"
        class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition">
        <i class="ri-add-line text-sm"></i> Tambah Sesi Baru
      </button>
    </div>

    <!-- Alert Success -->
    @if (session('success'))
      <div
        class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">✕</button>
      </div>
    @endif

    <!-- Alert Error -->
    @if ($errors->any())
      <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
        <ul class="list-disc list-inside space-y-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- Tabel Data Sesi -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600">
              <th class="p-4 w-16 text-center">No</th>
              <th class="p-4">Nama Sesi Waktu</th>
              <th class="p-4 text-center">Status Active</th>
              <th class="p-4 text-center w-36">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
            @forelse($sesis as $index =>$s)
              <tr class="hover:bg-slate-50/50 transition">
                <td class="p-4 text-center font-medium text-slate-400">{{ $index + 1 }}</td>
                <td class="p-4 font-bold text-slate-800">
                  Sesi {{ $s->nama_sesi }}
                </td>
                <td class="p-4 text-center">
                  <form action="{{ route('admin.sesi.toggle', $s->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                      class="px-3 py-1 rounded-full text-[10px] font-bold transition {{ $s->isActive ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                      {{ $s->isActive ? 'Aktif' : 'Non-Aktif' }}
                    </button>
                  </form>
                </td>
                <td class="p-4 text-center space-x-1">
                  <button type="button" onclick="openEditModal({{ $s->id }}, '{{ $s->nama_sesi }}')"
                    class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                    <i class="ri-pencil-line"></i>
                  </button>

                  <form action="{{ route('admin.sesi.destroy', $s->id) }}" method="POST" class="inline"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus sesi ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition">
                      <i class="ri-delete-bin-line"></i>
                    </button>
                  </form>
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

  <!-- MODAL TAMBAH & EDIT SESI -->
  <div id="sesiModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-3xl w-full max-w-md shadow-2xl mx-4">

      <div class="flex justify-between items-center mb-4 border-b pb-3">
        <h3 id="modalTitle" class="font-bold text-slate-900 text-base">Tambah Sesi Baru</h3>
        <button type="button" onclick="closeModal()"
          class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
      </div>

      <form id="sesiForm" method="POST" action="{{ route('admin.sesi.store') }}" class="space-y-4 text-xs">
        @csrf
        <input type="hidden" id="methodField" name="_method" value="POST">

        <div>
          <label class="block font-semibold text-slate-700 mb-1">Nama Sesi (misal: Pagi, Siang, Sore)</label>
          <input type="text" id="input_nama_sesi" name="nama_sesi" placeholder="Contoh: Pagi" required
            class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 outline-none focus:border-blue-600 bg-white">
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t">
          <button type="button" onclick="closeModal()"
            class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200">
            Batal
          </button>
          <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700">
            Simpan
          </button>
        </div>
      </form>

    </div>
  </div>

  <script>
    const sesiModal = document.getElementById('sesiModal');
    const sesiForm = document.getElementById('sesiForm');
    const modalTitle = document.getElementById('modalTitle');
    const inputNamaSesi = document.getElementById('input_nama_sesi');
    const methodField = document.getElementById('methodField');

    function openAddModal() {
      modalTitle.textContent = 'Tambah Sesi Baru';
      sesiForm.action = "{{ route('admin.sesi.store') }}";
      methodField.value = 'POST';
      inputNamaSesi.value = '';
      sesiModal.style.display = 'flex';
    }

    function openEditModal(id, nama) {
      modalTitle.textContent = 'Edit Sesi Waktu';
      sesiForm.action = `/admin/sesi/${id}`;
      methodField.value = 'PUT';
      inputNamaSesi.value = nama;
      sesiModal.style.display = 'flex';
    }

    function closeModal() {
      sesiModal.style.display = 'none';
    }
  </script>
@endsection
