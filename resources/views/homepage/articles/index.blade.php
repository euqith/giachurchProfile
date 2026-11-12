@extends('homepage.layout')

@section('konten')
  <!-- HERO HEADER -->
  <section class="page-title-area text-center" style="background: linear-gradient(rgba(28, 23, 20, 0.78), rgba(28, 23, 20, 0.88)), url('{{ asset('homepage/assets/images/banner/banner-image1.jpg') }}') center/cover; padding: 110px 0 70px;">
    <div class="container">
      <span class="badge px-3 py-1.5 rounded-pill text-uppercase d-inline-flex align-items-center gap-1.5 mb-3"
        style="background: rgba(212, 163, 115, 0.22); color: #F5EFEB; border: 1px solid rgba(212, 163, 115, 0.45); font-size: 11.5px; letter-spacing: 0.8px;">
        <i class="ri-newspaper-line" style="color: var(--earth-accent);"></i> Kabar & Pengajaran Rohani
      </span>
      <h1 class="text-white font-serif fw-bold mb-3" style="font-size: clamp(28px, 4.5vw, 44px); letter-spacing: -0.01em;">
        Artikel & Renungan Firman
      </h1>
      <p class="mx-auto fw-normal leading-relaxed mb-0" style="color: #E6DDD4; font-size: clamp(14px, 2vw, 16.5px); max-width: 680px;">
        Mendalami firman Tuhan, bertumbuh bersama dalam iman, dan membangun kehidupan jemaat yang berakar serta berbuah dalam kasih.
      </p>
    </div>
  </section>

  <!-- CONTENT SECTION -->
  <section class="section-gap" style="background: var(--earth-surface-warm); min-height: 60vh;">
    <div class="container">
      
      <!-- Filter Bar -->
      <div class="card-earth p-3 p-md-3.5 mb-5 shadow-xs" style="background: #FFFFFF; border-radius: 18px;">
        <form action="{{ route('articles.index') }}" method="GET" class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between gap-3">
          
          <!-- Category Pills -->
          <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('articles.index', request()->only('q')) }}"
              class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold transition {{ !request('kategori') ? 'btn-earth' : 'btn-earth-light' }}"
              style="font-size: 12.5px;">
              Semua
            </a>
            @foreach($kategoriList as $kat)
              <a href="{{ route('articles.index', array_merge(request()->only('q'), ['kategori' => $kat])) }}"
                class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold transition {{ request('kategori') === $kat ? 'btn-earth' : 'btn-earth-light' }}"
                style="font-size: 12.5px;">
                {{ $kat }}
              </a>
            @endforeach
          </div>

          <!-- Search Input -->
          <div class="input-group" style="max-width: 320px;">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari topik atau kata kunci..."
              class="form-control rounded-start-pill border-end-0 px-3 py-2"
              style="font-size: 13px; border-color: rgba(140, 94, 60, 0.25);">
            <button class="btn btn-earth rounded-end-pill px-3.5" type="submit">
              <i class="ri-search-line"></i>
            </button>
          </div>
        </form>
      </div>

      <!-- Articles Grid -->
      <div class="row g-4 mb-5">
        @forelse($articles as $article)
          <div class="col-lg-4 col-md-6">
            <div class="card-earth h-100 p-0 overflow-hidden shadow-sm d-flex flex-column justify-content-between transition-hover"
              style="background: #FFFFFF; border: 1px solid rgba(140, 94, 60, 0.16); border-radius: 20px;">
              <div>
                <!-- Banner Image -->
                <div style="position: relative; overflow: hidden; height: 220px;">
                  <img src="{{ $article->banner_url }}" alt="{{ $article->judul }}"
                    style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;"
                    onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                  <span class="badge position-absolute top-0 start-0 m-3 px-3 py-1.5 rounded-pill fw-bold"
                    style="background: rgba(28, 23, 20, 0.8); color: #F5EFEB; backdrop-filter: blur(4px); font-size: 11px;">
                    {{ $article->kategori }}
                  </span>
                </div>

                <div class="p-3.5 p-md-4">
                  <!-- Meta -->
                  <div class="d-flex align-items-center justify-content-between text-muted small mb-2.5" style="font-size: 12px;">
                    <span><i class="ri-calendar-line me-1" style="color: var(--earth-primary);"></i> {{ $article->formatted_date }}</span>
                    <span><i class="ri-time-line me-1" style="color: var(--earth-primary);"></i> {{ $article->reading_time }}</span>
                  </div>

                  <!-- Judul -->
                  <h4 class="font-serif fw-bold mb-2.5 line-clamp-2" style="font-size: 18px; line-height: 1.35;">
                    <a href="{{ route('articles.show', $article->slug) }}" class="text-decoration-none" style="color: var(--earth-dark);">
                      {{ $article->judul }}
                    </a>
                  </h4>

                  <!-- Excerpt -->
                  <p class="small mb-0 line-clamp-3" style="color: var(--earth-text-muted); font-size: 13.5px; line-height: 1.6;">
                    {{ $article->ringkasan }}
                  </p>
                </div>
              </div>

              <!-- Footer info -->
              <div class="px-3.5 px-md-4 py-3 border-top d-flex align-items-center justify-content-between mt-auto" style="border-color: rgba(140, 94, 60, 0.12) !important;">
                <div class="d-flex align-items-center gap-3" style="font-size: 12px; color: var(--earth-text-muted);">
                  <span title="{{ $article->likes->count() }} disukai"><i class="ri-heart-line text-danger me-1"></i> {{ $article->likes->count() }}</span>
                  <span title="{{ $article->comments->count() }} komentar"><i class="ri-chat-3-line text-primary me-1"></i> {{ $article->comments->count() }}</span>
                </div>

                <a href="{{ route('articles.show', $article->slug) }}" class="fw-bold small text-decoration-none d-inline-flex align-items-center gap-1" style="color: var(--earth-primary); font-size: 13px;">
                  <span>Baca Selengkapnya</span>
                  <i class="ri-arrow-right-s-line"></i>
                </a>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12 text-center py-5">
            <div class="p-5 rounded-4 mx-auto" style="max-width: 500px; background: #FFFFFF; border: 1px dashed rgba(140, 94, 60, 0.25);">
              <i class="ri-book-open-line" style="font-size: 42px; color: var(--earth-primary);"></i>
              <h4 class="font-serif fw-bold mt-3 mb-1" style="color: var(--earth-dark);">Tidak Menemukan Artikel</h4>
              <p class="text-muted small mb-4">Coba cari dengan kata kunci lain atau pilih kategori yang tersedia.</p>
              <a href="{{ route('articles.index') }}" class="btn btn-earth px-4 py-2 rounded-pill fw-bold" style="font-size: 13px;">
                Reset Pencarian
              </a>
            </div>
          </div>
        @endforelse
      </div>

      <!-- Pagination -->
      @if($articles->hasPages())
        <div class="d-flex justify-content-center">
          {{ $articles->links() }}
        </div>
      @endif

    </div>
  </section>
@endsection
