@extends('homepage.layout')

@section('konten')
  <!-- ARTICLE HEADER HERO -->
  <section style="background: linear-gradient(rgba(28, 23, 20, 0.85), rgba(28, 23, 20, 0.92)), url('{{ $article->banner_url }}') center/cover; padding: 100px 0 60px;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-9 text-center">
          <!-- Breadcrumb -->
          <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb justify-content-center mb-0" style="font-size: 12.5px;">
              <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white-50 text-decoration-none">Beranda</a></li>
              <li class="breadcrumb-item"><a href="{{ route('articles.index') }}" class="text-white-50 text-decoration-none">Artikel</a></li>
              <li class="breadcrumb-item active text-white" aria-current="page">{{ $article->kategori }}</li>
            </ol>
          </nav>

          <span class="badge px-3 py-1.5 rounded-pill fw-bold mb-3"
            style="background: rgba(212, 163, 115, 0.25); color: #F5EFEB; border: 1px solid rgba(212, 163, 115, 0.45); font-size: 11.5px; letter-spacing: 0.8px;">
            {{ $article->kategori }}
          </span>

          <h1 class="text-white font-serif fw-bold mb-4" style="font-size: clamp(26px, 4vw, 42px); line-height: 1.25;">
            {{ $article->judul }}
          </h1>

          <div class="d-flex align-items-center justify-content-center flex-wrap gap-3 text-white-50" style="font-size: 13px;">
            <div class="d-flex align-items-center gap-2">
              <i class="ri-user-3-line" style="color: var(--earth-accent);"></i>
              <span class="text-white">{{ $article->author_name ?: 'Admin Pelayanan' }}</span>
            </div>
            <span>•</span>
            <div><i class="ri-calendar-line me-1" style="color: var(--earth-accent);"></i> {{ $article->formatted_date }}</div>
            <span>•</span>
            <div><i class="ri-time-line me-1" style="color: var(--earth-accent);"></i> {{ $article->reading_time }}</div>
            <span>•</span>
            <div><i class="ri-eye-line me-1" style="color: var(--earth-accent);"></i> {{ number_format($article->views_count) }}x dibaca</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- MAIN ARTICLE BODY -->
  <section class="section-gap" style="background: var(--earth-surface-warm);">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-9">
          
          <div class="card-earth p-4 p-md-5 mb-5 shadow-sm" style="background: #FFFFFF; border-radius: 24px; border: 1px solid rgba(140, 94, 60, 0.15);">
            
            <!-- Banner Image -->
            <div class="mb-4 rounded-4 overflow-hidden shadow-xs" style="max-height: 480px;">
              <img src="{{ $article->banner_url }}" alt="{{ $article->judul }}" class="w-100 h-100 object-fit-cover" style="max-height: 480px;">
            </div>

            <!-- Excerpt Callout -->
            @if($article->ringkasan)
              <div class="p-3.5 p-md-4 mb-4 rounded-3 border-start border-4" style="background: var(--earth-surface-warm); border-color: var(--earth-primary) !important; font-style: italic; color: var(--earth-dark); font-size: 15px; line-height: 1.7;">
                "{{ $article->ringkasan }}"
              </div>
            @endif

            <!-- Konten Lengkap -->
            <div class="article-content font-sans" style="font-size: 16px; line-height: 1.85; color: #372F2A;">
              {!! nl2br(e($article->konten)) !!}
            </div>

            <!-- INTERAKSI: LIKE & SHARE -->
            <div class="pt-4 mt-5 border-top d-flex align-items-center justify-content-between flex-wrap gap-3" style="border-color: rgba(140, 94, 60, 0.15) !important;">
              
              <!-- Like Button -->
              <div class="d-flex align-items-center gap-2">
                <button type="button" id="likeButton" onclick="handleLike()"
                  class="btn rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 fw-bold transition shadow-xs {{ $isLiked ? 'btn-danger' : 'btn-outline-danger' }}"
                  style="font-size: 14px;">
                  <i id="likeIcon" class="{{ $isLiked ? 'ri-heart-fill' : 'ri-heart-line' }} text-lg"></i>
                  <span id="likeText">{{ $isLiked ? 'Disukai' : 'Suka Artikel' }}</span>
                  <span class="badge bg-white text-danger rounded-pill px-2 py-0.5 ms-1" id="likeCount">{{ $article->likes->count() }}</span>
                </button>
                <span class="text-muted small d-none d-sm-inline" style="font-size: 12px;">Sukai artikel ini jika memberkati Anda</span>
              </div>

              <!-- Share Buttons -->
              <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-semibold" style="font-size: 12px;">Bagikan:</span>
                <a href="https://api.whatsapp.com/send?text={{ urlencode($article->judul . ' - ' . url()->current()) }}" target="_blank"
                  class="btn btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center text-white"
                  style="background: #25D366; width: 34px; height: 34px;" title="Bagikan via WhatsApp">
                  <i class="ri-whatsapp-line"></i>
                </a>
                <button type="button" onclick="navigator.clipboard.writeText(window.location.href); alert('Link artikel berhasil disalin!');"
                  class="btn btn-sm btn-earth-light rounded-circle p-2 d-flex align-items-center justify-content-center"
                  style="width: 34px; height: 34px;" title="Salin Link Artikel">
                  <i class="ri-links-line"></i>
                </button>
              </div>

            </div>

          </div>

          <!-- SECTION KOMENTAR JEMAAT -->
          <div class="card-earth p-4 p-md-5 mb-5 shadow-sm" id="komentar-section" style="background: #FFFFFF; border-radius: 24px; border: 1px solid rgba(140, 94, 60, 0.15);">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom" style="border-color: rgba(140, 94, 60, 0.12) !important;">
              <div>
                <h3 class="font-serif fw-bold mb-1" style="color: var(--earth-dark); font-size: 22px;">
                  Komentar & Refleksi Jemaat
                </h3>
                <p class="text-muted small mb-0">Bagikan berkat, refleksi firman, atau doa yang menguatkan sesama.</p>
              </div>
              <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: var(--earth-accent-soft); color: var(--earth-primary); font-size: 13px;" id="totalCommentCount">
                {{ $article->comments->count() }} Komentar
              </span>
            </div>

            <!-- Form Tulis Komentar -->
            @auth
              <form action="{{ route('articles.comment', $article->id) }}" method="POST" id="commentForm" class="mb-5">
                @csrf
                <div class="d-flex gap-3">
                  <img src="{{ Auth::user()->avatar_url }}" class="rounded-circle shrink-0" style="width: 44px; height: 44px; object-fit: cover;" alt="Avatar">
                  <div class="flex-grow-1">
                    <div class="fw-bold mb-1" style="font-size: 13px; color: var(--earth-dark);">
                      {{ Auth::user()->name ?: Auth::user()->username }} <span class="badge bg-light text-muted ms-1" style="font-size: 10px;">Jemaat</span>
                    </div>
                    <textarea name="komentar" id="komentarInput" rows="3" required
                      placeholder="Tuliskan refleksi, kesan, atau doa Anda mengenai artikel ini..."
                      class="form-control mb-2 rounded-3 p-3" style="font-size: 13.5px; border-color: rgba(140, 94, 60, 0.25);"></textarea>
                    
                    <div class="d-flex justify-content-end">
                      <button type="submit" class="btn btn-earth px-4 py-2 rounded-pill fw-bold d-inline-flex align-items-center gap-1.5" style="font-size: 13px;">
                        <i class="ri-send-plane-fill"></i> Kirim Komentar
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            @else
              <!-- Ajakan Login untuk Berkomentar -->
              <div class="p-4 rounded-4 mb-5 text-center" style="background: var(--earth-surface-warm); border: 1px dashed rgba(140, 94, 60, 0.25);">
                <i class="ri-chat-voice-line" style="font-size: 32px; color: var(--earth-primary);"></i>
                <h5 class="font-serif fw-bold mt-2 mb-1" style="color: var(--earth-dark);">Ingin Ikut Berdiskusi atau Berkomentar?</h5>
                <p class="text-muted small mb-3">Silakan masuk menggunakan akun jemaat Anda untuk dapat menuliskan komentar dan menyukai postingan.</p>
                <div class="d-flex justify-content-center gap-2">
                  <a href="{{ route('jemaat.login', ['redirect' => url()->current() . '#komentar-section']) }}" class="btn btn-earth px-4 py-2 rounded-pill fw-bold" style="font-size: 13px;">
                    <i class="ri-login-box-line me-1"></i> Masuk Jemaat
                  </a>
                  <a href="{{ route('jemaat.register', ['redirect' => url()->current() . '#komentar-section']) }}" class="btn btn-earth-outline px-4 py-2 rounded-pill fw-bold" style="font-size: 13px;">
                    Daftar Akun Baru
                  </a>
                </div>
              </div>
            @endauth

            <!-- Daftar Komentar -->
            <div class="comments-list space-y-4" id="commentsContainer">
              @forelse($article->comments as $comment)
                <div class="p-3.5 rounded-3 mb-3" style="background: var(--earth-surface-warm);">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2.5">
                      <img src="{{ $comment->user->avatar_url ?? 'https://ui-avatars.com/api/?name=Jemaat' }}"
                        class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;" alt="Avatar">
                      <div>
                        <div class="fw-bold" style="font-size: 13px; color: var(--earth-dark);">
                          {{ $comment->user->name ?? $comment->user->username ?? 'Jemaat' }}
                        </div>
                        <span class="text-muted" style="font-size: 11px;">{{ $comment->formatted_date }}</span>
                      </div>
                    </div>
                  </div>
                  <p class="mb-0 text-secondary" style="font-size: 13.5px; line-height: 1.6; padding-left: 46px;">
                    {!! nl2br(e($comment->komentar)) !!}
                  </p>
                </div>
              @empty
                <div class="text-center py-4 text-muted" id="emptyCommentsPlaceholder">
                  <i class="ri-chat-smile-2-line text-2xl d-block mb-1"></i>
                  <p class="small mb-0">Belum ada komentar. Jadilah yang pertama memberikan refleksi!</p>
                </div>
              @endforelse
            </div>

          </div>

          <!-- ARTIKEL TERKAIT -->
          @if($relatedArticles->count() > 0)
            <div class="mt-5">
              <h4 class="font-serif fw-bold mb-4" style="color: var(--earth-dark);">Artikel & Renungan Lainnya</h4>
              <div class="row g-4">
                @foreach($relatedArticles as $rel)
                  <div class="col-md-4">
                    <div class="card-earth h-100 p-0 overflow-hidden shadow-xs" style="background: #FFFFFF; border-radius: 18px;">
                      <img src="{{ $rel->banner_url }}" class="w-100" style="height: 140px; object-fit: cover;" alt="{{ $rel->judul }}">
                      <div class="p-3">
                        <span class="badge mb-1.5" style="background: var(--earth-accent-soft); color: var(--earth-primary); font-size: 10px;">{{ $rel->kategori }}</span>
                        <h6 class="font-serif fw-bold mb-1 line-clamp-2" style="font-size: 14px;">
                          <a href="{{ route('articles.show', $rel->slug) }}" class="text-decoration-none" style="color: var(--earth-dark);">
                            {{ $rel->judul }}
                          </a>
                        </h6>
                        <span class="text-muted small" style="font-size: 11px;">{{ $rel->formatted_date }}</span>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endif

        </div>
      </div>
    </div>
  </section>

  <!-- JAVASCRIPT AJAX LIKE & KOMENTAR -->
  <script>
    function handleLike() {
      @guest
        if(confirm('Silakan masuk sebagai jemaat terlebih dahulu untuk menyukai artikel ini. Buka halaman login?')) {
          window.location.href = "{{ route('jemaat.login', ['redirect' => url()->current()]) }}";
        }
        return;
      @endguest

      const btn = document.getElementById('likeButton');
      const icon = document.getElementById('likeIcon');
      const text = document.getElementById('likeText');
      const count = document.getElementById('likeCount');

      fetch("{{ route('articles.like', $article->id) }}", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": "{{ csrf_token() }}",
          "Accept": "application/json"
        }
      })
      .then(res => res.json())
      .then(data => {
        if(data.success) {
          count.textContent = data.likes_count;
          if(data.liked) {
            btn.classList.remove('btn-outline-danger');
            btn.classList.add('btn-danger');
            icon.classList.remove('ri-heart-line');
            icon.classList.add('ri-heart-fill');
            text.textContent = 'Disukai';
          } else {
            btn.classList.remove('btn-danger');
            btn.classList.add('btn-outline-danger');
            icon.classList.remove('ri-heart-fill');
            icon.classList.add('ri-heart-line');
            text.textContent = 'Suka Artikel';
          }
        } else if(data.auth_required) {
          window.location.href = data.redirect_url;
        }
      })
      .catch(err => console.error(err));
    }
  </script>
@endsection
