@extends('admin.layoutCMS')

@section('content')
  <div class="flex justify-between items-center mb-6">
    <div>
      <h4 class="text-slate-900 dark:text-slate-200 text-lg font-bold mb-1">Manajemen Event</h4>
      <p class="text-slate-400 text-sm">Kelola seluruh agenda, KKR, dan kegiatan ibadah di semua cabang.</p>
    </div>
    <button onclick="document.getElementById('addEventModal').classList.remove('hidden')"
      class="btn bg-primary text-white font-medium text-sm px-4 py-2 rounded shadow hover:bg-primary-dark transition">
      <i class="ri-add-line pe-1"></i> Tambah Event
    </button>
  </div>

  @if (Session::has('success') || session('success'))
    <div id="success-alert"
      class="bg-success p-4 rounded-lg mb-6 text-sm font-semibold text-white shadow-md transition-opacity duration-500">
      <span class="flex items-center gap-2">
        <span>✅</span>
        <span>{{ Session::get('success') ?? session('success') }}</span>
      </span>
    </div>
  @endif

  <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Event</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Cabang</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Event Start</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Event Finish</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          @forelse($events as $event)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50">
              <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-200">
                <div class="flex items-center gap-3">
                  <img
                    src="{{ $event->gambar_banner ? asset('img/admin/events/' . $event->gambar_banner) . '?t=' . time() : asset('images/default-banner.jpg') }}"
                    class="w-14 h-10 object-cover rounded border border-gray-200 shadow-sm bg-slate-100" alt="Banner"
                    onerror="this.onerror=null;this.src='{{ asset('images/default-banner.jpg') }}';">
                  <span>{{ $event->nama_acara }}</span>
                </div>
              </td>

              <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                {{ $event->cabang ? $event->cabang->nama_cabang : 'Pusat / Semua Cabang' }}</td>

              <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                <div class="font-medium text-slate-800 dark:text-slate-200">
                  {{ \Carbon\Carbon::parse($event->waktu_mulai)->format('d M Y') }}</div>
                <div class="text-xs text-slate-400 mt-0.5">⏰
                  {{ \Carbon\Carbon::parse($event->waktu_mulai)->format('H:i') }} WIB</div>
              </td>

              <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                <div class="font-medium text-slate-800 dark:text-slate-200">
                  {{ \Carbon\Carbon::parse($event->waktu_selesai)->format('d M Y') }}</div>
                <div class="text-xs text-slate-400 mt-0.5">⏰
                  {{ \Carbon\Carbon::parse($event->waktu_selesai)->format('H:i') }} WIB</div>
              </td>

              <td class="px-6 py-4 text-sm align-middle whitespace-nowrap">
                @php
                  $sekarang = \Carbon\Carbon::now();
                  $mulai = \Carbon\Carbon::parse($event->waktu_mulai);
                  $selesai = \Carbon\Carbon::parse($event->waktu_selesai);
                @endphp
                @if ($sekarang->lt($mulai))
                  <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider gap-1.5">🔮
                    <span class="ms-0.5">Future</span></span>
                @elseif ($sekarang->between($mulai, $selesai))
                  <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider animate-pulse gap-1.5">🟢
                    <span class="ms-0.5">Ongoing</span></span>
                @else
                  <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200 uppercase tracking-wider gap-1.5">⏰
                    <span class="ms-0.5">Expired</span></span>
                @endif
              </td>

              <td class="px-6 py-4 text-sm text-center align-middle whitespace-nowrap space-x-2">
                <button type="button"
                  class="btn-edit-event text-sm text-primary hover:underline font-semibold inline-flex items-center gap-0.5"
                  data-id="{{ $event->id }}" data-nama="{{ $event->nama_acara }}"
                  data-cabang="{{ $event->cabang_id }}" data-lokasi="{{ $event->lokasi_spesifik }}"
                  data-mulai="{{ $event->waktu_mulai }}" data-selesai="{{ $event->waktu_selesai }}"
                  data-banner="{{ $event->gambar_banner }}" data-deskripsi="{{ $event->deskripsi_acara }}">
                  <i class="ri-edit-box-line"></i> Edit
                </button>
                <button type="button" onclick="openDeleteModal({{ $event->id }}, '{{ $event->nama_acara }}')"
                  class="text-sm text-danger hover:underline font-semibold inline-flex items-center gap-0.5"><i
                    class="ri-delete-bin-line"></i> Hapus</button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500 italic">Belum ada data event yang
                terdaftar.</td>
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
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Cabang Pelaksana</label>
              <select name="cabang_id" required
                class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm p-2.5 focus:border-primary focus:ring-0">
                <option value="" disabled selected>-- Pilih Cabang --</option>
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
          <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 dark:border-gray-700 mt-2">
            <button type="button" onclick="document.getElementById('addEventModal').classList.add('hidden')"
              class="btn bg-gray-100 text-gray-700 font-medium text-sm px-4 py-2 rounded hover:bg-gray-200 transition">Batal</button>
            <button type="submit"
              class="btn bg-primary text-white font-medium text-sm px-4 py-2 rounded shadow hover:bg-primary-dark transition">Simpan
              & Publikasikan</button>
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
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Cabang Pelaksana</label>
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
          <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 dark:border-gray-700 mt-2">
            <button type="button" onclick="document.getElementById('editEventModal').classList.add('hidden')"
              class="btn bg-gray-100 text-gray-700 font-medium text-sm px-4 py-2 rounded hover:bg-gray-200 transition">Batal</button>
            <button type="submit"
              class="btn bg-primary text-white font-medium text-sm px-4 py-2 rounded shadow hover:bg-primary-dark transition">Simpan
              Perubahan</button>
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
              class="btn bg-gray-100 text-gray-700 font-medium text-sm px-4 py-2 rounded hover:bg-gray-200 transition w-24">Batal</button>
            <button type="submit"
              class="btn bg-danger text-white font-medium text-sm px-4 py-2 rounded shadow hover:bg-danger-dark transition w-24">Hapus</button>
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
