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

    <!-- Header Title -->
    <div>
      <h1 class="text-xl font-bold text-slate-800">Kelola Jemaat</h1>
      <p class="text-xs text-slate-500 mt-0.5">Database anggota dan tamu, impor Excel/CSV</p>
    </div>

    <!-- Container Utama Seamless -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-4 w-full">

      <!-- Form Filter Utama -->
      <form method="GET" action="{{ route('admin.jemaat.index') }}" id="mainFilterForm" class="space-y-3 w-full">

        <!-- Search Input Panjang -->
        <div
          class="flex items-center w-full bg-slate-50/80 border border-slate-200 rounded-xl px-3.5 py-2 focus-within:bg-white focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-600/15 transition-all">
          <i class="ri-search-2-line text-slate-400 text-sm mr-2.5 shrink-0"></i>
          <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, alias, wilayah..."
            class="w-full text-xs text-slate-800 bg-transparent placeholder-slate-400 outline-none border-none"
            onchange="document.getElementById('mainFilterForm').submit()">
          <span
            class="text-[11px] font-semibold text-slate-400 border-l border-slate-200 pl-3 ml-2 shrink-0 whitespace-nowrap">
            {{ is_object($jemaats) && method_exists($jemaats, 'total') ? $jemaats->total() : count($jemaats) }} baris
          </span>
        </div>

        <!-- Toolbar Baris Ke-2: Filter Status & Buttons di Kiri, Per Page di Kanan -->
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">

          <!-- Kiri: Filter Status Lebar Pas & Semua Tombol Aksi -->
          <div class="flex flex-wrap items-center gap-2">

            <!-- Dropdown Status (Fixed Width & Extra Right Padding) -->
            <select name="status" onchange="document.getElementById('mainFilterForm').submit()"
              style="width: 175px !important; min-width: 175px !important; padding-right: 2rem !important;"
              class="text-xs bg-slate-50/80 border border-slate-200 text-slate-700 font-semibold rounded-xl pl-3.5 py-2 outline-none focus:bg-white focus:border-blue-600 cursor-pointer transition shrink-0">
              <option value="semua" {{ request('status', 'semua') == 'semua' ? 'selected' : '' }}>Semua Status</option>
              <option value="Anggota" {{ request('status') == 'Anggota' ? 'selected' : '' }}>Anggota</option>
              <option value="Tamu" {{ request('status') == 'Tamu' ? 'selected' : '' }}>Tamu</option>
            </select>

            <!-- Ekspor CSV -->
            <a href="{{ route('admin.jemaat.export') }}"
              class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition active:scale-95 shadow-2xs">
              <i class="ri-download-2-line text-slate-500 text-sm"></i>
              <span>Ekspor CSV</span>
            </a>

            <!-- Download Template -->
            <a href="{{ route('admin.jemaat.template') }}"
              class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition active:scale-95 shadow-2xs">
              <i class="ri-file-excel-2-line text-slate-500 text-sm"></i>
              <span>Template</span>
            </a>

            <!-- Impor Excel/CSV -->
            <button type="button" onclick="document.getElementById('importJemaatModal').style.display='flex'"
              class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition active:scale-95 shadow-2xs">
              <i class="ri-upload-2-line text-slate-500 text-sm"></i>
              <span>Impor Excel/CSV</span>
            </button>

            <!-- Tambah Anggota -->
            <button type="button" onclick="document.getElementById('addJemaatModal').style.display='flex'"
              style="background-color: #1e3a8a !important; color: #ffffff !important;"
              class="shrink-0 inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl hover:bg-blue-900 text-white text-xs font-bold shadow-sm hover:shadow transition active:scale-95">
              <i class="ri-add-line text-sm font-bold"></i>
              <span>+ Tambah Anggota</span>
            </button>

          </div>

          <!-- Kanan: Filter Row Per Page -->
          <div
            class="flex items-center gap-1.5 bg-slate-50/80 border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-600 font-medium shrink-0">
            <span>Tampilkan:</span>
            <select name="per_page" onchange="document.getElementById('mainFilterForm').submit()"
              class="bg-transparent text-xs text-slate-800 font-bold outline-none cursor-pointer">
              <option value="50" {{ request('per_page', '50') == '50' ? 'selected' : '' }}>50</option>
              <option value="5" {{ request('per_page') == '5' ? 'selected' : '' }}>5</option>
              <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
              <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
              <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
            </select>
          </div>

        </div>
      </form>

      <!-- Tabel Seamless -->
      <div class="border border-slate-200/80 rounded-xl overflow-hidden w-full">
        <table class="w-full text-left border-collapse table-fixed">
          <thead>
            <tr
              class="bg-slate-50/90 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
              <th class="py-3.5 px-3 w-[15%]">Nama Asli ↑</th>
              <th class="py-3.5 px-3 w-[9%]">Status</th>
              <th class="py-3.5 px-2 w-[7%]">Alias 1</th>
              <th class="py-3.5 px-2 w-[6%]">Alias 2</th>
              <th class="py-3.5 px-3 w-[13%]">Keluarga</th>
              <th class="py-3.5 px-2 w-[9%]">Wilayah</th>
              <th class="py-3.5 px-3 w-[18%]">Alamat</th>
              <th class="py-3.5 px-2 w-[8%]">Tmpt Lahir</th>
              <th class="py-3.5 px-2 w-[8%]">Tgl Lahir</th>
              <th class="py-3.5 px-2 w-[8%]">Telepon</th>
              <th class="py-3.5 px-2 w-[4%] text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
            @forelse($jemaats as $item)
              <tr class="hover:bg-slate-50/80 transition-colors">

                <!-- Nama Asli -->
                <td class="py-3.5 px-3 font-semibold text-slate-800 break-words align-middle">
                  <div class="flex flex-col gap-0.5">
                    <span>{{ $item->nama_asli }}</span>
                    @if ($item->badge_tag)
                      <span
                        class="bg-rose-50 text-rose-600 border border-rose-200 text-[9px] px-1.5 py-0.2 rounded font-medium w-fit">
                        {{ $item->badge_tag }}
                      </span>
                    @endif
                  </div>
                </td>

                <!-- Status Badge -->
                <td class="py-3.5 px-3 align-middle">
                  <span
                    class="inline-block bg-slate-100 text-slate-700 px-3 py-1 rounded-lg font-bold text-xs shadow-2xs">
                    {{ $item->status ?? 'Anggota' }}
                  </span>
                </td>

                <!-- Alias 1 & Alias 2 -->
                <td class="py-3.5 px-2 text-slate-600 break-words align-middle">{{ $item->alias_1 ?? '-' }}</td>
                <td class="py-3.5 px-2 text-slate-600 break-words align-middle">{{ $item->alias_2 ?? '-' }}</td>

                <!-- Highlight Keluarga Biru -->
                <td class="py-3.5 px-3 align-middle">
                  @if ($item->keluarga)
                    <span
                      style="background-color: #eef2ff !important; color: #3730a3 !important; border: 1px solid #c7d2fe !important;"
                      class="inline-block px-3 py-1.5 rounded-xl font-semibold text-xs leading-snug break-words max-w-full shadow-2xs">
                      {{ $item->keluarga }}
                    </span>
                  @else
                    <span class="text-slate-300">-</span>
                  @endif
                </td>

                <!-- Wilayah Ibadah -->
                <td class="py-3.5 px-2 text-slate-600 break-words align-middle">
                  {{ $item->cabang->nama_cabang ?? ($item->wilayah_ibadah ?? '-') }}
                </td>

                <!-- Alamat -->
                <td class="py-3.5 px-3 text-slate-600 break-words align-middle leading-relaxed">
                  {{ $item->alamat ?? '-' }}
                </td>

                <!-- Tempat Lahir & Tanggal Lahir -->
                <td class="py-3.5 px-2 text-slate-600 break-words align-middle">{{ $item->tempat_lahir ?? '-' }}</td>
                <td class="py-3.5 px-2 text-slate-600 break-words align-middle">
                  {{ $item->tanggal_lahir ? date('j M Y', strtotime($item->tanggal_lahir)) : ($item->tgl_lahir ? $item->tgl_lahir->format('j M Y') : '-') }}
                </td>

                <!-- Telepon -->
                <td class="py-3.5 px-2 text-slate-600 break-words align-middle">{{ $item->telepon ?? '-' }}</td>

                <!-- Aksi -->
                <td class="py-3.5 px-2 text-center align-middle">
                  <div class="flex items-center justify-center gap-2">
                    <button onclick="editJemaat({{ json_encode($item) }})"
                      class="p-1 text-slate-500 hover:text-slate-800 transition" title="Edit">
                      <i class="ri-pencil-line text-base"></i>
                    </button>
                    <form action="{{ route('admin.jemaat.destroy', $item->id) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data jemaat ini?')" class="inline">
                      @csrf @method('DELETE')
                      <button type="submit" class="p-1 text-rose-500 hover:text-rose-700 transition" title="Hapus">
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
      <!-- Pagination Custom dengan Mempertahankan Query Params -->
      @if (is_object($jemaats) && method_exists($jemaats, 'hasPages') && $jemaats->hasPages())
        <div
          class="pt-3 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-100 text-xs text-slate-500">
          <div>
            Menampilkan <span class="font-bold text-slate-700">{{ $jemaats->firstItem() }}</span> -
            <span class="font-bold text-slate-700">{{ $jemaats->lastItem() }}</span> dari
            <span class="font-bold text-slate-700">{{ $jemaats->total() }}</span> total jemaat
          </div>

          <!-- Component Tombol Angka Halaman Custom -->
          <div class="flex items-center gap-1 shrink-0">
            {{-- Tombol Previous --}}
            @if ($jemaats->onFirstPage())
              <span
                class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 font-medium cursor-not-allowed">«
                Previous</span>
            @else
              <a href="{{ $jemaats->appends(request()->except('page'))->previousPageUrl() }}"
                class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 transition">«
                Previous</a>
            @endif

            {{-- Angka Nomor Halaman (1, 2, 3...) --}}
            @foreach ($jemaats->getUrlRange(1, $jemaats->lastPage()) as $page => $url)
              @if ($page == $jemaats->currentPage())
                <span class="px-3 py-1.5 rounded-lg font-bold text-white shadow-xs"
                  style="background-color: #1e3a8a !important;">{{ $page }}</span>
              @else
                <a href="{{ $jemaats->appends(request()->except('page'))->url($page) }}"
                  class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 transition">{{ $page }}</a>
              @endif
            @endforeach

            {{-- Tombol Next --}}
            @if ($jemaats->hasMorePages())
              <a href="{{ $jemaats->appends(request()->except('page'))->nextPageUrl() }}"
                class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 transition">Next
                »</a>
            @else
              <span
                class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 font-medium cursor-not-allowed">Next
                »</span>
            @endif
          </div>
        </div>
      @endif

    </div>

  </div>

  <!-- MODAL IMPORT EXCEL / CSV -->
  <div id="importJemaatModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-2xl w-full max-w-md shadow-2xl mx-4 border border-slate-200">
      <div class="flex justify-between items-center mb-1 pb-2 border-b border-slate-100">
        <h3 class="font-bold text-slate-900 text-lg">Impor Data Jemaat</h3>
        <button type="button" onclick="document.getElementById('importJemaatModal').style.display='none'"
          class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
      </div>
      <p class="text-xs text-slate-500 mb-4 mt-2">Unggah file format .xlsx / .xls / .csv sesuai template.</p>

      <form action="{{ route('admin.jemaat.import') }}" method="POST" enctype="multipart/form-data"
        class="space-y-4 text-xs">
        @csrf
        <div>
          <label class="block font-semibold text-slate-700 mb-1.5">Pilih File Excel / CSV *</label>
          <input type="file" name="file" accept=".xlsx, .xls, .csv" required
            class="w-full border border-slate-300 rounded-xl px-3 py-2 text-slate-700 outline-none focus:border-blue-600 bg-slate-50">
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-slate-600 space-y-1">
          <p class="font-bold text-blue-900">Petunjuk Format:</p>
          <p>Pastikan nama kolom sesuai template (Nama Asli, Alias 1, Alias 2, Wilayah Ibadah, Alamat, dll).</p>
          <a href="{{ route('admin.jemaat.template') }}"
            class="text-blue-700 font-bold underline inline-flex items-center gap-1 mt-1">
            <i class="ri-download-line"></i> Download Template Excel (.xlsx)
          </a>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
          <button type="button" onclick="document.getElementById('importJemaatModal').style.display='none'"
            class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition">Batal</button>
          <button type="submit" style="background-color: #059669 !important; color: #ffffff !important;"
            class="px-6 py-2.5 rounded-xl hover:bg-emerald-700 text-white font-semibold transition shadow-md">Upload &
            Import</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL TAMBAH ANGGOTA -->
  <div id="addJemaatModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div
      class="bg-white p-6 rounded-2xl w-full max-w-xl shadow-2xl mx-4 max-h-[90vh] overflow-y-auto border border-slate-200">
      <div class="flex justify-between items-center mb-1 pb-2 border-b border-slate-100">
        <h3 class="font-bold text-slate-900 text-lg">Tambah Anggota</h3>
        <button type="button" onclick="document.getElementById('addJemaatModal').style.display='none'"
          class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
      </div>
      <p class="text-xs text-slate-500 mb-5 mt-2">Nama asli wajib diisi. Kolom lain opsional.</p>

      <form action="{{ route('admin.jemaat.store') }}" method="POST" class="space-y-4 text-xs">
        @csrf
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Nama Asli *</label>
          <input type="text" name="nama_asli" required
            class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Alias 1</label>
            <input type="text" name="alias_1"
              class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
          </div>
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Alias 2</label>
            <input type="text" name="alias_2"
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
          <input type="text" name="alamat"
            class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Tempat Lahir</label>
            <input type="text" name="tempat_lahir"
              class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
          </div>
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir"
              class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
          </div>
        </div>

        <div>
          <label class="block font-semibold text-slate-700 mb-1">Telepon</label>
          <input type="text" name="telepon"
            class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
        </div>

        <div>
          <label class="block font-semibold text-slate-700 mb-1">
            Keluarga <span class="text-slate-400 font-normal">(mis. "Kel. Augusta")</span>
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
    <div
      class="bg-white p-6 rounded-2xl w-full max-w-xl shadow-2xl mx-4 max-h-[90vh] overflow-y-auto border border-slate-200">
      <div class="flex justify-between items-center mb-1 pb-2 border-b border-slate-100">
        <h3 class="font-bold text-slate-900 text-lg">Edit Data Jemaat</h3>
        <button type="button" onclick="document.getElementById('editJemaatModal').style.display='none'"
          class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
      </div>
      <p class="text-xs text-slate-500 mb-5 mt-2">Perbarui informasi data jemaat di bawah ini.</p>

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
            <input type="date" id="edit_tanggal_lahir" name="tanggal_lahir"
              class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
          </div>
        </div>

        <div>
          <label class="block font-semibold text-slate-700 mb-1">Telepon</label>
          <input type="text" id="edit_telepon" name="telepon"
            class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:border-blue-600">
        </div>

        <div>
          <label class="block font-semibold text-slate-700 mb-1">
            Keluarga <span class="text-slate-400 font-normal">(mis. "Kel. Augusta")</span>
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

  <!-- Datalist Referensi Nama Keluarga -->
  <datalist id="list-keluarga">
    @foreach ($keluargas ?? [] as $kel)
      <option value="{{ $kel }}">
    @endforeach
  </datalist>

  <script>
    function editJemaat(jemaat) {
      document.getElementById('editJemaatForm').action = "/admin/jemaat/" + jemaat.id;
      document.getElementById('edit_nama_asli').value = jemaat.nama_asli;
      document.getElementById('edit_status').value = jemaat.status || 'Anggota';
      document.getElementById('edit_alias_1').value = jemaat.alias_1 || '';
      document.getElementById('edit_alias_2').value = jemaat.alias_2 || '';
      document.getElementById('edit_keluarga').value = jemaat.keluarga || '';
      document.getElementById('edit_cabang_id').value = jemaat.cabang_id || '';
      document.getElementById('edit_alamat').value = jemaat.alamat || '';
      document.getElementById('edit_tempat_lahir').value = jemaat.tempat_lahir || '';

      const rawDate = jemaat.tanggal_lahir || jemaat.tgl_lahir;
      document.getElementById('edit_tanggal_lahir').value = rawDate ? rawDate.split('T')[0] : '';

      document.getElementById('edit_telepon').value = jemaat.telepon || '';
      document.getElementById('editJemaatModal').style.display = 'flex';
    }
  </script>
@endsection
