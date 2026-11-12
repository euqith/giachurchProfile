@extends('admin.layoutCMS')

@section('content')
  <div class="mb-6">
    <a href="{{ route('admin.article.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition mb-3">
      <i class="ri-arrow-left-line"></i> Kembali ke Kelola Artikel
    </a>
    <div class="flex items-center gap-2">
      <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold">
        <i class="ri-edit-2-line text-lg"></i>
      </div>
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tulis Artikel Baru</h1>
        <p class="text-xs text-slate-500">Tulis renungan firman, warta kegiatan, atau pengajaran bagi jemaat.</p>
      </div>
    </div>
  </div>

  @if ($errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl mb-6 text-xs space-y-1">
      <div class="font-bold flex items-center gap-1.5">
        <i class="ri-error-warning-line"></i> Terdapat kesalahan pengisian formulir:
      </div>
      <ul class="list-disc pl-5">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.article.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Main Content (2 cols) -->
      <div class="lg:col-span-2 space-y-5">
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-2xs space-y-4">
          <!-- Judul Artikel -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Judul Artikel / Renungan *</label>
            <input type="text" name="judul" value="{{ old('judul') }}" required
              placeholder="Contoh: Mengakar Kuat Dalam Kasih Kristus di Tengah Badai Hidup"
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-900 focus:outline-none focus:border-amber-600 bg-white">
          </div>

          <!-- Ringkasan Excerpt -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Ringkasan Singkat (Excerpt)</label>
            <textarea name="ringkasan" rows="3"
              placeholder="Tuliskan 1-2 kalimat ringkasan menarik yang akan muncul di kartu artikel beranda..."
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-amber-600 bg-white">{{ old('ringkasan') }}</textarea>
            <span class="text-[11px] text-slate-400">Jika dikosongkan, ringkasan akan otomatis diambil dari kalimat awal konten.</span>
          </div>

          <!-- Konten Lengkap -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">Isi Konten Artikel *</label>
              <span class="text-[11px] text-slate-400">Mendukung format paragraf & baris baru</span>
            </div>
            <textarea name="konten" rows="16" required
              placeholder="Tuliskan isi renungan atau artikel selengkapnya di sini... Anda dapat menggunakan enter untuk membuat paragraf baru."
              class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:border-amber-600 bg-white leading-relaxed">{{ old('konten') }}</textarea>
          </div>
        </div>
      </div>

      <!-- Sidebar Settings (1 col) -->
      <div class="space-y-5">
        <!-- Publishing Box -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs space-y-4">
          <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
            <i class="ri-settings-3-line text-amber-600"></i> Pengaturan Publikasi
          </h3>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Publikasi *</label>
            <select name="status" required
              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-amber-600 bg-white text-slate-800">
              <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>🟢 Langsung Publikasikan (Published)</option>
              <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>⚪ Simpan Sebagai Draft</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Artikel *</label>
            <select name="kategori" required
              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-amber-600 bg-white text-slate-800">
              @foreach($kategoriList as $kat)
                <option value="{{ $kat }}" {{ old('kategori', 'Renungan') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
              @endforeach
            </select>
          </div>

          <div class="pt-3 border-t border-slate-100 space-y-2">
            <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-xs">
              Simpan & Terbitkan
            </button>
            <a href="{{ route('admin.article.index') }}" class="block text-center w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs transition">
              Batal
            </a>
          </div>
        </div>

        <!-- Banner Image Upload -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs space-y-4">
          <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
            <i class="ri-image-line text-amber-600"></i> Gambar Banner Artikel
          </h3>

          <div class="space-y-3">
            <div id="imagePreviewContainer" class="hidden">
              <img id="imagePreview" src="" class="w-full h-40 object-cover rounded-xl border border-slate-200 shadow-2xs" alt="Preview">
            </div>

            <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 text-center hover:border-amber-500 transition cursor-pointer" onclick="document.getElementById('gambar_banner_input').click()">
              <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-700 flex items-center justify-center mx-auto mb-2 text-lg">
                <i class="ri-upload-cloud-line"></i>
              </div>
              <p class="text-xs font-bold text-slate-700">Pilih Gambar Banner</p>
              <p class="text-[11px] text-slate-400 mt-0.5">JPG, PNG, atau WEBP (Maksimal 5 MB)</p>
            </div>

            <input type="file" id="gambar_banner_input" name="gambar_banner" accept="image/*" class="hidden" onchange="previewBanner(this)">
          </div>
        </div>

      </div>

    </div>
  </form>

  <script>
    function previewBanner(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('imagePreview').src = e.target.result;
          document.getElementById('imagePreviewContainer').classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>
@endsection
