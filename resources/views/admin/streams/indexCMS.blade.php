@extends('admin.layoutCMS')

@section('content')
<div class="space-y-6 max-w-5xl">

  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-red-700 text-xs font-semibold mb-1 border border-red-200">
        <i class="ri-broadcast-line"></i> Kanal Ibadah Online
      </div>
      <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kelola Live Streaming</h1>
      <p class="text-xs text-slate-500 mt-0.5">Atur tautan YouTube dan status siaran langsung untuk setiap cabang gereja.</p>
    </div>

    <!-- Global Status Indicator -->
    <div class="flex items-center gap-2">
      @if ($anyLive)
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-red-600 text-white text-xs font-bold shadow-md shadow-red-600/30 animate-pulse">
          <span class="w-2.5 h-2.5 rounded-full bg-white"></span>
          <span>SEDANG SIARAN LANGSUNG (ON AIR)</span>
        </span>
      @else
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold border border-slate-200">
          <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
          <span>SEMUA CABANG OFF AIR (TIDAK ADA STREAMING)</span>
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

  <!-- Helper Notice -->
  <div class="p-4 bg-amber-50/80 border border-amber-200 rounded-2xl flex items-start gap-3 text-xs text-amber-900">
    <i class="ri-information-line text-lg text-amber-700 shrink-0 mt-0.5"></i>
    <div class="space-y-1">
      <p class="font-bold">Cara Kerja Deteksi Streaming di Halaman Utama:</p>
      <p class="text-[11px] leading-relaxed text-amber-800">
        Jika ada minimal satu cabang yang disetel <strong>"Live (ON AIR)"</strong>, pemutar siaran langsung akan otomatis muncul di section streaming beranda.
        Sebaliknya, jika seluruh cabang dalam status <strong>"OFF"</strong>, sistem akan otomatis menampilkan pemberitahuan ramah kepada jemaat bahwa saat ini tidak ada siaran langsung yang sedang berlangsung.
      </p>
    </div>
  </div>

  <!-- Streams Grid per Cabang -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach ($streams as $stream)
      <div class="bg-white rounded-2xl border {{ $stream->is_live ? 'border-red-400 ring-2 ring-red-100 shadow-md' : 'border-slate-200 shadow-sm' }} p-5 space-y-4 flex flex-col justify-between transition">
        
        <div>
          <!-- Cabang Title & Status Badge -->
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-xl {{ $stream->is_live ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-700' }} flex items-center justify-center font-bold text-xs">
                {{ substr($stream->cabang->nama_cabang ?? 'C', 0, 1) }}
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-sm">Cabang {{ $stream->cabang->nama_cabang ?? 'Cabang' }}</h3>
                <span class="text-[10px] text-slate-400">{{ $stream->cabang->lokasi ?? 'Surabaya' }}</span>
              </div>
            </div>

            <!-- Fast Toggle Form -->
            <form action="{{ route('admin.stream.toggle', $stream->id) }}" method="POST">
              @csrf
              <button type="submit"
                class="px-3 py-1 rounded-full text-[11px] font-bold inline-flex items-center gap-1.5 transition {{ $stream->is_live ? 'bg-red-600 hover:bg-red-700 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}"
                title="Klik untuk ubah status siaran">
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

          <!-- Edit Form -->
          <form id="streamForm-{{ $stream->id }}" action="{{ route('admin.stream.update', $stream->id) }}" method="POST" class="mt-4 space-y-3">
            @csrf
            @method('PUT')

            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-1">Judul Streaming</label>
              <input type="text" name="judul_streaming" value="{{ old('judul_streaming', $stream->judul_streaming) }}" required
                class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-red-500 bg-white">
            </div>

            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-1">YouTube Video ID atau Tautan Penuh</label>
              <input type="text" name="youtube_id" value="{{ old('youtube_id', $stream->youtube_id) }}" placeholder="mis. dQw4w9WgXcQ atau https://youtube.com/watch?v=..."
                class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-red-500 bg-white">
              <span class="text-[10px] text-slate-400 mt-0.5 block">Tempel link live/video YouTube atau kode ID video 11 karakter.</span>
            </div>

            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Jadwal Tayang</label>
                <input type="text" name="waktu_tayang" value="{{ old('waktu_tayang', $stream->waktu_tayang) }}"
                  class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-red-500 bg-white">
              </div>

              <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Status Siaran</label>
                <select name="is_live"
                  class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-red-500 bg-white">
                  <option value="1" {{ $stream->is_live ? 'selected' : '' }}>🔴 ON AIR (Sedang Live)</option>
                  <option value="0" {{ !$stream->is_live ? 'selected' : '' }}>⚪ OFF (Tidak Live)</option>
                </select>
              </div>
            </div>
          </form>
        </div>

        <!-- Footer Actions -->
        <div class="pt-3 border-t border-slate-100 flex items-center justify-between mt-2">
          @if ($stream->youtube_id)
            <a href="{{ $stream->youtube_embed_url }}" target="_blank"
              class="text-xs text-red-600 hover:underline inline-flex items-center gap-1 font-semibold">
              <i class="ri-youtube-line"></i> Test Player
            </a>
          @else
            <span class="text-[11px] text-slate-400 italic">Belum ada video</span>
          @endif

          <button type="submit" form="streamForm-{{ $stream->id }}"
            class="px-4 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs">
            Simpan Cabang
          </button>
        </div>

      </div>
    @endforeach
  </div>

</div>
@endsection
