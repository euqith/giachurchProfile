@extends('admin.layoutCMS')

@section('content')
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold mb-1.5 border border-amber-200/80">
        <i class="ri-calendar-event-line"></i> Agenda & Kegiatan Gereja
      </div>
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Event</h1>
      <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh agenda, KKR, dan kegiatan ibadah di semua wilayah.</p>
    </div>
    <button onclick="document.getElementById('addEventModal').classList.remove('hidden')"
      class="btn-cms-primary">
      <i class="ri-add-line text-base"></i> Tambah Event
    </button>
  </div>

  @if (Session::has('success') || session('success'))
    <div id="success-alert"
      class="bg-emerald-500 p-4 rounded-xl mb-6 text-sm font-semibold text-white shadow-md transition-opacity duration-500">
      <span class="flex items-center gap-2">
        <span>✅</span>
        <span>{{ Session::get('success') ?? session('success') }}</span>
      </span>
    </div>
  @endif

  <div class="table-modern-wrapper mb-6">
    <div class="overflow-x-auto">
      <table class="min-w-full">
        <thead>
          <tr>
            <th>Nama Event</th>
            <th>Wilayah</th>
            <th>Event Start</th>
            <th>Event Finish</th>
            <th>Status</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($events as $event)
            <tr class="hover:bg-slate-50/70 transition-colors">
              <td class="px-6 py-4 text-sm font-semibold text-slate-900">
                <div class="flex items-center gap-3">
                  <img
                    src="{{ $event->gambar_banner ? asset('img/admin/events/' . $event->gambar_banner) . '?t=' . time() : asset('images/default-banner.jpg') }}"
                    class="w-14 h-10 object-cover rounded-xl border border-slate-200 shadow-2xs bg-slate-100" alt="Banner"
                    onerror="this.onerror=null;this.src='{{ asset('images/default-banner.jpg') }}';">
                  <span class="font-semibold text-slate-900">{{ $event->nama_acara }}</span>
                </div>
              </td>

              <td class="px-6 py-4 text-sm text-slate-600">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200/60">
                  {{ $event->cabang ? $event->cabang->nama_cabang : 'Pusat / Semua Wilayah' }}
                </span>
              </td>

              <td class="px-6 py-4 text-sm text-slate-600 whitespace-nowrap">
                <div class="font-semibold text-slate-800">
                  {{ \Carbon\Carbon::parse($event->waktu_mulai)->format('d M Y') }}
                </div>
                <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                  <i class="ri-time-line text-amber-600"></i>
                  <span>{{ \Carbon\Carbon::parse($event->waktu_mulai)->format('H:i') }} WIB</span>
                </div>
              </td>

              <td class="px-6 py-4 text-sm text-slate-600 whitespace-nowrap">
                <div class="font-semibold text-slate-800">
                  {{ \Carbon\Carbon::parse($event->waktu_selesai)->format('d M Y') }}
                </div>
                <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                  <i class="ri-time-line text-slate-400"></i>
                  <span>{{ \Carbon\Carbon::parse($event->waktu_selesai)->format('H:i') }} WIB</span>
                </div>
              </td>

              <td class="px-6 py-4 text-sm align-middle whitespace-nowrap">
                @php
                  $sekarang = \Carbon\Carbon::now();
                  $mulai = \Carbon\Carbon::parse($event->waktu_mulai);
                  $selesai = \Carbon\Carbon::parse($event->waktu_selesai);
                @endphp
                @if ($sekarang->lt($mulai))
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200 uppercase tracking-wider gap-1.5 shadow-2xs">
                    <span>🔮 Future</span>
                  </span>
                @elseif ($sekarang->between($mulai, $selesai))
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider gap-1.5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>🟢 Ongoing</span>
                  </span>
                @else
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200 uppercase tracking-wider gap-1.5">
                    <span>⏰ Expired</span>
                  </span>
                @endif
              </td>

              <td class="px-6 py-4 text-sm text-center align-middle whitespace-nowrap space-x-2">
                <button type="button"
                  class="btn-action-edit btn-edit-event"
                  data-id="{{ $event->id }}" data-nama="{{ $event->nama_acara }}"
                  data-cabang="{{ $event->cabang_id }}" data-lokasi="{{ $event->lokasi_spesifik }}"
                  data-mulai="{{ $event->waktu_mulai }}" data-selesai="{{ $event->waktu_selesai }}"
                  data-banner="{{ $event->gambar_banner }}" data-deskripsi="{{ $event->deskripsi_acara }}">
                  <i class="ri-edit-line"></i> Edit
                </button>
                <button type="button" onclick="openDeleteModal({{ $event->id }}, '{{ $event->nama_acara }}')"
                  class="btn-action-delete">
                  <i class="ri-delete-bin-line"></i> Hapus
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400 italic">
                <i class="ri-calendar-line text-3xl text-slate-300 block mb-2"></i>
                Belum ada data event yang terdaftar.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div id="addEventModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity z-10"
        onclick="document.getElementById('addEventModal').classList.add('hidden')"></div>

      <div
        class="bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-2xl sm:w-full border border-gray-100 dark:border-gray-700 z-20 max-h-[90vh] overflow-y-auto relative">
        <div
          class="flex justify-between items-center px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
          <h3 class="text-base font-bold text-slate-900 dark:text-slate-200">FORM TAMBAH EVENT</h3>
          <button onclick="document.getElementById('addEventModal').classList.add('hidden')" type="button"
            class="text-gray-400 hover:text-gray-500"><i class="ri-close-line text-xl"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.event.store') }}" enctype="multipart/form-data"
          class="p-6 space-y-4">
          @csrf
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Event / Acara</label>
              <input type="text" name="nama_acara" value="{{ old('nama_acara') }}" placeholder="Contoh: KKR Pemuda"
                required
                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm p-2.5 focus:border-primary focus:ring-0">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Wilayah Pelaksana</label>
              <select name="cabang_id" required
                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm p-2.5 focus:border-primary focus:ring-0">
                <option value="" disabled selected>-- Pilih Wilayah --</option>
                @foreach ($cabangs as $cabang)
                  <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Lokasi Spesifik /
                Ruangan</label>
              <input type="text" name="lokasi_spesifik" value="{{ old('lokasi_spesifik') }}"
                placeholder="Contoh: Main Hall lt. 2" required
                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm p-2.5 focus:border-primary focus:ring-0">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Event Start</label>
              <input type="datetime-local" name="waktu_mulai" value="{{ old('waktu_mulai') }}" required
                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm p-2.5 focus:border-primary focus:ring-0">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Event Finish</label>
              <input type="datetime-local" name="waktu_selesai" value="{{ old('waktu_selesai') }}" required
                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm p-2.5 focus:border-primary focus:ring-0">
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Upload Gambar
                Banner</label>
              <input type="file" name="gambar_banner"
                class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Deskripsi / Detail
                Acara</label>
              <textarea name="deskripsi_acara" rows="3" placeholder="Info susunan acara..." required
                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm p-2.5 focus:border-primary focus:ring-0">{{ old('deskripsi_acara') }}</textarea>
            </div>
          </div>
          <div class="flex justify-end gap-2 pt-4 border-t border-slate-100 mt-2">
            <button type="button" onclick="document.getElementById('addEventModal').classList.add('hidden')"
              class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition border border-slate-200 cursor-pointer">Batal</button>
            <button type="submit"
              class="btn-cms-primary text-xs">Simpan & Publikasikan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="editEventModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity z-10"
        onclick="document.getElementById('editEventModal').classList.add('hidden')"></div>

      <div
        class="bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-2xl sm:w-full border border-gray-100 dark:border-gray-700 z-20 max-h-[90vh] overflow-y-auto relative">
        <div
          class="flex justify-between items-center px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
          <h3 class="text-base font-bold text-slate-900 dark:text-slate-200">FORM EDIT EVENT</h3>
          <button onclick="document.getElementById('editEventModal').classList.add('hidden')" type="button"
            class="text-gray-400 hover:text-gray-500"><i class="ri-close-line text-xl"></i></button>
        </div>
        <form id="editForm" method="POST" action="" enctype="multipart/form-data" class="p-6 space-y-4">
          @csrf
          @method('PUT')
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Event / Acara</label>
              <input type="text" id="edit_nama_acara" name="nama_acara" required
                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm p-2.5 focus:border-primary focus:ring-0">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Wilayah Pelaksana</label>
              <select id="edit_cabang_id" name="cabang_id" required
                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm p-2.5 focus:border-primary focus:ring-0">
                @foreach ($cabangs as $cabang)
                  <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Lokasi Spesifik /
                Ruangan</label>
              <input type="text" id="edit_lokasi_spesifik" name="lokasi_spesifik" required
                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm p-2.5 focus:border-primary focus:ring-0">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Event Start</label>
              <input type="datetime-local" id="edit_waktu_mulai" name="waktu_mulai" required
                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm p-2.5 focus:border-primary focus:ring-0">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Event Finish</label>
              <input type="datetime-local" id="edit_waktu_selesai" name="waktu_selesai" required
                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm p-2.5 focus:border-primary focus:ring-0">
            </div>

            <div
              class="md:col-span-2 flex items-center gap-4 bg-slate-50 dark:bg-slate-700/30 p-3 rounded border border-dashed border-gray-200 dark:border-gray-600">
              <div
                class="w-16 h-12 bg-gray-200 rounded overflow-hidden flex-shrink-0 border border-gray-300 shadow-inner">
                <img id="edit_preview_banner" src="" class="w-full h-full object-cover hidden"
                  alt="Preview Banner">
              </div>
              <div class="flex-1">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                  Banner Aktif: <span id="edit_text_banner" class="text-xs font-normal text-primary underline"></span>
                </label>
                <input type="file" name="gambar_banner"
                  class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
              </div>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Deskripsi / Detail
                Acara</label>
              <textarea id="edit_deskripsi_acara" name="deskripsi_acara" rows="3" required
                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm p-2.5 focus:border-primary focus:ring-0"></textarea>
            </div>
          </div>
          <div class="flex justify-end gap-2 pt-4 border-t border-slate-100 mt-2">
            <button type="button" onclick="document.getElementById('editEventModal').classList.add('hidden')"
              class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition border border-slate-200 cursor-pointer">Batal</button>
            <button type="submit"
              class="btn-cms-primary text-xs">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="deleteEventModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity z-10"
        onclick="document.getElementById('deleteEventModal').classList.add('hidden')"></div>

      <div
        class="bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-md sm:w-full border border-gray-100 dark:border-gray-700 z-20 relative">
        <form id="deleteForm" method="POST" action="">
          @csrf
          @method('DELETE')

          <div class="p-6 text-center">
            <div
              class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-danger bg-opacity-10 text-danger mb-4">
              <i class="ri-error-warning-line text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-200 mb-2">Konfirmasi Hapus</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">
              Apakah kamu yakin ingin menghapus event <span id="delete_event_name"
                class="font-semibold text-slate-800 dark:text-slate-100"></span>? Tindakan ini tidak dapat dibatalkan.
            </p>
          </div>

          <div class="flex justify-center gap-2 pb-6 px-6">
            <button type="button" onclick="document.getElementById('deleteEventModal').classList.add('hidden')"
              class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition border border-slate-200 w-24 cursor-pointer">Batal</button>
            <button type="submit"
              class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs transition shadow-sm w-24 cursor-pointer">Hapus</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // ⚙️ 1. Handler Klik Tombol Edit Menggunakan Data Attributes (Anti-Crash)
      const editButtons = document.querySelectorAll('.btn-edit-event');

      editButtons.forEach(button => {
        button.addEventListener('click', function() {
          // Ambil semua data dari atribut HTML tombol yang diklik
          const id = this.getAttribute('data-id');
          const nama = this.getAttribute('data-nama');
          const cabang = this.getAttribute('data-cabang');
          const lokasi = this.getAttribute('data-lokasi');
          const mulai = this.getAttribute('data-mulai');
          const selesai = this.getAttribute('data-selesai');
          const banner = this.getAttribute('data-banner');
          const deskripsi = this.getAttribute('data-deskripsi');

          // 1. Set Action Form Target Update
          document.getElementById('editForm').action = `/admin/event/${id}`;

          // 2. Inject Data Teks ke Form
          document.getElementById('edit_nama_acara').value = nama;
          document.getElementById('edit_cabang_id').value = cabang;
          document.getElementById('edit_lokasi_spesifik').value = lokasi;
          document.getElementById('edit_deskripsi_acara').value = deskripsi;

          // 3. Pengecekan & Tampilkan Preview Gambar Banner
          const previewBanner = document.getElementById('edit_preview_banner');
          const textBanner = document.getElementById('edit_text_banner');

          if (banner && banner.trim() !== "" && banner !== "null") {
            previewBanner.src = `/img/admin/events/${banner}?t=${new Date().getTime()}`;
            previewBanner.classList.remove('hidden');
            if (textBanner) textBanner.innerText = banner;
          } else {
            previewBanner.src = "";
            previewBanner.classList.add('hidden');
            if (textBanner) textBanner.innerText = 'Belum ada banner';
          }

          // 4. Inject Tanggal Waktu (Potong format agar pas dengan datetime-local input)
          if (mulai) {
            document.getElementById('edit_waktu_mulai').value = mulai.slice(0, 16).replace(' ', 'T');
          }
          if (selesai) {
            document.getElementById('edit_waktu_selesai').value = selesai.slice(0, 16).replace(' ', 'T');
          }

          // 5. Buka Modal
          document.getElementById('editEventModal').classList.remove('hidden');
        });
      });
    });

    // ⚙️ 2. Handler Modal Hapus Kustom
    function openDeleteModal(eventId, eventName) {
      document.getElementById('deleteForm').action = `/admin/event/${eventId}`;
      document.getElementById('delete_event_name').innerText = eventName;
      document.getElementById('deleteEventModal').classList.remove('hidden');
    }

    // ⏰ 3. Auto Dismiss Alert 3 Detik
    window.addEventListener('DOMContentLoaded', (event) => {
      const alert = document.getElementById('success-alert');
      if (alert) {
        setTimeout(() => {
          alert.style.opacity = '0';
          setTimeout(() => {
            alert.remove();
          }, 500);
        }, 3000);
      }
    });
  </script>
@endsection
