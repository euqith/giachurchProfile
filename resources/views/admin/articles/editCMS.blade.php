@extends('admin.layoutCMS')

@section('content')
  <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <a href="{{ route('admin.article.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition mb-2">
        <i class="ri-arrow-left-line"></i> Kembali ke Kelola Artikel
      </a>
      <div class="flex items-center gap-2">
        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center font-bold">
          <i class="ri-edit-line text-lg"></i>
        </div>
        <div>
          <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Artikel</h1>
          <p class="text-xs text-slate-500">Perbarui konten artikel, kategori, status, atau moderasi komentar.</p>
        </div>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('articles.show', $article->slug) }}" target="_blank"
        class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition inline-flex items-center gap-1.5">
        <i class="ri-external-link-line"></i> Buka Halaman Publik
      </a>
    </div>
  </div>

  @if (Session::has('success') || session('success'))
    <div id="success-alert"
      class="bg-emerald-500 p-4 rounded-xl mb-6 text-sm font-semibold text-white shadow-md transition-opacity duration-500 flex items-center gap-2">
      <span>✅</span>
      <span>{{ Session::get('success') ?? session('success') }}</span>
    </div>
  @endif

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

  <form action="{{ route('admin.article.update', $article->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Main Content (2 cols) -->
      <div class="lg:col-span-2 space-y-5">
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-2xs space-y-4">
          <!-- Judul Artikel -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Judul Artikel / Renungan *</label>
            <input type="text" name="judul" value="{{ old('judul', $article->judul) }}" required
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-900 focus:outline-none focus:border-amber-600 bg-white">
            <span class="text-[11px] text-slate-400 font-mono mt-1 block">Slug URL: /artikel/{{ $article->slug }}</span>
          </div>

          <!-- Ringkasan Excerpt -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Ringkasan Singkat (Excerpt)</label>
            <textarea name="ringkasan" rows="3"
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-amber-600 bg-white">{{ old('ringkasan', $article->ringkasan) }}</textarea>
          </div>

          <!-- Konten Lengkap -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700">Isi Konten Artikel *</label>
              <span class="text-[11px] text-slate-400">Mendukung format paragraf & baris baru</span>
            </div>
            <textarea name="konten" rows="16" required
              class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:border-amber-600 bg-white leading-relaxed">{{ old('konten', $article->konten) }}</textarea>
          </div>
        </div>

        <!-- Moderasi Komentar Masuk -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-2xs space-y-4">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
              <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i class="ri-chat-3-line text-blue-600"></i> Komentar Jemaat ({{ $article->comments->count() }})
              </h3>
              <p class="text-[11px] text-slate-400">Tinjau atau hapus komentar yang tidak sesuai etika jemaat.</p>
            </div>
          </div>

          <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto space-y-3">
            @forelse($article->comments as $comment)
              <div class="pt-3 flex items-start justify-between gap-3">
                <div class="flex items-start gap-2.5">
                  <img src="{{ $comment->user->avatar_url ?? 'https://ui-avatars.com/api/?name=Jemaat' }}" class="w-8 h-8 rounded-full object-cover shrink-0" alt="Avatar">
                  <div>
                    <div class="flex items-center gap-2">
                      <span class="text-xs font-bold text-slate-900">{{ $comment->user->name ?? $comment->user->username ?? 'Jemaat' }}</span>
                      <span class="text-[10px] text-slate-400">{{ $comment->formatted_date }}</span>
                    </div>
                    <p class="text-xs text-slate-700 mt-1 leading-relaxed">{{ $comment->komentar }}</p>
                  </div>
                </div>

                <button type="button"
                  onclick="if(confirm('Hapus komentar dari jemaat ini?')) document.getElementById('delete-comment-form-{{ $comment->id }}').submit();"
                  class="text-[11px] text-rose-600 hover:text-rose-700 font-semibold shrink-0 p-1 rounded hover:bg-rose-50 transition">
                  <i class="ri-delete-bin-line"></i> Hapus
                </button>
              </div>
            @empty
              <p class="text-xs text-slate-400 py-4 text-center">Belum ada komentar pada artikel ini.</p>
            @endforelse
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
              <option value="published" {{ old('status', $article->status) === 'published' ? 'selected' : '' }}>🟢 Dipublikasikan (Published)</option>
              <option value="draft" {{ old('status', $article->status) === 'draft' ? 'selected' : '' }}>⚪ Simpan Sebagai Draft</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Artikel *</label>
            <select name="kategori" required
              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-amber-600 bg-white text-slate-800">
              @foreach($kategoriList as $kat)
                <option value="{{ $kat }}" {{ old('kategori', $article->kategori) === $kat ? 'selected' : '' }}>{{ $kat }}</option>
              @endforeach
            </select>
          </div>

          <div class="text-[11px] text-slate-500 pt-2 border-t border-slate-100 space-y-1">
            <div><i class="ri-user-line text-slate-400"></i> Penulis: <strong>{{ $article->author_name ?: 'Admin' }}</strong></div>
            <div><i class="ri-eye-line text-slate-400"></i> Dibaca: <strong>{{ number_format($article->views_count) }} kali</strong></div>
            <div><i class="ri-heart-line text-slate-400"></i> Disukai: <strong>{{ number_format($article->likes->count()) }} orang</strong></div>
          </div>

          <div class="pt-3 border-t border-slate-100 space-y-2">
            <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-xs">
              Simpan Perubahan
            </button>
            <a href="{{ route('admin.article.index') }}" class="block text-center w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs transition">
              Kembali
            </a>
          </div>
        </div>

        <!-- Banner Image Upload -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs space-y-4">
          <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
            <i class="ri-image-line text-amber-600"></i> Gambar Banner Artikel
          </h3>

          <div class="space-y-3">
            <div>
              <img id="imagePreview" src="{{ $article->banner_url }}"
                class="w-full h-40 object-cover rounded-xl border border-slate-200 shadow-2xs" alt="Banner Saat Ini">
              <span class="text-[10px] text-slate-400 block mt-1 text-center">Gambar banner saat ini</span>
            </div>

            <div class="border-2 border-dashed border-slate-200 rounded-xl p-3 text-center hover:border-amber-500 transition cursor-pointer" onclick="document.getElementById('gambar_banner_input').click()">
              <p class="text-xs font-bold text-slate-700">Ganti Gambar Banner</p>
              <p class="text-[11px] text-slate-400 mt-0.5">Maksimal 5 MB (JPG, PNG, WEBP)</p>
            </div>

            <input type="file" id="gambar_banner_input" name="gambar_banner" accept="image/*" class="hidden" onchange="previewBanner(this)">
          </div>
        </div>

      </div>

    </div>
  </form>

  <!-- Delete Comment Form Template -->
  @foreach($article->comments as $c)
    <form id="delete-comment-form-{{ $c->id }}" action="{{ route('admin.article.comment.destroy', ['article_id' => $article->id, 'comment_id' => $c->id]) }}" method="POST" class="hidden">
      @csrf
      @method('DELETE')
    </form>
  @endforeach

  <script>
    function previewBanner(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('imagePreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>
@endsection
