@extends('admin.layoutCMS')

@section('content')
  <div class="space-y-6">

    <!-- Flash Message Success (Auto Hide 3s) -->
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

    <!-- Header Title -->
    <div>
      <h1 class="text-xl font-bold text-slate-800">Kelola Jemaat</h1>
      <p class="text-xs text-slate-500 mt-1">Database anggota dan tamu, impor Excel/CSV</p>
    </div>

    <!-- Toolbar Clean 1 Baris (Pencarian & Tombol Aksi) -->
    <div
      class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col xl:flex-row items-stretch xl:items-center justify-between gap-3">

      <!-- Left: Form Search Input -->
      <form method="GET" action="{{ route('admin.jemaat.index') }}"
        class="flex items-center gap-2 w-full xl:w-96 shrink-0">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <div
          class="flex items-center w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus-within:bg-white focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-600/20 transition-all">
          <i class="ri-search-2-line text-slate-400 text-sm mr-2 shrink-0"></i>
          <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Cari nama, alias, keluarga, wilayah..."
            class="w-full text-xs text-slate-800 bg-transparent placeholder-slate-400 outline-none border-none">
          <span
            class="text-[10px] font-semibold text-slate-500 bg-slate-200/70 px-2 py-0.5 rounded-md ml-2 shrink-0 whitespace-nowrap">
            {{ count($jemaats) }} baris
          </span>
        </div>
      </form>

      <!-- Right: Action Controls & Buttons -->
      <div class="flex flex-wrap items-center gap-2 justify-between xl:justify-end w-full xl:w-auto">

        <div class="flex flex-wrap items-center gap-2">
          <!-- Filter Status (Lebih Lebar & Bebas Bertabrakan) -->
          <form method="GET" action="{{ route('admin.jemaat.index') }}" id="filterStatusForm" class="shrink-0">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <select name="status" onchange="document.getElementById('filterStatusForm').submit()"
              class="w-48 min-w-[180px] text-xs bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl pl-4 pr-10 py-2.5 outline-none focus:border-blue-600 cursor-pointer transition">
              <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
              <option value="Anggota" {{ request('status') == 'Anggota' ? 'selected' : '' }}>Anggota</option>
              <option value="Tamu" {{ request('status') == 'Tamu' ? 'selected' : '' }}>Tamu</option>
            </select>
          </form>

          <!-- Ekspor CSV -->
          <button type="button"
            class="shrink-0 inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition active:scale-95">
            <i class="ri-download-2-line text-slate-500 text-sm"></i>
            <span>Ekspor CSV</span>
          </button>

          <!-- Template -->
          <button type="button"
            class="shrink-0 inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition active:scale-95">
            <i class="ri-file-text-line text-slate-500 text-sm"></i>
            <span>Template</span>
          </button>

          <!-- Impor Excel/CSV -->
          <button type="button"
            class="shrink-0 inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition active:scale-95">
            <i class="ri-upload-2-line text-slate-500 text-sm"></i>
            <span>Impor Excel/CSV</span>
          </button>
        </div>

        <!-- Tambah Anggota (Warna Biru Menyala di Ujung Kanan) -->
        <button type="button" onclick="document.getElementById('addJemaatModal').style.display='flex'"
          style="background-color: #2563eb !important; color: #ffffff !important;"
          class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl hover:bg-blue-700 text-white text-xs font-bold shadow-md hover:shadow-lg transition active:scale-95 ml-auto xl:ml-0">
          <i class="ri-add-line text-sm font-bold"></i>
          <span>Tambah Anggota</span>
        </button>
      </div>
    </div>

    <!-- Tabel Data Jemaat -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600">
              <th class="p-3.5 whitespace-nowrap">Nama Asli ↑</th>
              <th class="p-3.5 whitespace-nowrap">Status</th>
              <th class="p-3.5 whitespace-nowrap">Alias 1</th>
              <th class="p-3.5 whitespace-nowrap">Alias 2</th>
              <th class="p-3.5 whitespace-nowrap">Keluarga</th>
              <th class="p-3.5 whitespace-nowrap">Wilayah Ibadah</th>
              <th class="p-3.5 whitespace-nowrap">Alamat</th>
              <th class="p-3.5 whitespace-nowrap">Tempat Lahir</th>
              <th class="p-3.5 whitespace-nowrap">Tgl Lahir</th>
              <th class="p-3.5 whitespace-nowrap">Telepon</th>
              <th class="p-3.5 text-center whitespace-nowrap">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
            @forelse($jemaats as $item)
              <tr class="hover:bg-slate-50/50 transition">
                <td class="p-3.5 font-semibold text-slate-800">
                  <div class="flex items-center gap-2">
                    <span>{{ $item->nama_asli }}</span>
                    @if ($item->badge_tag)
                      <span class="bg-rose-600 text-white text-[10px] px-2 py-0.5 rounded leading-tight font-medium">
                        {{ $item->badge_tag }}
                      </span>
                    @endif
                  </div>
                </td>
                <td class="p-3.5">
                  <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-full font-medium">
                    {{ $item->status }}
                  </span>
                </td>
                <td class="p-3.5 text-slate-600">{{ $item->alias_1 ?? '-' }}</td>
                <td class="p-3.5 text-slate-600">{{ $item->alias_2 ?? '-' }}</td>
                <td class="p-3.5">
                  @if ($item->keluarga)
                    <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg font-medium border border-blue-100">
                      {{ $item->keluarga }}
                    </span>
                  @else
                    <span class="text-slate-400">-</span>
                  @endif
                </td>
                <td class="p-3.5 text-slate-600">{{ $item->cabang->nama_cabang ?? '-' }}</td>
                <td class="p-3.5 text-slate-600 max-w-xs truncate">{{ $item->alamat ?? '-' }}</td>
                <td class="p-3.5 text-slate-600">{{ $item->tempat_lahir ?? '-' }}</td>
                <td class="p-3.5 text-slate-600">
                  {{ $item->tgl_lahir ? $item->tgl_lahir->format('j M Y') : '-' }}
                </td>
                <td class="p-3.5 text-slate-600">{{ $item->telepon ?? '-' }}</td>
                <td class="p-3.5 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <button onclick="editJemaat({{ json_encode($item) }})"
                      class="p-1 text-slate-600 hover:text-amber-600 transition" title="Edit">
                      <i class="ri-pencil-line text-base"></i>
                    </button>
                    <form action="{{ route('admin.jemaat.destroy', $item->id) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data jemaat ini?')" class="inline">
                      @csrf @method('DELETE')
                      <button type="submit" class="p-1 text-slate-600 hover:text-red-600 transition" title="Hapus">
                        <i class="ri-delete-bin-line text-base"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" class="p-8 text-center text-slate-400">Belum ada data jemaat terdaftar.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- MODAL TAMBAH ANGGOTA -->
  <div id="addJemaatModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-2xl w-full max-w-xl shadow-2xl mx-4 max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-center mb-1 pb-2">
        <h3 class="font-bold text-slate-900 text-lg">Tambah Anggota</h3>
        <button type="button" onclick="document.getElementById('addJemaatModal').style.display='none'"
          class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
      </div>
      <p class="text-xs text-slate-500 mb-5">Nama asli wajib diisi. Kolom lain opsional.</p>

      <form action="{{ route('admin.jemaat.store') }}" method="POST" class="space-y-4 text-xs">
        @csrf
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Nama Asli *</label>
          <input type="text" name="nama_asli" placeholder="" required
            class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Alias 1</label>
            <input type="text" name="alias_1" placeholder=""
              class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
          </div>
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Alias 2</label>
            <input type="text" name="alias_2" placeholder=""
              class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Wilayah Ibadah</label>
            <select name="cabang_id"
              class="w-full border border-slate-300 rounded-xl p-3 bg-white outline-none focus:border-blue-600">
              <option value="">-- Pilih Wilayah --</option>
              @foreach ($cabangs as $cabang)
                <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Status</label>
            <select name="status"
              class="w-full border border-slate-300 rounded-xl p-3 bg-white outline-none focus:border-blue-600">
              <option value="Anggota">Anggota</option>
              <option value="Tamu">Tamu</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block font-semibold text-slate-700 mb-1">Alamat</label>
          <input type="text" name="alamat" placeholder=""
            class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" placeholder=""
              class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
          </div>
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Tanggal Lahir</label>
            <input type="date" name="tgl_lahir"
              class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
          </div>
        </div>

        <div>
          <label class="block font-semibold text-slate-700 mb-1">Telepon</label>
          <input type="text" name="telepon" placeholder=""
            class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
        </div>

        <!-- Input Keluarga Autocomplete dari Datalist -->
        <div>
          <label class="block font-semibold text-slate-700 mb-1">
            Keluarga <span class="text-slate-400 font-normal">(mis. "Kel. Augusta" - untuk tap satu keluarga di Mode
              Sentuh)</span>
          </label>
          <input type="text" name="keluarga" list="list-keluarga" placeholder="Ketik atau pilih nama keluarga"
            autocomplete="off" class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
          <button type="button" onclick="document.getElementById('addJemaatModal').style.display='none'"
            class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition">Batal</button>
          <button type="submit" style="background-color: #2563eb !important; color: #ffffff !important;"
            class="px-6 py-2.5 rounded-xl hover:bg-blue-700 text-white font-semibold transition shadow-md">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL EDIT ANGGOTA -->
  <div id="editJemaatModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-2xl w-full max-w-xl shadow-2xl mx-4 max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-center mb-1 pb-2">
        <h3 class="font-bold text-slate-900 text-lg">Edit Data Jemaat</h3>
        <button type="button" onclick="document.getElementById('editJemaatModal').style.display='none'"
          class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
      </div>
      <p class="text-xs text-slate-500 mb-5">Perbarui informasi data jemaat di bawah ini.</p>

      <form id="editJemaatForm" method="POST" class="space-y-4 text-xs">
        @csrf @method('PUT')
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Nama Asli *</label>
          <input type="text" id="edit_nama_asli" name="nama_asli" required
            class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Alias 1</label>
            <input type="text" id="edit_alias_1" name="alias_1"
              class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
          </div>
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Alias 2</label>
            <input type="text" id="edit_alias_2" name="alias_2"
              class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Wilayah Ibadah</label>
            <select id="edit_cabang_id" name="cabang_id"
              class="w-full border border-slate-300 rounded-xl p-3 bg-white outline-none focus:border-blue-600">
              <option value="">-- Pilih Wilayah --</option>
              @foreach ($cabangs as $cabang)
                <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Status</label>
            <select id="edit_status" name="status"
              class="w-full border border-slate-300 rounded-xl p-3 bg-white outline-none focus:border-blue-600">
              <option value="Anggota">Anggota</option>
              <option value="Tamu">Tamu</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block font-semibold text-slate-700 mb-1">Alamat</label>
          <input type="text" id="edit_alamat" name="alamat"
            class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Tempat Lahir</label>
            <input type="text" id="edit_tempat_lahir" name="tempat_lahir"
              class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
          </div>
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Tanggal Lahir</label>
            <input type="date" id="edit_tgl_lahir" name="tgl_lahir"
              class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
          </div>
        </div>

        <div>
          <label class="block font-semibold text-slate-700 mb-1">Telepon</label>
          <input type="text" id="edit_telepon" name="telepon"
            class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
        </div>

        <!-- Input Keluarga Edit Autocomplete -->
        <div>
          <label class="block font-semibold text-slate-700 mb-1">
            Keluarga <span class="text-slate-400 font-normal">(mis. "Kel. Augusta" - untuk tap satu keluarga di Mode
              Sentuh)</span>
          </label>
          <input type="text" id="edit_keluarga" name="keluarga" list="list-keluarga"
            placeholder="Ketik atau pilih nama keluarga" autocomplete="off"
            class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
          <button type="button" onclick="document.getElementById('editJemaatModal').style.display='none'"
            class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition">Batal</button>
          <button type="submit" style="background-color: #2563eb !important; color: #ffffff !important;"
            class="px-6 py-2.5 rounded-xl hover:bg-blue-700 text-white font-semibold transition shadow-md">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Datalist Referensi Nama Keluarga dari Database -->
  <datalist id="list-keluarga">
    @foreach ($keluargas ?? [] as $kel)
      <option value="{{ $kel }}">
    @endforeach
  </datalist>

  <script>
    function editJemaat(jemaat) {
      document.getElementById('editJemaatForm').action = "/admin/jemaat/" + jemaat.id;
      document.getElementById('edit_nama_asli').value = jemaat.nama_asli;
      document.getElementById('edit_status').value = jemaat.status;
      document.getElementById('edit_alias_1').value = jemaat.alias_1 || '';
      document.getElementById('edit_alias_2').value = jemaat.alias_2 || '';
      document.getElementById('edit_keluarga').value = jemaat.keluarga || '';
      document.getElementById('edit_cabang_id').value = jemaat.cabang_id || '';
      document.getElementById('edit_alamat').value = jemaat.alamat || '';
      document.getElementById('edit_tempat_lahir').value = jemaat.tempat_lahir || '';
      document.getElementById('edit_tgl_lahir').value = jemaat.tgl_lahir ? jemaat.tgl_lahir.split('T')[0] : '';
      document.getElementById('edit_telepon').value = jemaat.telepon || '';
      document.getElementById('editJemaatModal').style.display = 'flex';
    }
  </script>
@endsection
