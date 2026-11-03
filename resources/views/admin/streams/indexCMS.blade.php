@extends('admin.layoutCMS')

@section('content')
<div class="space-y-6 max-w-6xl">

  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-red-700 text-xs font-semibold mb-1 border border-red-200">
        <i class="ri-broadcast-line"></i> Kanal Ibadah Online & Auto-Detect
      </div>
      <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kelola Live Streaming</h1>
      <p class="text-xs text-slate-500 mt-0.5">Atur akun YouTube dan otomatisasi deteksi siaran langsung untuk setiap wilayah gereja.</p>
    </div>

    <!-- Action Buttons & Global Indicator -->
    <div class="flex flex-wrap items-center gap-2.5">
      <!-- TOMBOL AUTO-DETECT SEMUA WILAYAH -->
      <form action="{{ route('admin.stream.auto-detect') }}" method="POST">
        @csrf
        <button type="submit"
          class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-600/20 inline-flex items-center gap-2 transition cursor-pointer">
          <i class="ri-refresh-line text-sm"></i> Cek Status Streaming Sekarang
        </button>
      </form>

      @if ($anyLive)
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-red-600 text-white text-xs font-bold shadow-md shadow-red-600/30 animate-pulse">
          <span class="w-2.5 h-2.5 rounded-full bg-white"></span>
          <span>SEDANG SIARAN LANGSUNG (ON AIR)</span>
        </span>
      @else
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold border border-slate-200">
          <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
          <span>SEMUA WILAYAH OFF AIR</span>
        </span>
      @endif
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

  <!-- CARD 1: PENGATURAN KONEKSI GOOGLE YOUTUBE API KEY -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-lg">
          <i class="ri-key-2-line"></i>
        </div>
        <div>
          <h2 class="text-sm font-bold text-slate-900">Koneksi YouTube Data API v3 (Opsional)</h2>
          <p class="text-[11px] text-slate-500">API Key resmi dari Google Cloud Console untuk mendeteksi status live streaming setiap wilayah secara instan dan akurat.</p>
        </div>
      </div>
      <span class="text-[10px] px-2.5 py-1 rounded-full font-bold {{ !empty($profile->youtube_api_key) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
        {{ !empty($profile->youtube_api_key) ? '✓ API Key Aktif' : 'Mode Probe URL Aktif' }}
      </span>
    </div>

    <form action="{{ route('admin.stream.api-key') }}" method="POST" class="space-y-3">
      @csrf
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
        <div class="sm:col-span-3">
          <label class="block text-xs font-bold text-slate-700 mb-1">Google YouTube Data API Key</label>
          <input type="password" name="youtube_api_key" value="{{ old('youtube_api_key', $profile->youtube_api_key) }}" placeholder="AIzaSy..."
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono text-slate-800 focus:outline-none focus:border-blue-600 bg-white">
          <p class="text-[10px] text-slate-500 mt-1.5 leading-relaxed">
            <span class="font-bold text-slate-700">💡 Cukup 1 API Key untuk semua wilayah:</span> 1 API Key Google berfungsi sebagai pembaca data publik dan dapat memeriksa kelima akun YouTube wilayah yang berbeda sekaligus. Yang membedakan tiap wilayah adalah kolom <strong>"Akun / Channel YouTube Wilayah"</strong> pada kartu masing-masing di bawah. Jika dikosongkan, sistem tetap berjalan otomatis menggunakan mode <strong>Web Probe URL</strong>.
          </p>
        </div>
        <div class="flex items-end">
          <button type="submit"
            class="w-full px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs">
            Simpan API Key
          </button>
        </div>
      </div>
    </form>
  </div>

  <!-- Helper Notice -->
  <div class="p-4 bg-amber-50/80 border border-amber-200 rounded-2xl flex items-start gap-3 text-xs text-amber-900">
    <i class="ri-information-line text-lg text-amber-700 shrink-0 mt-0.5"></i>
    <div class="space-y-1">
      <p class="font-bold">Cara Kerja Otomatisasi Siaran Langsung (Auto-Detect):</p>
      <p class="text-[11px] leading-relaxed text-amber-800">
        Masukkan Handle YouTube (misal: <code>@giasurabaya</code> atau <code>@giadarmocabang</code>) atau Channel ID pada masing-masing cabang. Sistem akan secara otomatis mendeteksi ketika ibadah online dimulai. Anda juga dapat menekan tombol <strong>"Cek Status Streaming Sekarang"</strong> kapan saja untuk sinkronisasi seketika, atau mengatur status siaran secara manual melalui tombol toggle <strong>ON / OFF</strong>.
      </p>
    </div>
  </div>

  <!-- Streams Grid per Wilayah -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach ($streams as $stream)
      <div class="bg-white rounded-2xl border {{ $stream->is_live ? 'border-red-400 ring-2 ring-red-100 shadow-md' : 'border-slate-200 shadow-sm' }} p-5 space-y-4 flex flex-col justify-between transition">
        
        <div>
          <!-- Wilayah Title & Fast Actions -->
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-xl {{ $stream->is_live ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-700' }} flex items-center justify-center font-bold text-xs">
                {{ substr($stream->cabang->nama_cabang ?? 'W', 0, 1) }}
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-sm">Wilayah {{ $stream->cabang->nama_cabang ?? 'Wilayah' }}</h3>
                <span class="text-[10px] text-slate-400">{{ $stream->cabang->lokasi ?? 'Surabaya' }}</span>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <!-- Single Branch Auto-Detect -->
              <form action="{{ route('admin.stream.auto-detect.single', $stream->id) }}" method="POST">
                @csrf
                <button type="submit" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs transition" title="Cek status live wilayah ini sekarang">
                  <i class="ri-refresh-line"></i>
                </button>
              </form>

              <!-- Fast Toggle Form -->
              <form action="{{ route('admin.stream.toggle', $stream->id) }}" method="POST">
                @csrf
                <button type="submit"
                  class="px-3 py-1 rounded-full text-[11px] font-bold inline-flex items-center gap-1.5 transition {{ $stream->is_live ? 'bg-red-600 hover:bg-red-700 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}"
                  title="Klik untuk ubah status siaran secara manual">
                  @if ($stream->is_live)
                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                    <span>LIVE (ON)</span>
                  @else
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    <span>OFF AIR</span>
                  @endif
                </button>
              </form>
            </div>
          </div>

          <!-- Edit Form -->
          <form id="streamForm-{{ $stream->id }}" action="{{ route('admin.stream.update', $stream->id) }}" method="POST" class="space-y-3 pt-3">
            @csrf
            @method('PUT')

            <!-- Target Akun / Channel YouTube Wilayah -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                <i class="ri-youtube-line text-red-600"></i> Akun / Channel YouTube Wilayah *
              </label>
              <input type="text" name="channel_id" value="{{ old('channel_id', $stream->channel_id ?: '@giasurabaya') }}" required
                placeholder="@namawilayah atau UCxxxx..."
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-blue-600 bg-white">
              <span class="text-[10px] text-slate-400">Handle YouTube (contoh: <code>@giasurabaya</code>) atau Channel ID.</span>
            </div>

            <!-- Judul Streaming -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Judul Siaran Ibadah *</label>
              <input type="text" name="judul_streaming" value="{{ old('judul_streaming', $stream->judul_streaming) }}" required
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-blue-600 bg-white">
            </div>

            <!-- Video ID / Embed URL Fallback -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ID Video YouTube / URL Siaran</label>
              <input type="text" name="youtube_id" value="{{ old('youtube_id', $stream->youtube_id) }}"
                placeholder="Contoh: dQw4w9WgXcQ atau https://youtu.be/..."
                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-blue-600 bg-white font-mono">
              <span class="text-[10px] text-slate-400">Otomatis terisi saat siaran live terdeteksi atau masukkan manual jika offline.</span>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Jadwal Tayang Ibadah</label>
                <input type="text" name="waktu_tayang" value="{{ old('waktu_tayang', $stream->waktu_tayang) }}"
                  placeholder="Minggu, 09:00 WIB"
                  class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-blue-600 bg-white">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Auto-Detect</label>
                <select name="auto_detect" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-blue-600 bg-white">
                  <option value="1" {{ $stream->auto_detect ? 'selected' : '' }}>Aktif (Otomatis)</option>
                  <option value="0" {{ !$stream->auto_detect ? 'selected' : '' }}>Manual Saja</option>
                </select>
              </div>
            </div>

            @if($stream->last_checked_at)
              <div class="text-[10px] text-slate-400 pt-1">
                <i class="ri-time-line"></i> Terakhir dicek: {{ \Carbon\Carbon::parse($stream->last_checked_at)->translatedFormat('d M Y, H:i') }} WIB
              </div>
            @endif
          </form>
        </div>

        <!-- Footer Actions -->
        <div class="pt-3 border-t border-slate-100 flex items-center justify-between mt-2">
          @if ($stream->youtube_embed_url)
            <a href="{{ $stream->youtube_embed_url }}" target="_blank"
              class="text-xs text-red-600 hover:underline inline-flex items-center gap-1 font-semibold">
              <i class="ri-youtube-line"></i> Test Player
            </a>
          @else
            <span class="text-xs text-slate-400">Player belum disetel</span>
          @endif

          <button type="submit" form="streamForm-{{ $stream->id }}"
            class="px-4 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs">
            Simpan Wilayah
          </button>
        </div>

      </div>
    @endforeach
  </div>

</div>
@endsection
