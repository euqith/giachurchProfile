@extends('admin.layoutCMS')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-20" x-data="worshipEditor()">

  <!-- TOP HEADER (IMAGE-8) -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
    <div class="flex items-center gap-3">
      <a href="{{ route('admin.attendance.persiapan') }}"
        class="w-9 h-9 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 flex items-center justify-center transition shadow-xs" title="Kembali ke Persiapan Ibadah">
        <i class="ri-arrow-left-line text-lg"></i>
      </a>

      <div class="w-10 h-10 rounded-xl bg-blue-900 text-white flex items-center justify-center font-bold text-lg shadow-sm">
        <i class="ri-church-line"></i>
      </div>

      <div>
        <h1 class="text-base font-bold text-slate-900 leading-tight">Gereja Isa Almasih Surabaya</h1>
        <p class="text-xs text-slate-500">Persiapan Ibadah & Liturgi</p>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.attendance.persiapan') }}"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition">
        <i class="ri-apps-2-line text-xs"></i> Kembali ke aplikasi
      </a>
    </div>
  </div>

  <!-- SUB-BAR: MODE BACA / EDIT + AKSI WA & SALIN TAUTAN (IMAGE-8) -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    
    <!-- Mode Toggle: Baca | Edit -->
    <div class="inline-flex p-1 bg-slate-100 rounded-xl text-xs font-bold text-slate-600">
      <button type="button" @click="viewMode = 'baca'"
        :class="viewMode === 'baca' ? 'bg-white text-blue-900 shadow-xs' : 'hover:text-slate-900'"
        class="px-4 py-1.5 rounded-lg transition inline-flex items-center gap-1.5">
        <i class="ri-eye-line text-xs"></i> Baca
      </button>
      <button type="button" @click="viewMode = 'edit'"
        :class="viewMode === 'edit' ? 'bg-white text-blue-900 shadow-xs' : 'hover:text-slate-900'"
        class="px-4 py-1.5 rounded-lg transition inline-flex items-center gap-1.5">
        <i class="ri-edit-line text-xs"></i> Edit
      </button>
    </div>

    <!-- Actions: Bagikan WA & Salin Tautan -->
    <div class="flex items-center gap-2">
      <button type="button" @click="openWAModal()"
        class="px-3.5 py-1.5 rounded-xl border border-emerald-300 bg-white hover:bg-emerald-50 text-emerald-700 text-xs font-semibold inline-flex items-center gap-1.5 shadow-xs transition">
        <i class="ri-whatsapp-line text-sm text-emerald-600"></i> Bagikan WA
      </button>
      <button type="button" @click="copyPublicLink()"
        class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center gap-1.5 shadow-xs transition">
        <i class="ri-file-copy-line text-xs"></i> Salin tautan
      </button>
    </div>

  </div>

  <!-- TOAST NOTIFICATION -->
  <div x-show="toastMessage" style="display: none;"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    class="p-3 bg-slate-900 text-white rounded-xl text-xs font-semibold flex items-center justify-between shadow-lg">
    <div class="flex items-center gap-2">
      <i class="ri-checkbox-circle-fill text-emerald-400 text-base"></i>
      <span x-text="toastMessage"></span>
    </div>
    <button type="button" @click="toastMessage = ''" class="text-slate-400 hover:text-white ml-3">✕</button>
  </div>

  <!-- ==================== VIEW MODE: EDIT ==================== -->
  <div x-show="viewMode === 'edit'" class="space-y-6">

    <!-- CARD 1: KEPALA & TEMA (IMAGE-8) -->
    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/90 shadow-xs space-y-4">
      <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center text-sm">
            <i class="ri-calendar-event-line"></i>
          </div>
          <div>
            <h2 class="text-sm font-bold text-slate-900">Kepala & Tema</h2>
            <p class="text-[11px] text-slate-500">Tanggal, jenis ibadah, lokasi, tema besar, dan ayat bacaan.</p>
          </div>
        </div>
      </div>

      <!-- Form Inputs: Tanggal, Jenis, Lokasi -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
          <label class="block text-[11px] font-bold text-slate-700 mb-1">Tanggal</label>
          <input type="date" x-model="formData.tanggal" @input="markDirty()"
            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500 bg-white">
        </div>

        <div>
          <label class="block text-[11px] font-bold text-slate-700 mb-1">Jenis Ibadah</label>
          <select x-model="formData.nama_ibadah" @change="markDirty()"
            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500 bg-white">
            @foreach($jenisIbadahs as $j)
              <option value="{{ $j->nama_ibadah }}">{{ $j->nama_ibadah }}</option>
            @endforeach
            <option value="Ibadah Umum">Ibadah Umum</option>
          </select>
        </div>

        <div>
          <label class="block text-[11px] font-bold text-slate-700 mb-1">Lokasi</label>
          <select x-model="formData.nama_cabang" @change="markDirty()"
            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500 bg-white">
            @foreach($cabangs as $c)
              <option value="{{ $c->nama_cabang }}">{{ $c->nama_cabang }}</option>
            @endforeach
            <option value="Darmo Pagi">Darmo Pagi</option>
            <option value="Bromo">Bromo</option>
            <option value="Citraland">Citraland</option>
            <option value="Gateway">Gateway</option>
          </select>
        </div>
      </div>

      <!-- Tema Besar & Ayat Bacaan -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block text-[11px] font-bold text-slate-700 mb-1">Tema Besar</label>
          <input type="text" x-model="formData.tema" @input="markDirty()"
            placeholder="mis. Hidup dalam Roh"
            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500 bg-white">
        </div>

        <div>
          <label class="block text-[11px] font-bold text-slate-700 mb-1">Ayat Bacaan</label>
          <input type="text" x-model="formData.ayat" @input="markDirty()"
            placeholder="mis. Roma 8:1-11"
            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500 bg-white">
        </div>
      </div>

      <!-- Catatan Singkat -->
      <div>
        <label class="block text-[11px] font-bold text-slate-700 mb-1">Catatan Singkat</label>
        <textarea x-model="formData.catatan_singkat" @input="markDirty()" rows="2"
          placeholder="Pengumuman untuk tim pelayanan..."
          class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500 bg-white"></textarea>
      </div>
    </div>

    <!-- CARD 2: PETUGAS PELAYANAN (IMAGE-8, IMAGE-9, IMAGE-11) -->
    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/90 shadow-xs space-y-5">
      <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center text-sm">
            <i class="ri-team-line"></i>
          </div>
          <div>
            <h2 class="text-sm font-bold text-slate-900">Petugas Pelayanan</h2>
            <p class="text-[11px] text-slate-500">Nama boleh diketik bebas; saran diambil dari database jemaat. Slot kosong akan diabaikan saat disimpan.</p>
          </div>
        </div>
      </div>

      <!-- Row: Pengkhotbah & Pemimpin Pujian -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block text-[11px] font-bold text-slate-700 mb-1">Pengkhotbah</label>
          <input type="text" list="jemaatList" x-model="formData.pengkhotbah" @input="markDirty()"
            placeholder="Nama"
            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500 bg-white">
        </div>

        <div>
          <label class="block text-[11px] font-bold text-slate-700 mb-1">Pemimpin Pujian</label>
          <input type="text" list="jemaatList" x-model="formData.pemimpin_pujian" @input="markDirty()"
            placeholder="Nama"
            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-blue-500 bg-white">
        </div>
      </div>

      <!-- Section: Pemusik (Image-8 & Image-11) -->
      <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-xs font-bold text-slate-900">Pemusik</h3>
            <p class="text-[10px] text-slate-500" x-text="`${formData.petugas_tambahan.pemusik.length} orang · pilih instrumen untuk setiap pemusik`"></p>
          </div>
          <button type="button" @click="addPemusik()"
            class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-white text-slate-700 text-xs font-semibold inline-flex items-center gap-1 shadow-xs transition">
            <i class="ri-add-line text-xs"></i> Tambah Pemusik
          </button>
        </div>

        <!-- List of Pemusik Rows (Image-11) -->
        <div class="space-y-2">
          <template x-for="(p, pIdx) in formData.petugas_tambahan.pemusik" :key="pIdx">
            <div class="flex items-center gap-2">
              <select x-model="p.instrumen" @change="markDirty()"
                class="w-36 sm:w-44 px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 bg-white focus:outline-none focus:border-blue-500">
                <option value="">Instrumen</option>
                <option value="Keyboard">Keyboard</option>
                <option value="Gitar Akustik">Gitar Akustik</option>
                <option value="Gitar Elektrik">Gitar Elektrik</option>
                <option value="Bass">Bass</option>
                <option value="Drum">Drum</option>
                <option value="Saxophone">Saxophone</option>
                <option value="Biola">Biola</option>
                <option value="Kajon">Kajon</option>
                <option value="Lainnya">Lainnya</option>
              </select>

              <input type="text" list="jemaatList" x-model="p.nama" @input="markDirty()"
                :placeholder="`Nama pemusik ${pIdx + 1}`"
                class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 bg-white focus:outline-none focus:border-blue-500">

              <button type="button" @click="removePemusik(pIdx)"
                class="w-8 h-8 rounded-lg text-rose-500 hover:bg-rose-50 hover:text-rose-700 flex items-center justify-center transition" title="Hapus">
                <i class="ri-delete-bin-line text-base"></i>
              </button>
            </div>
          </template>

          <p x-show="formData.petugas_tambahan.pemusik.length === 0" class="text-xs text-slate-400 py-1">
            Belum ada pemusik.
          </p>
        </div>
      </div>

      <!-- 2-COLUMN DUTY CARDS (IMAGE-9) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        <!-- Penerima Tamu -->
        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-xs font-bold text-slate-900">Penerima Tamu</h4>
              <p class="text-[10px] text-slate-500" x-text="`${formData.petugas_tambahan.penerima_tamu.filter(n => n).length}/8 orang · min. 2`"></p>
            </div>
            <button type="button" @click="addRoleItem('penerima_tamu')"
              class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-white text-slate-700 text-xs font-semibold inline-flex items-center gap-1 shadow-xs transition">
              <i class="ri-add-line text-xs"></i> Tambah
            </button>
          </div>
          <div class="space-y-2">
            <template x-for="(name, idx) in formData.petugas_tambahan.penerima_tamu" :key="idx">
              <div class="flex items-center gap-2">
                <input type="text" list="jemaatList" x-model="formData.petugas_tambahan.penerima_tamu[idx]" @input="markDirty()"
                  :placeholder="`Penerima Tamu ${idx + 1}`"
                  class="flex-1 px-3 py-1.5 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none focus:border-blue-500">
                <button type="button" @click="removeRoleItem('penerima_tamu', idx)" class="text-rose-500 hover:text-rose-700 p-1">
                  <i class="ri-delete-bin-line text-sm"></i>
                </button>
              </div>
            </template>
          </div>
        </div>

        <!-- Soundman -->
        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-xs font-bold text-slate-900">Soundman</h4>
              <p class="text-[10px] text-slate-500" x-text="`${formData.petugas_tambahan.soundman.filter(n => n).length}/2 orang`"></p>
            </div>
            <button type="button" @click="addRoleItem('soundman')"
              class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-white text-slate-700 text-xs font-semibold inline-flex items-center gap-1 shadow-xs transition">
              <i class="ri-add-line text-xs"></i> Tambah
            </button>
          </div>
          <div class="space-y-2">
            <template x-for="(name, idx) in formData.petugas_tambahan.soundman" :key="idx">
              <div class="flex items-center gap-2">
                <input type="text" list="jemaatList" x-model="formData.petugas_tambahan.soundman[idx]" @input="markDirty()"
                  :placeholder="`Soundman ${idx + 1}`"
                  class="flex-1 px-3 py-1.5 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none focus:border-blue-500">
                <button type="button" @click="removeRoleItem('soundman', idx)" class="text-rose-500 hover:text-rose-700 p-1">
                  <i class="ri-delete-bin-line text-sm"></i>
                </button>
              </div>
            </template>
          </div>
        </div>

        <!-- Petugas Kolekte -->
        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-xs font-bold text-slate-900">Petugas Kolekte</h4>
              <p class="text-[10px] text-slate-500" x-text="`${formData.petugas_tambahan.kolekte.filter(n => n).length}/20 orang · min. 2`"></p>
            </div>
            <button type="button" @click="addRoleItem('kolekte')"
              class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-white text-slate-700 text-xs font-semibold inline-flex items-center gap-1 shadow-xs transition">
              <i class="ri-add-line text-xs"></i> Tambah
            </button>
          </div>
          <div class="space-y-2">
            <template x-for="(name, idx) in formData.petugas_tambahan.kolekte" :key="idx">
              <div class="flex items-center gap-2">
                <input type="text" list="jemaatList" x-model="formData.petugas_tambahan.kolekte[idx]" @input="markDirty()"
                  :placeholder="`Petugas Kolekte ${idx + 1}`"
                  class="flex-1 px-3 py-1.5 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none focus:border-blue-500">
                <button type="button" @click="removeRoleItem('kolekte', idx)" class="text-rose-500 hover:text-rose-700 p-1">
                  <i class="ri-delete-bin-line text-sm"></i>
                </button>
              </div>
            </template>
          </div>
        </div>

        <!-- Petugas Perjamuan Kudus -->
        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-xs font-bold text-slate-900">Petugas Perjamuan Kudus</h4>
              <p class="text-[10px] text-slate-500" x-text="`${formData.petugas_tambahan.perjamuan.filter(n => n).length}/10 orang · min. 2`"></p>
            </div>
            <button type="button" @click="addRoleItem('perjamuan')"
              class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-white text-slate-700 text-xs font-semibold inline-flex items-center gap-1 shadow-xs transition">
              <i class="ri-add-line text-xs"></i> Tambah
            </button>
          </div>
          <div class="space-y-2">
            <template x-for="(name, idx) in formData.petugas_tambahan.perjamuan" :key="idx">
              <div class="flex items-center gap-2">
                <input type="text" list="jemaatList" x-model="formData.petugas_tambahan.perjamuan[idx]" @input="markDirty()"
                  :placeholder="`Petugas Perjamuan ${idx + 1}`"
                  class="flex-1 px-3 py-1.5 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none focus:border-blue-500">
                <button type="button" @click="removeRoleItem('perjamuan', idx)" class="text-rose-500 hover:text-rose-700 p-1">
                  <i class="ri-delete-bin-line text-sm"></i>
                </button>
              </div>
            </template>
          </div>
        </div>

        <!-- Majelis Bertugas -->
        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-xs font-bold text-slate-900">Majelis Bertugas</h4>
              <p class="text-[10px] text-slate-500" x-text="`${formData.petugas_tambahan.majelis.filter(n => n).length}/2 orang`"></p>
            </div>
            <button type="button" @click="addRoleItem('majelis')"
              class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-white text-slate-700 text-xs font-semibold inline-flex items-center gap-1 shadow-xs transition">
              <i class="ri-add-line text-xs"></i> Tambah
            </button>
          </div>
          <div class="space-y-2">
            <template x-for="(name, idx) in formData.petugas_tambahan.majelis" :key="idx">
              <div class="flex items-center gap-2">
                <input type="text" list="jemaatList" x-model="formData.petugas_tambahan.majelis[idx]" @input="markDirty()"
                  :placeholder="`Majelis Bertugas ${idx + 1}`"
                  class="flex-1 px-3 py-1.5 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none focus:border-blue-500">
                <button type="button" @click="removeRoleItem('majelis', idx)" class="text-rose-500 hover:text-rose-700 p-1">
                  <i class="ri-delete-bin-line text-sm"></i>
                </button>
              </div>
            </template>
          </div>
        </div>

        <!-- Petugas Multimedia -->
        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-xs font-bold text-slate-900">Petugas Multimedia</h4>
              <p class="text-[10px] text-slate-500" x-text="`${formData.petugas_tambahan.multimedia.filter(n => n).length}/4 orang`"></p>
            </div>
            <button type="button" @click="addRoleItem('multimedia')"
              class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-white text-slate-700 text-xs font-semibold inline-flex items-center gap-1 shadow-xs transition">
              <i class="ri-add-line text-xs"></i> Tambah
            </button>
          </div>
          <div class="space-y-2">
            <template x-for="(name, idx) in formData.petugas_tambahan.multimedia" :key="idx">
              <div class="flex items-center gap-2">
                <input type="text" list="jemaatList" x-model="formData.petugas_tambahan.multimedia[idx]" @input="markDirty()"
                  :placeholder="`Petugas Multimedia ${idx + 1}`"
                  class="flex-1 px-3 py-1.5 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none focus:border-blue-500">
                <button type="button" @click="removeRoleItem('multimedia', idx)" class="text-rose-500 hover:text-rose-700 p-1">
                  <i class="ri-delete-bin-line text-sm"></i>
                </button>
              </div>
            </template>
          </div>
        </div>

      </div>

      <!-- Peran Tambahan (Image-9) -->
      <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <span class="text-xs font-bold text-slate-800">Peran Tambahan</span>
        <button type="button" @click="addCustomRole()"
          class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center gap-1 transition">
          <i class="ri-add-line text-xs"></i> Tambah Peran
        </button>
      </div>

      <div class="space-y-2" x-show="formData.petugas_tambahan.peran_tambahan.length > 0">
        <template x-for="(cr, crIdx) in formData.petugas_tambahan.peran_tambahan" :key="crIdx">
          <div class="flex items-center gap-2">
            <input type="text" x-model="cr.role" placeholder="Nama Peran (cth: Singer)"
              class="w-36 sm:w-44 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium bg-white">
            <input type="text" list="jemaatList" x-model="cr.nama" placeholder="Nama Petugas"
              class="flex-1 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium bg-white">
            <button type="button" @click="formData.petugas_tambahan.peran_tambahan.splice(crIdx, 1); markDirty()"
              class="text-rose-500 hover:text-rose-700 p-1">
              <i class="ri-delete-bin-line text-sm"></i>
            </button>
          </div>
        </template>
      </div>

    </div>

    <!-- CARD 3: PERSIAPAN PUJIAN & MUSIK (IMAGE-9, IMAGE-10, IMAGE-14) -->
    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/90 shadow-xs space-y-5">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center text-sm">
            <i class="ri-music-2-line"></i>
          </div>
          <div>
            <h2 class="text-sm font-bold text-slate-900">Persiapan Pujian & Musik</h2>
            <p class="text-[11px] text-slate-500" x-text="`${formData.songs.length} lagu · ${liturgicalSectionCount()} bagian liturgi`"></p>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <!-- Buka Semua Video -->
          <button type="button" @click="openAllVideos()"
            :disabled="youtubeVideoCount() === 0"
            class="px-3 py-1.5 rounded-xl border border-rose-300 bg-white hover:bg-rose-50 disabled:opacity-40 text-rose-700 text-xs font-semibold inline-flex items-center gap-1.5 transition">
            <i class="ri-play-circle-line text-rose-600"></i> Buka semua video (<span x-text="youtubeVideoCount()"></span>)
          </button>

          <!-- Dari Pustaka (Image-13) -->
          <button type="button" @click="openPustakaModal()"
            class="px-3 py-1.5 rounded-xl border border-emerald-300 bg-white hover:bg-emerald-50 text-emerald-800 text-xs font-semibold inline-flex items-center gap-1.5 transition">
            <i class="ri-booklet-line text-emerald-600"></i> Dari Pustaka ({{ count($songs) }})
          </button>

          <!-- Tambah Lagu (Image-12) -->
          <button type="button" @click="openAddSongModal()"
            class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center gap-1 shadow-xs transition">
            <i class="ri-add-line text-xs"></i> Tambah Lagu
          </button>
        </div>
      </div>

      <!-- Empty State (Image-9 & Image-10) -->
      <div x-show="formData.songs.length === 0" class="py-12 border border-dashed border-slate-200 rounded-2xl text-center space-y-2">
        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-lg">
          <i class="ri-music-line"></i>
        </div>
        <p class="text-xs text-slate-400">Belum ada lagu. Klik "Tambah Lagu" untuk memulai.</p>
      </div>

      <!-- Populated State with Category Pills & Grouping (Image-14) -->
      <div x-show="formData.songs.length > 0" class="space-y-5">
        
        <!-- Liturgy Section Filter Pills -->
        <div class="flex flex-wrap items-center gap-2">
          <template x-for="sec in uniqueSections" :key="sec.name">
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/70 inline-flex items-center gap-1.5">
              <span x-text="sec.name"></span>
              <span class="text-[10px] bg-emerald-200 text-emerald-900 px-1.5 py-0.2 rounded-full" x-text="sec.count"></span>
            </span>
          </template>
        </div>

        <!-- Song Cards Grouped by Section -->
        <template x-for="sec in uniqueSections" :key="sec.name">
          <div class="space-y-2">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2">
              <span x-text="sec.name"></span>
              <span class="text-slate-400 font-normal lowercase" x-text="`${sec.count} lagu`"></span>
            </div>

            <div class="space-y-2">
              <template x-for="(song, sIdx) in getSongsBySection(sec.name)" :key="song.song_id || sIdx">
                <div class="p-3.5 sm:p-4 rounded-2xl border border-slate-200 bg-white hover:border-blue-300 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                  
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-800 font-extrabold text-xs flex items-center justify-center shrink-0">
                      <span x-text="sIdx + 1"></span>
                    </div>

                    <div>
                      <div class="font-bold text-xs text-slate-900" x-text="song.judul_lagu"></div>
                      <div class="text-[11px] text-slate-500 mt-0.5">
                        <span x-text="song.artis_sumber || 'Himne'"></span>
                        <span class="mx-1">·</span>
                        <span x-text="`Nada dasar ${song.nada_dasar || 'C'}`"></span>
                      </div>
                    </div>
                  </div>

                  <!-- Media Action Badges & Reorder Controls (Image-14) -->
                  <div class="flex items-center gap-2 self-end sm:self-center">
                    
                    <!-- YouTube Badge -->
                    <template x-if="song.youtube_url">
                      <a :href="song.youtube_url" target="_blank"
                        class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition" title="Buka YouTube">
                        <i class="ri-youtube-line text-sm"></i>
                      </a>
                    </template>
                    <template x-if="!song.youtube_url">
                      <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-300 flex items-center justify-center cursor-not-allowed">
                        <i class="ri-youtube-line text-sm"></i>
                      </div>
                    </template>

                    <!-- Spotify Badge -->
                    <template x-if="song.spotify_url">
                      <a :href="song.spotify_url" target="_blank"
                        class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition" title="Buka Spotify">
                        <i class="ri-spotify-line text-sm"></i>
                      </a>
                    </template>
                    <template x-if="!song.spotify_url">
                      <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-300 flex items-center justify-center cursor-not-allowed">
                        <i class="ri-music-2-line text-sm"></i>
                      </div>
                    </template>

                    <!-- Chord / PDF Badge -->
                    <template x-if="song.chord_url">
                      <a :href="song.chord_url" target="_blank"
                        class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 hover:bg-blue-600 hover:text-white flex items-center justify-center transition" title="Buka Chord">
                        <i class="ri-file-text-line text-sm"></i>
                      </a>
                    </template>
                    <template x-if="!song.chord_url">
                      <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-300 flex items-center justify-center cursor-not-allowed">
                        <i class="ri-file-text-line text-sm"></i>
                      </div>
                    </template>

                    <!-- Divider -->
                    <div class="h-5 w-px bg-slate-200 mx-1"></div>

                    <!-- Move Up / Down -->
                    <button type="button" @click="moveSongUp(song)"
                      class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-800 hover:bg-slate-100 flex items-center justify-center transition">
                      <i class="ri-arrow-up-line text-sm"></i>
                    </button>
                    <button type="button" @click="moveSongDown(song)"
                      class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-800 hover:bg-slate-100 flex items-center justify-center transition">
                      <i class="ri-arrow-down-line text-sm"></i>
                    </button>

                    <!-- Edit Song -->
                    <button type="button" @click="editLiturgySong(song)"
                      class="w-7 h-7 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 flex items-center justify-center transition" title="Edit Bagian / Nada">
                      <i class="ri-edit-line text-sm"></i>
                    </button>

                    <!-- Delete Song -->
                    <button type="button" @click="removeSong(song)"
                      class="w-7 h-7 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 flex items-center justify-center transition" title="Hapus dari Liturgi">
                      <i class="ri-delete-bin-line text-sm"></i>
                    </button>

                  </div>

                </div>
              </template>
            </div>
          </div>
        </template>

        <!-- Unsaved notice message (Image-14) -->
        <p x-show="isDirty" class="text-xs text-amber-600 font-semibold pt-1">
          Ada perubahan yang belum disimpan.
        </p>

      </div>
    </div>

    <!-- CARD 4: BAHAN KHOTBAH & MEDIA (IMAGE-23) -->
    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/90 shadow-xs space-y-4">
      <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center text-sm">
            <i class="ri-book-open-line"></i>
          </div>
          <div>
            <h2 class="text-sm font-bold text-slate-900">Bahan Khotbah & Media</h2>
            <p class="text-[11px] text-slate-500">Slide PPT, catatan khotbah, dan bahan lain.</p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <button type="button" @click="openAddMediaModal()"
            class="px-3.5 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center gap-1.5 shadow-xs transition">
            <i class="ri-link text-xs"></i> Tambah Tautan
          </button>
        </div>
      </div>

      <!-- Hidden File Input for Hosting Upload -->
      <input type="file" x-ref="mediaInput" @change="handleFileUpload($event)" multiple
        accept=".ppt,.pptx,.pdf,.doc,.docx,.xls,.xlsx,.zip,.png,.jpg,.jpeg,.mp4,.key" class="hidden">

      <!-- Drag & Drop Zone (IMAGE-23) -->
      <div class="p-8 border-2 border-dashed rounded-3xl text-center space-y-2 transition cursor-pointer select-none"
        :class="isDraggingMedia ? 'border-blue-500 bg-blue-50/80 scale-[1.01]' : 'border-blue-300/80 bg-blue-50/20 hover:bg-blue-50/50 hover:border-blue-400'"
        @click="$refs.mediaInput.click()"
        @dragover.prevent="isDraggingMedia = true"
        @dragleave.prevent="isDraggingMedia = false"
        @drop.prevent="handleFileDrop($event)">
        
        <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mx-auto text-2xl shadow-xs">
          <i class="ri-upload-cloud-line" x-show="!isUploadingMedia"></i>
          <i class="ri-loader-4-line animate-spin" x-show="isUploadingMedia"></i>
        </div>

        <div class="space-y-1">
          <p class="text-xs sm:text-sm font-bold text-slate-800"
            x-text="isUploadingMedia ? uploadProgressText : 'Seret-lepas file di sini atau klik untuk memilih'"></p>
          <p class="text-[11px] text-slate-500 max-w-lg mx-auto leading-relaxed">
            Nama & ukuran file dicatat sebagai penanda. Untuk unduhan, isi tautan Google Drive / Canva / OneDrive pada setiap bahan.
          </p>
        </div>
      </div>

      <!-- Slides List or Empty State (IMAGE-23) -->
      <div class="p-6 border border-slate-200/90 rounded-2xl bg-slate-50/40">
        <template x-if="formData.bahan_media.length === 0">
          <div class="text-center space-y-2 py-6">
            <div class="w-11 h-11 rounded-2xl bg-slate-200/70 text-slate-400 flex items-center justify-center mx-auto text-xl shadow-2xs">
              <i class="ri-slideshow-line"></i>
            </div>
            <p class="text-xs text-slate-400 font-medium">Belum ada slide yang diunggah.</p>
          </div>
        </template>

        <template x-if="formData.bahan_media.length > 0">
          <div class="space-y-2.5">
            <template x-for="(m, mIdx) in formData.bahan_media" :key="mIdx">
              <div class="p-3.5 bg-white rounded-2xl border border-slate-200/90 hover:border-blue-300 flex items-center justify-between gap-3 shadow-2xs transition">
                <div class="flex items-center gap-3 min-w-0">
                  <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0">
                    <i :class="getMediaIcon(m.tipe)"></i>
                  </div>
                  <div class="min-w-0 space-y-0.5">
                    <div class="flex items-center gap-2 flex-wrap">
                      <span class="text-xs font-bold text-slate-900 truncate" x-text="m.judul"></span>
                      <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700" x-text="m.tipe || 'File'"></span>
                      <span class="text-[10px] text-slate-400" x-show="m.size" x-text="m.size"></span>
                    </div>
                    <div>
                      <a :href="m.url" target="_blank" class="text-[11px] font-bold text-blue-600 hover:underline inline-flex items-center gap-1">
                        <i class="ri-external-link-line"></i> Buka / Unduh
                      </a>
                    </div>
                  </div>
                </div>
                <button type="button" @click="removeMedia(mIdx)"
                  class="text-slate-400 hover:text-rose-600 p-2 rounded-xl hover:bg-rose-50 transition" title="Hapus bahan">
                  <i class="ri-delete-bin-line text-sm"></i>
                </button>
              </div>
            </template>
          </div>
        </template>
      </div>

      <!-- Footer Timestamp (Image-23) -->
      <div class="text-right pt-2 text-[11px] text-slate-400">
        Terakhir diperbarui oleh <strong class="text-slate-600" x-text="updatedBy"></strong> pada <span x-text="updatedAt"></span>
      </div>
    </div>

    <!-- BOTTOM ACTION: SINGLE SAVE BUTTON BAR -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3 sticky bottom-4 z-30">
      <div class="flex items-center gap-2">
        <span x-show="isDirty" class="inline-flex items-center gap-1.5 text-xs text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-xl font-medium">
          <i class="ri-alert-line"></i> Ada perubahan yang belum disimpan
        </span>
        <span x-show="!isDirty" class="text-xs text-slate-400">
          Seluruh data jadwal ibadah, petugas, dan lagu akan disimpan.
        </span>
      </div>

      <div class="flex items-center gap-2.5 w-full sm:w-auto">
        <a href="{{ route('admin.attendance.persiapan') }}"
          class="flex-1 sm:flex-initial px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold text-center transition">
          Batal
        </a>
        <button type="button" @click="saveAll()"
          :disabled="isSaving"
          class="flex-1 sm:flex-initial px-6 py-2.5 rounded-xl bg-blue-900 hover:bg-blue-800 disabled:opacity-50 text-white text-xs font-bold shadow-md shadow-blue-900/20 inline-flex items-center justify-center gap-2 transition">
          <i class="ri-save-line text-sm" x-show="!isSaving"></i>
          <i class="ri-loader-4-line animate-spin text-sm" x-show="isSaving"></i>
          <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Jadwal Ibadah'"></span>
        </button>
      </div>
    </div>

  </div>

  <!-- ==================== VIEW MODE: BACA (RUNDOWN VIEW) ==================== -->
  <div x-show="viewMode === 'baca'" class="space-y-6">
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
      
      <!-- Banner Kepala & Tema -->
      <div class="border-b border-slate-100 pb-5 space-y-2">
        <div class="flex flex-wrap items-center gap-2">
          <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-extrabold" x-text="formData.nama_ibadah"></span>
          <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold" x-text="formData.nama_cabang"></span>
          <span class="text-xs text-slate-400 font-medium" x-text="formatDateHuman(formData.tanggal)"></span>
        </div>

        <h2 class="text-2xl font-black text-slate-900 tracking-tight" x-text="formData.tema ? `“${formData.tema}”` : 'Tanpa Tema'"></h2>

        <div class="text-xs font-semibold text-blue-700" x-show="formData.ayat">
          <i class="ri-book-open-line mr-1"></i> Bacaan: <span x-text="formData.ayat"></span>
        </div>

        <p class="text-xs text-slate-600 italic bg-slate-50 p-3 rounded-xl border border-slate-100" x-show="formData.catatan_singkat" x-text="formData.catatan_singkat"></p>
      </div>

      <!-- Petugas Pelayanan Rundown -->
      <div class="space-y-3">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Petugas Pelayanan</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 text-xs">
          <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
            <span class="text-[10px] text-slate-400 uppercase font-bold block">Pengkhotbah</span>
            <span class="font-bold text-slate-800 text-sm" x-text="formData.pengkhotbah || '-'"></span>
          </div>
          <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
            <span class="text-[10px] text-slate-400 uppercase font-bold block">Pemimpin Pujian (WL)</span>
            <span class="font-bold text-slate-800 text-sm" x-text="formData.pemimpin_pujian || '-'"></span>
          </div>
          <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
            <span class="text-[10px] text-slate-400 uppercase font-bold block">Pemusik</span>
            <span class="font-bold text-slate-800 text-xs block" x-text="getPemusikSummary()"></span>
          </div>
        </div>
      </div>

      <!-- Susunan Liturgi Lagu -->
      <div class="space-y-3 pt-3 border-t border-slate-100">
        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Susunan Lagu Pujian</h3>
        
        <div class="divide-y divide-slate-100 border border-slate-200 rounded-2xl overflow-hidden">
          <template x-for="(song, idx) in formData.songs" :key="idx">
            <div class="p-3.5 hover:bg-slate-50/70 transition flex items-center justify-between gap-3 text-xs">
              <div class="flex items-center gap-3">
                <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 font-bold flex items-center justify-center text-xs" x-text="idx + 1"></span>
                <div>
                  <div class="font-bold text-slate-900" x-text="song.judul_lagu"></div>
                  <div class="text-[10px] text-slate-400">
                    <span class="text-blue-600 font-semibold" x-text="song.bagian_liturgi"></span>
                    <span class="mx-1">·</span>
                    <span x-text="`Nada: ${song.nada_dasar || '-'}`"></span>
                  </div>
                </div>
              </div>

              <div class="flex items-center gap-2">
                <template x-if="song.youtube_url">
                  <a :href="song.youtube_url" target="_blank" class="px-2 py-1 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded text-[11px] font-semibold flex items-center gap-1">
                    <i class="ri-youtube-line"></i> Video
                  </a>
                </template>
                <template x-if="song.spotify_url">
                  <a :href="song.spotify_url" target="_blank" class="px-2 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded text-[11px] font-semibold flex items-center gap-1">
                    <i class="ri-spotify-line"></i> Spotify
                  </a>
                </template>
                <template x-if="song.chord_url">
                  <a :href="song.chord_url" target="_blank" class="px-2 py-1 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded text-[11px] font-semibold flex items-center gap-1">
                    <i class="ri-file-text-line"></i> Chord
                  </a>
                </template>
              </div>
            </div>
          </template>
        </div>
      </div>

    </div>
  </div>

  <!-- ==================== MODAL TAMBAH LAGU (IMAGE-12) ==================== -->
  <div x-show="showAddSongModal" style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl border border-slate-200 overflow-hidden flex flex-col"
      @click.outside="showAddSongModal = false">
      
      <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div>
          <h3 class="text-sm font-bold text-slate-900">Tambah Lagu</h3>
          <p class="text-[11px] text-slate-500 mt-0.5">Tempel tautan YouTube / Spotify / chord; ikon akan aktif otomatis.</p>
        </div>
        <button type="button" @click="showAddSongModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
      </div>

      <div class="p-5 space-y-3 max-h-[75vh] overflow-y-auto text-xs">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Judul Lagu *</label>
          <input type="text" x-model="modalSong.judul_lagu" required
            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:outline-none focus:border-blue-500">
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Bagian Liturgi</label>
          <select x-model="modalSong.bagian_liturgi"
            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium bg-white focus:outline-none focus:border-blue-500">
            <option value="Pujian & Penyembahan">Pujian & Penyembahan</option>
            <option value="Pujian Pembuka">Pujian Pembuka</option>
            <option value="Firman Tuhan">Firman Tuhan</option>
            <option value="Persembahan">Persembahan</option>
            <option value="Penutup / Berkat">Penutup / Berkat</option>
          </select>
        </div>

        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Nada Dasar</label>
            <input type="text" x-model="modalSong.nada_dasar" placeholder="mis. G"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:outline-none focus:border-blue-500">
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Artis / Sumber</label>
            <input type="text" x-model="modalSong.artis_sumber"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:outline-none focus:border-blue-500">
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1 flex items-center gap-1">
            <i class="ri-youtube-line text-rose-600"></i> Tautan YouTube
          </label>
          <input type="url" x-model="modalSong.youtube_url" placeholder="https://..."
            class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium focus:outline-none focus:border-blue-500">
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1 flex items-center gap-1">
            <i class="ri-spotify-line text-emerald-600"></i> Tautan Spotify
          </label>
          <input type="url" x-model="modalSong.spotify_url" placeholder="https://..."
            class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium focus:outline-none focus:border-blue-500">
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1 flex items-center gap-1">
            <i class="ri-file-text-line text-slate-600"></i> Tautan Chord / PDF
          </label>
          <input type="url" x-model="modalSong.chord_url" placeholder="https://..."
            class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium focus:outline-none focus:border-blue-500">
        </div>
      </div>

      <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-2">
        <button type="button" @click="showAddSongModal = false"
          class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold">
          Batal
        </button>
        <button type="button" @click="saveNewSongToLiturgy()"
          class="px-5 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold shadow-xs">
          Tambah
        </button>
      </div>

    </div>
  </div>

  <!-- ==================== MODAL DARI PUSTAKA (IMAGE-13) ==================== -->
  <div x-show="showPustakaModal" style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[85vh]"
      @click.outside="showPustakaModal = false">
      
      <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <i class="ri-music-2-line text-emerald-600 text-lg"></i>
          <div>
            <h3 class="text-sm font-bold text-slate-900">Pustaka Lagu</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Lagu yang pernah dipakai, dengan tautan yang sudah terisi. Klik "Tambah" untuk memasukkan ke bagian liturgi terpilih.</p>
          </div>
        </div>
        <button type="button" @click="showPustakaModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
      </div>

      <!-- Controls: Search & Masuk Ke Bagian (Image-13) -->
      <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center gap-2.5">
        <div class="relative flex-1 w-full">
          <i class="ri-search-2-line text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 text-xs"></i>
          <input type="text" x-model="pustakaSearchQuery"
            placeholder="Cari judul atau artis..."
            class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none focus:border-blue-500">
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
          <span class="text-[11px] text-slate-500 font-medium">Masuk ke bagian</span>
          <select x-model="targetPustakaSection"
            class="px-2.5 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white">
            <option value="Pujian & Penyembahan">Pujian & Penyembahan</option>
            <option value="Pujian Pembuka">Pujian Pembuka</option>
            <option value="Firman Tuhan">Firman Tuhan</option>
            <option value="Persembahan">Persembahan</option>
            <option value="Penutup / Berkat">Penutup / Berkat</option>
          </select>
        </div>
      </div>

      <!-- List of Library Songs (Image-13) -->
      <div class="p-4 space-y-2 overflow-y-auto max-h-96">
        <template x-for="s in filteredPustakaSongs" :key="s.id">
          <div class="p-3 rounded-2xl border border-slate-200 hover:border-blue-300 transition flex items-center justify-between gap-3 shadow-2xs">
            <div>
              <div class="font-bold text-xs text-slate-900" x-text="s.judul_lagu"></div>
              <div class="text-[11px] text-slate-500 mt-0.5">
                <span x-text="s.artis_sumber || 'Himne'"></span>
                <span class="mx-1">·</span>
                <span x-text="`Nada ${s.nada_dasar || 'C'}`"></span>
              </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
              <!-- Indicators -->
              <div class="flex items-center gap-1">
                <i class="ri-youtube-line text-xs" :class="s.youtube_url ? 'text-rose-600' : 'text-slate-300'"></i>
                <i class="ri-music-2-line text-xs" :class="s.spotify_url ? 'text-emerald-600' : 'text-slate-300'"></i>
                <i class="ri-file-text-line text-xs" :class="s.chord_url ? 'text-slate-600' : 'text-slate-300'"></i>
              </div>

              <!-- Usage Badge -->
              <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600"
                x-text="`${s.dipakai_count || 0}x dipakai`"></span>

              <!-- Tambah Button -->
              <button type="button" @click="addFromPustaka(s)"
                class="px-3 py-1 rounded-xl bg-white border border-slate-200 hover:bg-blue-50 hover:text-blue-700 text-slate-700 text-xs font-bold transition shadow-xs">
                + Tambah
              </button>
            </div>
          </div>
        </template>
      </div>

      <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end">
        <button type="button" @click="showPustakaModal = false"
          class="px-5 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold shadow-xs">
          Selesai
        </button>
      </div>

    </div>
  </div>

  <!-- ==================== MODAL BAGIKAN WA (IMAGE-22) ==================== -->
  <div x-show="isWAModalOpen" style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]"
      @click.outside="isWAModalOpen = false">
      
      <!-- Header -->
      <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
            <i class="ri-whatsapp-line"></i>
          </div>
          <div>
            <h3 class="text-sm font-bold text-slate-900">Bagikan Persiapan Ibadah ke WhatsApp</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Teks sudah disesuaikan dengan susunan liturgi dan petugas.</p>
          </div>
        </div>
        <button type="button" @click="isWAModalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
      </div>

      <!-- Body -->
      <div class="p-5 space-y-4 overflow-y-auto max-h-[70vh] text-xs">
        
        <!-- Kirim Langsung Ke & Button -->
        <div class="space-y-1.5">
          <label class="block font-bold text-slate-700 flex items-center gap-1.5">
            <i class="ri-user-shared-line text-emerald-600"></i> Kirim langsung ke
          </label>
          <div class="flex items-center gap-2">
            <div class="relative flex-1">
              <select x-model="waTargetPhone"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:border-emerald-500">
                <option value="6281703872525">Ariel · +62 81703872525</option>
                <option value="6281234567890">Admin GIA · +62 81234567890</option>
                @foreach($jemaats as $jem)
                  @if($jem->telepon && strlen($jem->telepon) > 6)
                    @php
                      $cPhone = preg_replace('/[^0-9]/', '', $jem->telepon);
                      if (str_starts_with($cPhone, '0')) {
                        $cPhone = '62' . substr($cPhone, 1);
                      }
                    @endphp
                    <option value="{{ $cPhone }}">{{ $jem->nama_asli }} ({{ $jem->status }}) · +{{ $cPhone }}</option>
                  @endif
                @endforeach
              </select>
            </div>
            <button type="button" @click="sendDirectWA()"
              class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-xs transition shrink-0">
              <i class="ri-whatsapp-line text-sm"></i> Kirim WA
            </button>
          </div>
        </div>

        <!-- Toggles / Checkboxes -->
        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 space-y-2.5">
          <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" x-model="waIncludeSongLinks" @change="generateWAText()"
              class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
            <span class="text-xs font-semibold text-slate-700">Sertakan tautan lagu (YouTube, Spotify, Chord)</span>
          </label>

          <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" x-model="waIncludeMediaLinks" @change="generateWAText()"
              class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
            <span class="text-xs font-semibold text-slate-700">Sertakan bahan khotbah / slide</span>
          </label>
        </div>

        <!-- Live Preview Textarea -->
        <div class="space-y-1">
          <div class="flex items-center justify-between">
            <label class="font-bold text-slate-700">Pratinjau Format Pesan:</label>
            <span class="text-[10px] text-slate-400">Dapat diedit langsung sebelum dikirim</span>
          </div>
          <textarea x-model="waGeneratedText" rows="12"
            class="w-full p-3 rounded-2xl border border-slate-200 bg-slate-50 font-mono text-[11px] text-slate-800 leading-relaxed focus:bg-white focus:outline-none focus:border-emerald-500"></textarea>
        </div>

      </div>

      <!-- Footer Actions -->
      <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between gap-2">
        <button type="button" @click="isWAModalOpen = false"
          class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold">
          Tutup
        </button>
        <div class="flex items-center gap-2">
          <button type="button" @click="sendWAWeb()"
            class="px-4 py-2 rounded-xl border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold inline-flex items-center gap-1.5 transition">
            <i class="ri-whatsapp-line"></i> Pilih Kontak di WA
          </button>
          <button type="button" @click="copyWAText()"
            class="px-5 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold inline-flex items-center gap-1.5 shadow-xs transition">
            <i class="ri-file-copy-line"></i> Salin
          </button>
        </div>
      </div>

    </div>
  </div>

  <!-- ==================== MODAL TAMBAH TAUTAN MEDIA ==================== -->
  <div x-show="showAddMediaModal" style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl border border-slate-200 overflow-hidden flex flex-col"
      @click.outside="showAddMediaModal = false">
      
      <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base">
            <i class="ri-link"></i>
          </div>
          <div>
            <h3 class="text-sm font-bold text-slate-900">Tambah Tautan Bahan Khotbah</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Tautkan file Google Drive, presentasi Canva, atau dokumen cloud.</p>
          </div>
        </div>
        <button type="button" @click="showAddMediaModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
      </div>

      <div class="p-5 space-y-3.5 text-xs">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Judul Bahan *</label>
          <input type="text" x-model="modalMedia.judul" placeholder="cth: Slide Presentasi Khotbah Minggu"
            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:outline-none focus:border-blue-500">
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Tipe Bahan</label>
          <select x-model="modalMedia.tipe"
            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:border-blue-500">
            <option value="Slide PPT">Slide PPT / Keynote</option>
            <option value="Canva">Desain Canva</option>
            <option value="Dokumen PDF">Dokumen PDF</option>
            <option value="Google Drive">Google Drive Folder / File</option>
            <option value="Video">Video Media</option>
            <option value="Lainnya">Lainnya</option>
          </select>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">URL / Tautan *</label>
          <input type="url" x-model="modalMedia.url" placeholder="https://..."
            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:outline-none focus:border-blue-500">
        </div>
      </div>

      <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-2">
        <button type="button" @click="showAddMediaModal = false"
          class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold">
          Batal
        </button>
        <button type="button" @click="saveMediaLink()"
          class="px-5 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold shadow-xs">
          Simpan Tautan
        </button>
      </div>

    </div>
  </div>

  <!-- DATALIST SARAN JEMAAT -->
  <datalist id="jemaatList">
    @foreach($jemaats as $jem)
      <option value="{{ $jem->nama_asli }}">{{ $jem->status }}</option>
    @endforeach
  </datalist>

</div>

<!-- Alpine Script for Worship Editor -->
<script>
  function worshipEditor() {
    return {
      viewMode: 'edit',
      isDirty: false,
      isSaving: false,
      toastMessage: '',
      showAddSongModal: false,
      showPustakaModal: false,
      pustakaSearchQuery: '',
      targetPustakaSection: 'Pujian & Penyembahan',

      // Bahan Media Upload & Modal State (Image-23)
      isDraggingMedia: false,
      isUploadingMedia: false,
      uploadProgressText: '',
      showAddMediaModal: false,
      modalMedia: { judul: '', tipe: 'Slide PPT', url: '' },

      // Bagikan WA Modal State (Image-22)
      isWAModalOpen: false,
      waTargetPhone: '6281703872525',
      waIncludeSongLinks: true,
      waIncludeMediaLinks: true,
      waGeneratedText: '',

      allLibrarySongs: @json($songs),
      updatedBy: '{{ $schedule->updatedBy ?: ($schedule->createdBy ?: (Auth::user()->name ?? 'Administrator')) }}',
      updatedAt: '{{ $schedule->updated_at ? $schedule->updated_at->format('d M Y H:i') : date('d M Y H:i') }}',

      formData: {
        id: {{ $schedule->id ?: 'null' }},
        tanggal: '{{ $schedule->tanggal ? $schedule->tanggal->format('Y-m-d') : date('Y-m-d') }}',
        nama_ibadah: '{{ $schedule->nama_ibadah ?: ($jenisIbadahs->first()->nama_ibadah ?? 'Ibadah Umum') }}',
        nama_sesi: '{{ $schedule->nama_sesi ?: 'Pagi' }}',
        nama_cabang: '{{ $schedule->nama_cabang ?: ($cabangs->first()->nama_cabang ?? 'Darmo') }}',
        tema: '{{ addslashes($schedule->tema ?? '') }}',
        ayat: '{{ addslashes($schedule->ayat ?? '') }}',
        catatan_singkat: '{{ addslashes($schedule->catatan_singkat ?? '') }}',
        pengkhotbah: '{{ addslashes($schedule->pengkhotbah ?? '') }}',
        pemimpin_pujian: '{{ addslashes($schedule->pemimpin_pujian ?? '') }}',
        
        petugas_tambahan: {
          pemusik: @json($schedule->petugas_tambahan['pemusik'] ?? []),
          penerima_tamu: @json($schedule->petugas_tambahan['penerima_tamu'] ?? ['', '']),
          soundman: @json($schedule->petugas_tambahan['soundman'] ?? ['']),
          kolekte: @json($schedule->petugas_tambahan['kolekte'] ?? ['', '']),
          perjamuan: @json($schedule->petugas_tambahan['perjamuan'] ?? ['', '']),
          majelis: @json($schedule->petugas_tambahan['majelis'] ?? ['']),
          multimedia: @json($schedule->petugas_tambahan['multimedia'] ?? ['']),
          peran_tambahan: @json($schedule->petugas_tambahan['peran_tambahan'] ?? [])
        },

        songs: [
          @foreach($schedule->scheduleSongs as $idx => $sSong)
            @if($sSong->song)
              {
                song_id: {{ $sSong->song_id }},
                judul_lagu: "{{ addslashes($sSong->song->judul_lagu) }}",
                artis_sumber: "{{ addslashes($sSong->song->artis_sumber ?? '') }}",
                bagian_liturgi: "{{ addslashes($sSong->bagian_liturgi ?? 'Pujian & Penyembahan') }}",
                nada_dasar: "{{ addslashes($sSong->nada_dasar ?: ($sSong->song->nada_dasar ?? 'C')) }}",
                youtube_url: "{{ addslashes($sSong->song->youtube_url ?? '') }}",
                spotify_url: "{{ addslashes($sSong->song->spotify_url ?? '') }}",
                chord_url: "{{ addslashes($sSong->song->chord_url ?? '') }}"
              },
            @endif
          @endforeach
        ],

        bahan_media: @json($schedule->bahan_media ?? [])
      },

      modalSong: {
        judul_lagu: '',
        bagian_liturgi: 'Pujian & Penyembahan',
        nada_dasar: 'G',
        artis_sumber: '',
        youtube_url: '',
        spotify_url: '',
        chord_url: ''
      },

      markDirty() {
        this.isDirty = true;
      },

      showToast(msg) {
        this.toastMessage = msg;
        setTimeout(() => {
          this.toastMessage = '';
        }, 4000);
      },

      addPemusik() {
        this.formData.petugas_tambahan.pemusik.push({ instrumen: 'Keyboard', nama: '' });
        this.markDirty();
      },

      removePemusik(idx) {
        this.formData.petugas_tambahan.pemusik.splice(idx, 1);
        this.markDirty();
      },

      addRoleItem(roleKey) {
        this.formData.petugas_tambahan[roleKey].push('');
        this.markDirty();
      },

      removeRoleItem(roleKey, idx) {
        this.formData.petugas_tambahan[roleKey].splice(idx, 1);
        this.markDirty();
      },

      addCustomRole() {
        const role = prompt('Nama peran baru (cth: Singer, Tambourine, Kameramen):');
        if (role && role.trim()) {
          this.formData.petugas_tambahan.peran_tambahan.push({
            role: role.trim(),
            nama: ''
          });
          this.markDirty();
        }
      },

      get uniqueSections() {
        const map = {};
        this.formData.songs.forEach(s => {
          const sec = s.bagian_liturgi || 'Pujian & Penyembahan';
          map[sec] = (map[sec] || 0) + 1;
        });
        return Object.keys(map).map(k => ({ name: k, count: map[k] }));
      },

      getSongsBySection(secName) {
        return this.formData.songs.filter(s => (s.bagian_liturgi || 'Pujian & Penyembahan') === secName);
      },

      liturgicalSectionCount() {
        return this.uniqueSections.length;
      },

      youtubeVideoCount() {
        return this.formData.songs.filter(s => s.youtube_url).length;
      },

      openAllVideos() {
        const urls = this.formData.songs.filter(s => s.youtube_url).map(s => s.youtube_url);
        if (urls.length === 0) {
          alert('Tidak ada tautan YouTube yang tersedia.');
          return;
        }
        urls.forEach(u => window.open(u, '_blank'));
      },

      moveSongUp(song) {
        const idx = this.formData.songs.indexOf(song);
        if (idx > 0) {
          const temp = this.formData.songs[idx];
          this.formData.songs[idx] = this.formData.songs[idx - 1];
          this.formData.songs[idx - 1] = temp;
          this.markDirty();
        }
      },

      moveSongDown(song) {
        const idx = this.formData.songs.indexOf(song);
        if (idx < this.formData.songs.length - 1) {
          const temp = this.formData.songs[idx];
          this.formData.songs[idx] = this.formData.songs[idx + 1];
          this.formData.songs[idx + 1] = temp;
          this.markDirty();
        }
      },

      removeSong(song) {
        const idx = this.formData.songs.indexOf(song);
        if (idx > -1) {
          this.formData.songs.splice(idx, 1);
          this.markDirty();
        }
      },

      editLiturgySong(song) {
        const newKey = prompt('Ubah nada dasar untuk ' + song.judul_lagu + ':', song.nada_dasar);
        if (newKey !== null) {
          song.nada_dasar = newKey.trim();
          this.markDirty();
        }
      },

      openAddSongModal() {
        this.modalSong = {
          judul_lagu: '',
          bagian_liturgi: this.targetPustakaSection || 'Pujian & Penyembahan',
          nada_dasar: 'G',
          artis_sumber: '',
          youtube_url: '',
          spotify_url: '',
          chord_url: ''
        };
        this.showAddSongModal = true;
      },

      saveNewSongToLiturgy() {
        if (!this.modalSong.judul_lagu || !this.modalSong.judul_lagu.trim()) {
          alert('Judul lagu wajib diisi!');
          return;
        }

        // Save to song database via API
        fetch('{{ route('admin.attendance.storeSong') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          },
          body: JSON.stringify(this.modalSong)
        })
        .then(res => res.json())
        .then(data => {
          const createdSong = data.song || {};
          this.formData.songs.push({
            song_id: createdSong.id,
            judul_lagu: createdSong.judul_lagu || this.modalSong.judul_lagu,
            artis_sumber: createdSong.artis_sumber || this.modalSong.artis_sumber,
            bagian_liturgi: this.modalSong.bagian_liturgi,
            nada_dasar: this.modalSong.nada_dasar,
            youtube_url: createdSong.youtube_url || this.modalSong.youtube_url,
            spotify_url: createdSong.spotify_url || this.modalSong.spotify_url,
            chord_url: createdSong.chord_url || this.modalSong.chord_url
          });
          this.allLibrarySongs.unshift(createdSong);
          this.showAddSongModal = false;
          this.markDirty();
          this.showToast('Lagu berhasil ditambahkan ke liturgi!');
        })
        .catch(err => {
          alert('Gagal menyimpan lagu: ' + err.message);
        });
      },

      openPustakaModal() {
        this.pustakaSearchQuery = '';
        this.showPustakaModal = true;
      },

      get filteredPustakaSongs() {
        const q = this.pustakaSearchQuery.toLowerCase().trim();
        if (!q) return this.allLibrarySongs;
        return this.allLibrarySongs.filter(s => 
          s.judul_lagu.toLowerCase().includes(q) ||
          (s.artis_sumber && s.artis_sumber.toLowerCase().includes(q))
        );
      },

      addFromPustaka(song) {
        this.formData.songs.push({
          song_id: song.id,
          judul_lagu: song.judul_lagu,
          artis_sumber: song.artis_sumber || '',
          bagian_liturgi: this.targetPustakaSection,
          nada_dasar: song.nada_dasar || 'C',
          youtube_url: song.youtube_url || '',
          spotify_url: song.spotify_url || '',
          chord_url: song.chord_url || ''
        });
        this.markDirty();
        this.showToast(`Lagu "${song.judul_lagu}" dimasukkan ke bagian ${this.targetPustakaSection}!`);
      },

      openAddMediaModal() {
        this.modalMedia = {
          judul: '',
          tipe: 'Slide PPT',
          url: ''
        };
        this.showAddMediaModal = true;
      },

      saveMediaLink() {
        if (!this.modalMedia.judul || !this.modalMedia.judul.trim()) {
          alert('Judul bahan wajib diisi!');
          return;
        }
        if (!this.modalMedia.url || !this.modalMedia.url.trim()) {
          alert('Tautan bahan wajib diisi!');
          return;
        }

        this.formData.bahan_media.push({
          judul: this.modalMedia.judul.trim(),
          tipe: this.modalMedia.tipe,
          url: this.modalMedia.url.trim(),
          file_path: null,
          size: null,
          uploaded_at: new Date().toISOString()
        });

        this.showAddMediaModal = false;
        this.markDirty();
        this.showToast('Tautan bahan khotbah berhasil ditambahkan!');
      },

      handleFileUpload(event) {
        const files = event.target.files;
        if (files && files.length > 0) {
          this.uploadFiles(files);
        }
        event.target.value = '';
      },

      handleFileDrop(event) {
        this.isDraggingMedia = false;
        const files = event.dataTransfer.files;
        if (files && files.length > 0) {
          this.uploadFiles(files);
        }
      },

      uploadFiles(files) {
        this.isUploadingMedia = true;
        this.uploadProgressText = `Mengunggah ${files.length} file...`;

        const uploadPromises = Array.from(files).map(file => {
          const form = new FormData();
          form.append('media_file', file);
          form.append('judul', file.name.replace(/\.[^/.]+$/, ''));

          return fetch('{{ route('admin.attendance.uploadMedia') }}', {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            },
            body: form
          })
          .then(res => res.json())
          .then(data => {
            if (data.success && data.file) {
              this.formData.bahan_media.push(data.file);
            } else {
              throw new Error(data.message || 'Gagal mengunggah file ' + file.name);
            }
          });
        });

        Promise.all(uploadPromises)
          .then(() => {
            this.isUploadingMedia = false;
            this.uploadProgressText = '';
            this.markDirty();
            this.showToast('File bahan khotbah berhasil disimpan di hosting!');
          })
          .catch(err => {
            this.isUploadingMedia = false;
            this.uploadProgressText = '';
            alert('Kesalahan unggah: ' + err.message);
          });
      },

      getMediaIcon(tipe) {
        if (!tipe) return 'ri-file-line text-slate-600';
        const t = tipe.toLowerCase();
        if (t.includes('ppt') || t.includes('slide') || t.includes('powerpoint')) return 'ri-slideshow-line text-amber-600';
        if (t.includes('pdf')) return 'ri-file-pdf-line text-rose-600';
        if (t.includes('doc') || t.includes('word')) return 'ri-file-word-line text-blue-600';
        if (t.includes('xls') || t.includes('excel') || t.includes('sheet')) return 'ri-file-excel-line text-emerald-600';
        if (t.includes('canva') || t.includes('drive')) return 'ri-external-link-line text-teal-600';
        if (t.includes('video') || t.includes('mp4')) return 'ri-video-line text-purple-600';
        return 'ri-file-line text-slate-600';
      },

      removeMedia(idx) {
        if (confirm('Hapus bahan ini dari daftar?')) {
          this.formData.bahan_media.splice(idx, 1);
          this.markDirty();
        }
      },

      openWAModal() {
        this.generateWAText();
        this.isWAModalOpen = true;
      },

      generateWAText() {
        let text = `*PERSIAPAN IBADAH - GIA SURABAYA*\n`;
        text += `*${this.formData.nama_ibadah} (${this.formData.nama_cabang})*\n`;
        text += `Tanggal: ${this.formatDateHuman(this.formData.tanggal)} · Sesi: ${this.formData.nama_sesi || 'Pagi'}\n`;
        if (this.formData.tema) text += `*Tema:* "${this.formData.tema}"\n`;
        if (this.formData.ayat) text += `*Ayat:* ${this.formData.ayat}\n`;
        
        text += `\n*PETUGAS PELAYANAN:*\n`;
        text += `• Pengkhotbah: ${this.formData.pengkhotbah || '-'}\n`;
        text += `• Pemimpin Pujian (WL): ${this.formData.pemimpin_pujian || '-'}\n`;
        if (this.formData.petugas_tambahan.pemusik && this.formData.petugas_tambahan.pemusik.length > 0) {
          text += `• Pemusik: ${this.getPemusikSummary()}\n`;
        }
        if (this.formData.petugas_tambahan.penerima_tamu && this.formData.petugas_tambahan.penerima_tamu.filter(p => p).length > 0) {
          text += `• Penerima Tamu: ${this.formData.petugas_tambahan.penerima_tamu.filter(p => p).join(', ')}\n`;
        }
        if (this.formData.petugas_tambahan.soundman && this.formData.petugas_tambahan.soundman.filter(p => p).length > 0) {
          text += `• Soundman: ${this.formData.petugas_tambahan.soundman.filter(p => p).join(', ')}\n`;
        }
        if (this.formData.petugas_tambahan.multimedia && this.formData.petugas_tambahan.multimedia.filter(p => p).length > 0) {
          text += `• Multimedia: ${this.formData.petugas_tambahan.multimedia.filter(p => p).join(', ')}\n`;
        }

        if (this.formData.songs && this.formData.songs.length > 0) {
          text += `\n*SUSUNAN LITURGI & LAGU:*\n`;
          this.formData.songs.forEach((s, i) => {
            text += `${i + 1}. [${s.bagian_liturgi || 'Pujian'}] ${s.judul_lagu} (Nada: ${s.nada_dasar || '-'})\n`;
            if (this.waIncludeSongLinks) {
              if (s.youtube_url) text += `   - YouTube: ${s.youtube_url}\n`;
              if (s.spotify_url) text += `   - Spotify: ${s.spotify_url}\n`;
              if (s.chord_url) text += `   - Chord: ${s.chord_url}\n`;
            }
          });
        }

        if (this.waIncludeMediaLinks && this.formData.bahan_media && this.formData.bahan_media.length > 0) {
          text += `\n*BAHAN KHOTBAH & MEDIA:*\n`;
          this.formData.bahan_media.forEach((m, idx) => {
            text += `• ${m.judul} (${m.tipe || 'File'}): ${m.url}\n`;
          });
        }

        if (this.formData.catatan_singkat) {
          text += `\n*Catatan:*\n${this.formData.catatan_singkat}\n`;
        }

        const publicUrl = this.formData.id ? `{{ url('/ibadah') }}?id=${this.formData.id}` : `{{ url('/ibadah') }}`;
        text += `\nTautan Jadwal & Liturgi:\n${publicUrl}\n\nTuhan Yesus Memberkati.`;

        this.waGeneratedText = text;
      },

      sendDirectWA() {
        const phone = (this.waTargetPhone || '').replace(/[^0-9]/g, '');
        if (!phone) {
          alert('Pilih nomor kontak tujuan WhatsApp.');
          return;
        }
        const url = `https://wa.me/${phone}?text=${encodeURIComponent(this.waGeneratedText)}`;
        window.open(url, '_blank');
      },

      sendWAWeb() {
        const url = `https://api.whatsapp.com/send?text=${encodeURIComponent(this.waGeneratedText)}`;
        window.open(url, '_blank');
      },

      copyWAText() {
        navigator.clipboard.writeText(this.waGeneratedText).then(() => {
          this.showToast('Teks pesan WhatsApp berhasil disalin!');
        });
      },

      getPemusikSummary() {
        const musicians = this.formData.petugas_tambahan.pemusik.filter(p => p.nama);
        if (musicians.length === 0) return '-';
        return musicians.map(p => `${p.nama} (${p.instrumen})`).join(', ');
      },

      formatDateHuman(dStr) {
        if (!dStr) return '';
        const d = new Date(dStr);
        return d.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
      },

      copyPublicLink() {
        const url = '{{ url('/ibadah') }}';
        navigator.clipboard.writeText(url).then(() => {
          this.showToast('Tautan jadwal publik disalin ke clipboard!');
        });
      },

      shareWhatsApp() {
        let text = `*RUNDOWN PERSIAPAN IBADAH - GIA SURABAYA*\n`;
        text += `Tanggal: ${this.formatDateHuman(this.formData.tanggal)}\n`;
        text += `Ibadah: ${this.formData.nama_ibadah} (${this.formData.nama_cabang})\n`;
        if (this.formData.tema) text += `Tema: "${this.formData.tema}"\n`;
        if (this.formData.ayat) text += `Ayat: ${this.formData.ayat}\n`;
        text += `\n*PETUGAS:*\n`;
        text += `• Pengkhotbah: ${this.formData.pengkhotbah || '-'}\n`;
        text += `• Pemimpin Pujian (WL): ${this.formData.pemimpin_pujian || '-'}\n`;
        if (this.formData.petugas_tambahan.pemusik.length > 0) {
          text += `• Pemusik: ${this.getPemusikSummary()}\n`;
        }
        text += `\n*SUSUNAN LAGU:*\n`;
        this.formData.songs.forEach((s, i) => {
          text += `${i + 1}. [${s.bagian_liturgi}] ${s.judul_lagu} (${s.nada_dasar || '-'})\n`;
          if (s.youtube_url) text += `   Video: ${s.youtube_url}\n`;
          if (s.chord_url) text += `   Chord: ${s.chord_url}\n`;
        });
        text += `\nTautan Jadwal: {{ url('/ibadah') }}`;

        const waUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(text);
        window.open(waUrl, '_blank');
      },

      saveAll() {
        this.isSaving = true;
        const isCreate = !this.formData.id;
        const url = isCreate 
          ? '{{ route('admin.attendance.storeSchedule') }}'
          : `/admin/attendance/persiapan/schedule/${this.formData.id}`;

        const method = isCreate ? 'POST' : 'PUT';

        fetch(url, {
          method: method,
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          },
          body: JSON.stringify(this.formData)
        })
        .then(res => res.json())
        .then(data => {
          this.isSaving = false;
          if (data.success) {
            this.isDirty = false;
            this.showToast('Data jadwal ibadah & liturgi berhasil disimpan!');
            if (data.updated_by) this.updatedBy = data.updated_by;
            if (data.updated_at) this.updatedAt = data.updated_at;
            if (isCreate && data.redirect_url) {
              window.location.href = data.redirect_url;
            }
          } else {
            alert('Gagal menyimpan: ' + (data.message || 'Terjadi kesalahan'));
          }
        })
        .catch(err => {
          this.isSaving = false;
          alert('Kesalahan jaringan: ' + err.message);
        });
      }
    };
  }
</script>
@endsection

