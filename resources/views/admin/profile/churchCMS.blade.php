@extends('admin.layoutCMS')

@section('content')
<div class="space-y-6 max-w-5xl">

  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold mb-1 border border-amber-200">
        <i class="ri-government-line"></i> Profil Website & Banner
      </div>
      <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pengaturan Profil Gereja</h1>
      <p class="text-xs text-slate-500 mt-0.5">Kelola foto utama gereja (Banner 1), tentang gereja, visi misi, serta informasi kontak yang ditampilkan di beranda publik.</p>
    </div>
  </div>

  @if (session('success'))
    <div class="p-4 rounded-xl bg-emerald-500 text-white text-xs font-bold flex items-center justify-between shadow-sm">
      <div class="flex items-center gap-2">
        <i class="ri-checkbox-circle-fill text-lg"></i>
        <span>{{ session('success') }}</span>
      </div>
      <button type="button" onclick="this.parentElement.remove()" class="text-white/80 hover:text-white">✕</button>
    </div>
  @endif

  @if ($errors->any())
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
      <div class="font-bold flex items-center gap-1.5">
        <i class="ri-error-warning-fill text-rose-600"></i> Mohon periksa kembali form berikut:
      </div>
      <ul class="list-disc list-inside space-y-0.5 text-[11px] pl-2 text-rose-700">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.church.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <!-- CARD 1: FOTO UTAMA GEREJA (SLIDE BANNER 1) -->
    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
      <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-sm font-bold">
            1
          </div>
          <div>
            <h2 class="text-sm font-bold text-slate-900">Foto Utama Gereja (Banner 1 Homepage)</h2>
            <p class="text-[11px] text-slate-500">Foto gedung / tempat ibadah utama yang selalu tampil fix pada slide pertama banner beranda.</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-center">
        <!-- Preview Box -->
        <div class="md:col-span-5">
          <div class="relative rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 shadow-inner group aspect-video">
            @php
              $photoUrl = $profile->foto_gereja 
                ? asset('img/admin/church/' . $profile->foto_gereja) 
                : asset('homepage/assets/images/banner/banner-image1.jpg');
            @endphp
            <img id="churchPhotoPreview" src="{{ $photoUrl }}" alt="Foto Gereja"
              class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-3">
              <span class="text-[11px] text-white/90 font-medium">Pratinjau Banner 1 Saat Ini</span>
            </div>
          </div>
        </div>

        <!-- Upload Controls -->
        <div class="md:col-span-7 space-y-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Unggah Foto Gereja Baru</label>
            <input type="file" name="foto_gereja" id="foto_gereja" accept="image/*"
              onchange="const f = this.files[0]; if(f){ document.getElementById('churchPhotoPreview').src = URL.createObjectURL(f); }"
              class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 cursor-pointer">
            <p class="text-[11px] text-slate-400 mt-1.5">
              Format: JPG, JPEG, PNG, WEBP. Ukuran rekomendasi minimal 1920x1080 px untuk tampilan tajam. Maksimal 5 MB.
            </p>
          </div>

          <div class="p-3 bg-amber-50/70 rounded-xl border border-amber-200/60 text-[11px] text-amber-900 leading-relaxed">
            <i class="ri-information-line text-amber-700 mr-1"></i>
            Slide banner selanjutnya (Banner 2, 3, dst.) akan otomatis menampilkan preview dari <strong>Event Aktif & Mendatang</strong> yang dibuat di menu Kelola Event.
          </div>
        </div>
      </div>
    </div>

    <!-- CARD 2: IDENTITAS & TENTANG GEREJA -->
    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
      <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-sm font-bold">
          2
        </div>
        <div>
          <h2 class="text-sm font-bold text-slate-900">Tentang Gereja & Sambutan</h2>
          <p class="text-[11px] text-slate-500">Teks pengenalan gereja, sejarah singkat, dan moto pelayanan untuk jemaat & pengunjung.</p>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nama Gereja *</label>
          <input type="text" name="nama_gereja" value="{{ old('nama_gereja', $profile->nama_gereja) }}" required
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-900 focus:outline-none focus:border-amber-600 bg-white">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Tagline / Moto Banner</label>
          <input type="text" name="tagline" value="{{ old('tagline', $profile->tagline) }}"
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-amber-600 bg-white">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Tentang Gereja (Sambutan / Deskripsi Utama)</label>
        <textarea name="tentang_gereja" rows="4"
          class="w-full p-3 rounded-xl border border-slate-200 text-xs leading-relaxed text-slate-800 focus:outline-none focus:border-amber-600 bg-white">{{ old('tentang_gereja', $profile->tentang_gereja) }}</textarea>
        <p class="text-[10px] text-slate-400 mt-1">Ditampilkan pada section "Tentang Gereja" di halaman depan.</p>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Sejarah Singkat (Opsional)</label>
        <textarea name="sejarah_singkat" rows="3"
          class="w-full p-3 rounded-xl border border-slate-200 text-xs leading-relaxed text-slate-800 focus:outline-none focus:border-amber-600 bg-white">{{ old('sejarah_singkat', $profile->sejarah_singkat) }}</textarea>
      </div>
    </div>

    <!-- CARD 3: VISI & MISI GEREJA -->
    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
      <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-sm font-bold">
          3
        </div>
        <div>
          <h2 class="text-sm font-bold text-slate-900">Visi & Misi Gereja</h2>
          <p class="text-[11px] text-slate-500">Landasan dan arah pelayanan gereja yang ditampilkan pada section profil beranda.</p>
        </div>
      </div>

      <div class="space-y-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Visi Gereja</label>
          <textarea name="visi" rows="3"
            class="w-full p-3 rounded-xl border border-slate-200 text-xs leading-relaxed text-slate-800 focus:outline-none focus:border-amber-600 bg-white">{{ old('visi', $profile->visi) }}</textarea>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Misi Gereja (Pisahkan dengan baris baru)</label>
          <textarea name="misi" rows="5"
            class="w-full p-3 rounded-xl border border-slate-200 text-xs leading-relaxed text-slate-800 focus:outline-none focus:border-amber-600 bg-white">{{ old('misi', $profile->misi) }}</textarea>
          <p class="text-[10px] text-slate-400 mt-1">Setiap baris akan ditampilkan sebagai poin misi di halaman beranda.</p>
        </div>
      </div>
    </div>

    <!-- CARD 4: KONTAK & MEDIA SOSIAL PUSAT -->
    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
      <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-sm font-bold">
          4
        </div>
        <div>
          <h2 class="text-sm font-bold text-slate-900">Kontak & Media Sosial</h2>
          <p class="text-[11px] text-slate-500">Informasi sekretariat pusat dan kanal resmi yang tertera di topbar & footer beranda.</p>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="sm:col-span-3">
          <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Kantor Pusat</label>
          <input type="text" name="alamat_pusat" value="{{ old('alamat_pusat', $profile->alamat_pusat) }}"
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-amber-600 bg-white">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Telepon / WhatsApp</label>
          <input type="text" name="telepon" value="{{ old('telepon', $profile->telepon) }}"
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-amber-600 bg-white">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Email Sekretariat</label>
          <input type="email" name="email" value="{{ old('email', $profile->email) }}"
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-amber-600 bg-white">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Tautan YouTube Resmi</label>
          <input type="url" name="youtube_channel_url" value="{{ old('youtube_channel_url', $profile->youtube_channel_url) }}" placeholder="https://youtube.com/@..."
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-amber-600 bg-white">
        </div>
      </div>
    </div>

    <!-- SUBMIT BAR -->
    <div class="flex items-center justify-end gap-3 pt-2">
      <button type="submit"
        class="px-6 py-2.5 rounded-xl bg-amber-800 hover:bg-amber-900 text-white text-xs font-bold shadow-md shadow-amber-900/20 inline-flex items-center gap-2 transition">
        <i class="ri-save-line text-sm"></i> Simpan Pengaturan Profil
      </button>
    </div>

  </form>

</div>
@endsection
