@extends('admin.layoutCMS')

@section('content')
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold mb-1.5 border border-amber-200/80">
        <i class="ri-newspaper-line"></i> Publikasi & Renungan Jemaat
      </div>
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kelola Artikel</h1>
      <p class="text-xs text-slate-500 mt-0.5">Tulis renungan firman, kabar pelayanan, dan artikel rohani yang tampil di situs web gereja.</p>
    </div>
    <a href="{{ route('admin.article.create') }}" class="btn-cms-primary inline-flex items-center gap-2">
      <i class="ri-add-line text-base"></i> Tulis Artikel Baru
    </a>
  </div>

  @if (Session::has('success') || session('success'))
    <div id="success-alert"
      class="bg-emerald-500 p-4 rounded-xl mb-6 text-sm font-semibold text-white shadow-md transition-opacity duration-500 flex items-center gap-2">
      <span>✅</span>
      <span>{{ Session::get('success') ?? session('success') }}</span>
    </div>
  @endif

  <!-- Metrics Grid -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-medium text-slate-500">Total Artikel</span>
        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
          <i class="ri-article-line"></i>
        </div>
      </div>
      <div class="text-2xl font-black text-slate-900 mt-2">{{ number_format($stats['total']) }}</div>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-medium text-slate-500">Dipublikasikan</span>
        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
          <i class="ri-checkbox-circle-line"></i>
        </div>
      </div>
      <div class="text-2xl font-black text-emerald-600 mt-2">{{ number_format($stats['published']) }}</div>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-medium text-slate-500">Draft Disimpan</span>
        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
          <i class="ri-draft-line"></i>
        </div>
      </div>
      <div class="text-2xl font-black text-amber-600 mt-2">{{ number_format($stats['draft']) }}</div>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-medium text-slate-500">Total Interaksi Komentar</span>
        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
          <i class="ri-chat-3-line"></i>
        </div>
      </div>
      <div class="text-2xl font-black text-purple-600 mt-2">{{ number_format($stats['total_comments']) }}</div>
    </div>
  </div>

  <!-- Filter & Search Toolbar -->
  <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs mb-6">
    <form action="{{ route('admin.article.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 items-center justify-between">
      <div class="flex-1 w-full md:max-w-md relative">
        <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input type="text" name="search" value="{{ request('search') }}"
          placeholder="Cari judul artikel, topik, atau penulis..."
          class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-amber-600 bg-slate-50/50">
      </div>

      <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
        <select name="kategori" onchange="this.form.submit()"
          class="px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-amber-600 bg-white text-slate-700">
          <option value="">Semua Kategori</option>
          @foreach($kategoriList as $kat)
            <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
          @endforeach
        </select>

        <select name="status" onchange="this.form.submit()"
          class="px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-amber-600 bg-white text-slate-700">
          <option value="">Semua Status</option>
          <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
          <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
        </select>

        @if(request()->hasAny(['search', 'kategori', 'status']))
          <a href="{{ route('admin.article.index') }}" class="px-3 py-2 rounded-xl text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold transition">
            Reset
          </a>
        @endif
      </div>
    </form>
  </div>

  <!-- Articles Table -->
  <div class="table-modern-wrapper mb-6">
    <div class="overflow-x-auto">
      <table class="min-w-full">
        <thead>
          <tr>
            <th>Artikel</th>
            <th>Kategori</th>
            <th>Penulis</th>
            <th>Tanggal Rilis</th>
            <th>Interaksi</th>
            <th>Status</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($articles as $article)
            <tr class="hover:bg-slate-50/70 transition-colors">
              <td class="px-6 py-4 text-sm font-semibold text-slate-900">
                <div class="flex items-center gap-3">
                  <img
                    src="{{ $article->banner_url }}"
                    class="w-16 h-12 object-cover rounded-xl border border-slate-200 shadow-2xs bg-slate-100 shrink-0" alt="Banner"
                    onerror="this.onerror=null;this.src='{{ asset('homepage/assets/images/banner/banner-image1.jpg') }}';">
                  <div class="min-w-0">
                    <a href="{{ route('admin.article.edit', $article->id) }}" class="font-bold text-slate-900 hover:text-amber-700 line-clamp-1 transition">
                      {{ $article->judul }}
                    </a>
                    <span class="text-[11px] text-slate-400 font-mono block mt-0.5">/artikel/{{ $article->slug }}</span>
                  </div>
                </div>
              </td>

              <td class="px-6 py-4 text-xs text-slate-600 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200/60">
                  {{ $article->kategori }}
                </span>
              </td>

              <td class="px-6 py-4 text-xs text-slate-700 whitespace-nowrap font-medium">
                <i class="ri-user-3-line text-slate-400 me-1"></i>
                {{ $article->author_name ?: ($article->author->name ?? 'Admin') }}
              </td>

              <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">
                {{ $article->formatted_date }}
              </td>

              <td class="px-6 py-4 text-xs text-slate-600 whitespace-nowrap">
                <div class="flex items-center gap-3">
                  <span class="inline-flex items-center gap-1 text-slate-500" title="Dibaca {{ $article->views_count }} kali">
                    <i class="ri-eye-line text-slate-400"></i> {{ $article->views_count }}
                  </span>
                  <span class="inline-flex items-center gap-1 text-rose-600 font-semibold" title="{{ $article->likes->count() }} Disukai">
                    <i class="ri-heart-fill"></i> {{ $article->likes->count() }}
                  </span>
                  <span class="inline-flex items-center gap-1 text-blue-600 font-semibold" title="{{ $article->comments->count() }} Komentar">
                    <i class="ri-chat-3-line"></i> {{ $article->comments->count() }}
                  </span>
                </div>
              </td>

              <td class="px-6 py-4 text-xs whitespace-nowrap">
                <form action="{{ route('admin.article.toggle-status', $article->id) }}" method="POST">
                  @csrf
                  <button type="submit"
                    class="px-2.5 py-1 rounded-full text-[11px] font-bold inline-flex items-center gap-1 transition {{ $article->status === 'published' ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                    title="Klik untuk ubah status publikasi">
                    <span class="w-1.5 h-1.5 rounded-full {{ $article->status === 'published' ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>
                    <span>{{ strtoupper($article->status) }}</span>
                  </button>
                </form>
              </td>

              <td class="px-6 py-4 text-center whitespace-nowrap">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('articles.show', $article->slug) }}" target="_blank"
                    class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition" title="Lihat di Web">
                    <i class="ri-external-link-line text-base"></i>
                  </a>

                  <a href="{{ route('admin.article.edit', $article->id) }}"
                    class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition" title="Edit Artikel">
                    <i class="ri-pencil-line text-base"></i>
                  </a>

                  <button type="button"
                    onclick="if(confirm('Yakin ingin menghapus artikel ini?')) document.getElementById('delete-form-{{ $article->id }}').submit();"
                    class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 transition" title="Hapus Artikel">
                    <i class="ri-delete-bin-line text-base"></i>
                  </button>

                  <form id="delete-form-{{ $article->id }}" action="{{ route('admin.article.destroy', $article->id) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                  <i class="ri-inbox-line"></i>
                </div>
                Belum ada artikel yang ditambahkan.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($articles->hasPages())
      <div class="p-4 border-t border-slate-100">
        {{ $articles->links() }}
      </div>
    @endif
  </div>
@endsection

