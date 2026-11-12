@extends('homepage.layout')

@section('konten')
  <!-- PROFILE HERO HEADER -->
  <section style="background: linear-gradient(rgba(28, 23, 20, 0.88), rgba(28, 23, 20, 0.94)), url('{{ asset('homepage/assets/images/banner/banner-image1.jpg') }}') center/cover; padding: 80px 0 50px;">
    <div class="container">
      <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
        
        <!-- User Info -->
        <div class="d-flex align-items-center gap-3.5 text-center text-md-start">
          <img src="{{ $user->avatar_url }}" alt="Avatar" class="rounded-circle shadow-md border border-2 border-white"
            style="width: 82px; height: 82px; object-fit: cover;">
          <div>
            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 mb-1">
              <h2 class="text-white font-serif fw-bold mb-0" style="font-size: 24px;">{{ $user->name ?: $user->username }}</h2>
              <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(212, 163, 115, 0.25); color: #F5EFEB; font-size: 11px;">
                Jemaat GIA Surabaya
              </span>
            </div>
            <p class="text-white-50 mb-0 font-mono" style="font-size: 13px;">{{ '@' . $user->username }}</p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex align-items-center gap-2">
          <a href="{{ route('articles.index') }}" class="btn btn-earth-light px-3.5 py-2 rounded-pill fw-bold d-inline-flex align-items-center gap-2" style="font-size: 13px;">
            <i class="ri-newspaper-line"></i>
            <span>Jelajahi Artikel</span>
          </a>

          <form action="{{ route('jemaat.logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-light px-3.5 py-2 rounded-pill d-inline-flex align-items-center gap-1.5" style="font-size: 13px;">
              <i class="ri-logout-box-r-line"></i>
              <span>Keluar</span>
            </button>
          </form>
        </div>

      </div>
    </div>
  </section>

  <!-- MAIN PROFILE BODY -->
  <section class="section-gap" style="background: var(--earth-surface-warm); min-height: 70vh;">
    <div class="container">
      
      @if (Session::has('success') || session('success'))
        <div class="alert alert-success p-3 rounded-4 mb-4 text-xs font-semibold shadow-xs flex items-center gap-2">
          <span>✅</span>
          <span>{{ Session::get('success') ?? session('success') }}</span>
        </div>
      @endif

      @if ($errors->any())
        <div class="alert alert-danger p-3 rounded-4 mb-4 text-xs">
          <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="row g-4">
        
        <!-- Left: Form Edit Profil -->
        <div class="col-lg-4">
          <div class="card-earth p-4 shadow-sm" style="background: #FFFFFF; border-radius: 20px; border: 1px solid rgba(140, 94, 60, 0.15);">
            <h4 class="font-serif fw-bold mb-3 pb-2 border-bottom" style="font-size: 17px; color: var(--earth-dark); border-color: rgba(140, 94, 60, 0.12) !important;">
              <i class="ri-user-settings-line me-1" style="color: var(--earth-primary);"></i> Informasi Profil
            </h4>

            <form action="{{ route('jemaat.profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
              @csrf

              <!-- Avatar Change -->
              <div class="text-center mb-3">
                <div class="position-relative d-inline-block">
                  <img id="avatarPreview" src="{{ $user->avatar_url }}" class="rounded-circle border border-2 border-slate-200"
                    style="width: 74px; height: 74px; object-fit: cover;" alt="Avatar">
                  <button type="button" onclick="document.getElementById('avatarInput').click()"
                    class="btn btn-sm btn-earth rounded-circle position-absolute bottom-0 end-0 p-1 d-flex align-items-center justify-content-center shadow-xs"
                    style="width: 26px; height: 26px;" title="Ganti Foto Profil">
                    <i class="ri-camera-line" style="font-size: 12px;"></i>
                  </button>
                </div>
                <input type="file" name="avatar" id="avatarInput" accept="image/*" class="d-none" onchange="previewAvatar(this)">
                <div class="small text-muted mt-1" style="font-size: 11px;">Klik ikon kamera untuk ganti foto</div>
              </div>

              <div class="mb-2.5">
                <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 12px;">Nama Lengkap *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                  class="form-control py-2 text-xs" style="font-size: 13px; border-radius: 10px;">
              </div>

              <div class="mb-2.5">
                <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 12px;">Username *</label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted py-1.5" style="border-radius: 10px 0 0 10px; font-size: 12px;">@</span>
                  <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                    class="form-control py-2 text-xs" style="border-radius: 0 10px 10px 0; font-size: 13px;">
                </div>
              </div>

              <div class="mb-2.5">
                <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 12px;">Nomor Telepon / WhatsApp</label>
                <input type="text" name="telepon" value="{{ old('telepon', $user->telepon) }}" placeholder="Contoh: 08123456789"
                  class="form-control py-2 text-xs" style="font-size: 13px; border-radius: 10px;">
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 12px;">Bio / Catatan Singkat</label>
                <textarea name="bio" rows="2" placeholder="Tuliskan ayat favorit atau bio singkat..."
                  class="form-control py-2 text-xs" style="font-size: 13px; border-radius: 10px;">{{ old('bio', $user->bio) }}</textarea>
              </div>

              <!-- Ganti Password Optional -->
              <div class="pt-2 border-top mb-3" style="border-color: rgba(140, 94, 60, 0.1) !important;">
                <span class="small fw-bold text-secondary d-block mb-2" style="font-size: 11.5px;">Ganti Kata Sandi (Opsional)</span>
                
                <input type="password" name="new_password" placeholder="Kata sandi baru (kosongkan jika tidak ubah)"
                  class="form-control py-1.5 mb-2 text-xs" style="font-size: 12px; border-radius: 8px;">
                <input type="password" name="new_password_confirmation" placeholder="Ulangi kata sandi baru"
                  class="form-control py-1.5 text-xs" style="font-size: 12px; border-radius: 8px;">
              </div>

              <button type="submit" class="btn btn-earth w-100 py-2 rounded-pill fw-bold shadow-xs" style="font-size: 13px;">
                Simpan Perubahan
              </button>
            </form>
          </div>
        </div>

        <!-- Right: Riwayat Like & Komentar -->
        <div class="col-lg-8">
          <div class="card-earth p-4 p-md-4.5 shadow-sm" style="background: #FFFFFF; border-radius: 20px; border: 1px solid rgba(140, 94, 60, 0.15);">
            
            <!-- Tabs Navigation -->
            <ul class="nav nav-pills gap-2 mb-4 pb-3 border-bottom" id="profileTabs" role="tablist" style="border-color: rgba(140, 94, 60, 0.12) !important;">
              <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-3.5 py-1.5 fw-bold d-inline-flex align-items-center gap-2"
                  id="liked-tab" data-bs-toggle="pill" data-bs-target="#liked-pane" type="button" role="tab"
                  style="font-size: 13px;">
                  <i class="ri-heart-fill text-danger"></i>
                  <span>Artikel Disukai</span>
                  <span class="badge bg-danger text-white rounded-pill ms-1" style="font-size: 10px;">{{ $likedArticles->count() }}</span>
                </button>
              </li>

              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3.5 py-1.5 fw-bold d-inline-flex align-items-center gap-2"
                  id="comments-tab" data-bs-toggle="pill" data-bs-target="#comments-pane" type="button" role="tab"
                  style="font-size: 13px;">
                  <i class="ri-chat-3-fill text-primary"></i>
                  <span>Komentar Saya</span>
                  <span class="badge bg-primary text-white rounded-pill ms-1" style="font-size: 10px;">{{ $myComments->count() }}</span>
                </button>
              </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content" id="profileTabsContent">
              
              <!-- Tab 1: Liked Articles -->
              <div class="tab-pane fade show active" id="liked-pane" role="tabpanel">
                @if($likedArticles->count() > 0)
                  <div class="row g-3">
                    @foreach($likedArticles as $article)
                      <div class="col-md-6">
                        <div class="card-earth p-0 h-100 overflow-hidden shadow-2xs border transition-hover" style="border-radius: 16px;">
                          <div class="d-flex">
                            <img src="{{ $article->banner_url }}" class="shrink-0" style="width: 100px; height: 100px; object-fit: cover;" alt="Banner">
                            <div class="p-2.5 d-flex flex-column justify-content-between flex-grow-1">
                              <div>
                                <span class="badge mb-1" style="background: var(--earth-accent-soft); color: var(--earth-primary); font-size: 9.5px;">{{ $article->kategori }}</span>
                                <h6 class="font-serif fw-bold mb-1 line-clamp-2" style="font-size: 13px;">
                                  <a href="{{ route('articles.show', $article->slug) }}" class="text-decoration-none text-dark">
                                    {{ $article->judul }}
                                  </a>
                                </h6>
                              </div>
                              <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 11px;">
                                <span>{{ $article->formatted_date }}</span>
                                <a href="{{ route('articles.show', $article->slug) }}" class="fw-bold text-decoration-none" style="color: var(--earth-primary);">
                                  Baca <i class="ri-arrow-right-s-line"></i>
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @else
                  <div class="text-center py-5 text-muted">
                    <i class="ri-heart-line text-4xl d-block mb-2 text-danger opacity-50" style="font-size: 38px;"></i>
                    <h6 class="font-serif fw-bold mb-1" style="color: var(--earth-dark);">Belum Ada Artikel yang Disukai</h6>
                    <p class="small text-muted mb-3">Klik tombol hati pada artikel yang memberkati Anda untuk menyimpannya di sini.</p>
                    <a href="{{ route('articles.index') }}" class="btn btn-sm btn-earth rounded-pill px-3.5 py-1.5 fw-bold" style="font-size: 12px;">
                      Mulai Baca Artikel
                    </a>
                  </div>
                @endif
              </div>

              <!-- Tab 2: My Comments -->
              <div class="tab-pane fade" id="comments-pane" role="tabpanel">
                @if($myComments->count() > 0)
                  <div class="space-y-3">
                    @foreach($myComments as $com)
                      <div class="p-3 rounded-3 border mb-2.5" style="background: var(--earth-surface-warm); border-color: rgba(140, 94, 60, 0.15) !important;">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                          <span class="small text-muted" style="font-size: 11.5px;">
                            Pada artikel:
                            @if($com->article)
                              <a href="{{ route('articles.show', $com->article->slug) }}" class="fw-bold text-decoration-none" style="color: var(--earth-primary);">
                                {{ $com->article->judul }}
                              </a>
                            @else
                              <em>(Artikel telah dihapus)</em>
                            @endif
                          </span>
                          <span class="text-muted" style="font-size: 11px;">{{ $com->formatted_date }}</span>
                        </div>
                        <p class="small mb-0 text-dark" style="font-size: 13px; line-height: 1.5;">
                          "{{ $com->komentar }}"
                        </p>
                      </div>
                    @endforeach
                  </div>
                @else
                  <div class="text-center py-5 text-muted">
                    <i class="ri-chat-smile-2-line text-4xl d-block mb-2 text-primary opacity-50" style="font-size: 38px;"></i>
                    <h6 class="font-serif fw-bold mb-1" style="color: var(--earth-dark);">Belum Menuliskan Komentar</h6>
                    <p class="small text-muted mb-3">Tuliskan refleksi, kesan, atau doa Anda pada artikel untuk berbagi berkat bersama jemaat.</p>
                    <a href="{{ route('articles.index') }}" class="btn btn-sm btn-earth rounded-pill px-3.5 py-1.5 fw-bold" style="font-size: 12px;">
                      Lihat Daftar Artikel
                    </a>
                  </div>
                @endif
              </div>

            </div>

          </div>
        </div>

      </div>

    </div>
  </section>

  <script>
    function previewAvatar(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>
@endsection
