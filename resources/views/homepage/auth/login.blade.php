@extends('homepage.layout')

@section('konten')
  <section class="section-gap py-5" style="background: var(--earth-surface-warm); min-height: 80vh; display: flex; align-items: center;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5 col-xl-4">
          
          <div class="card-earth p-4 p-sm-5 shadow-lg" style="background: #FFFFFF; border-radius: 24px; border: 1px solid rgba(140, 94, 60, 0.18);">
            
            <!-- Logo & Heading -->
            <div class="text-center mb-4">
              <img src="{{ asset('homepage/assets/images/logo/logo-gelap.png') }}"
                onerror="this.onerror=null;this.src='{{ asset('homepage/assets/images/logo/tulisan-putih.png') }}';"
                alt="Logo Gereja" class="mb-3" style="max-height: 58px; width: auto; object-fit: contain;">
              <h3 class="font-serif fw-bold mb-1" style="color: var(--earth-dark); font-size: 22px;">Masuk Akun Jemaat</h3>
              <p class="text-muted small mb-0" style="font-size: 13px;">Gereja Isa Almasih Surabaya</p>
            </div>

            <!-- Alerts -->
            @if(session('success'))
              <div class="alert alert-success p-2.5 rounded-3 text-xs mb-3" style="font-size: 12.5px;">
                {{ session('success') }}
              </div>
            @endif

            @if(session('warning'))
              <div class="alert alert-warning p-2.5 rounded-3 text-xs mb-3" style="font-size: 12.5px;">
                {{ session('warning') }}
              </div>
            @endif

            @if(session('error'))
              <div class="alert alert-danger p-2.5 rounded-3 text-xs mb-3" style="font-size: 12.5px;">
                {{ session('error') }}
              </div>
            @endif

            @if ($errors->any())
              <div class="alert alert-danger p-2.5 rounded-3 text-xs mb-3" style="font-size: 12.5px;">
                @foreach ($errors->all() as $error)
                  <div>{{ $error }}</div>
                @endforeach
              </div>
            @endif

            <!-- Form Login -->
            <form action="{{ route('jemaat.login.submit') }}" method="POST" class="space-y-3">
              @csrf
              @if($redirect)
                <input type="hidden" name="redirect" value="{{ $redirect }}">
              @endif

              <div class="mb-3">
                <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 12px;">Username atau Email</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 12px 0 0 12px;">
                    <i class="ri-user-line"></i>
                  </span>
                  <input type="text" name="login" value="{{ old('login') }}" required autofocus
                    placeholder="Masukkan username atau email"
                    class="form-control border-start-0 py-2" style="border-radius: 0 12px 12px 0; font-size: 13.5px; border-color: #dee2e6;">
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 12px;">Kata Sandi</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 12px 0 0 12px;">
                    <i class="ri-lock-line"></i>
                  </span>
                  <input type="password" name="password" required
                    placeholder="••••••••"
                    class="form-control border-start-0 py-2" style="border-radius: 0 12px 12px 0; font-size: 13.5px; border-color: #dee2e6;">
                </div>
              </div>

              <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                  <label class="form-check-label small text-muted" for="remember" style="font-size: 12px;">
                    Ingat saya
                  </label>
                </div>
              </div>

              <button type="submit" class="btn btn-earth w-100 py-2.5 rounded-pill fw-bold mb-3 shadow-xs" style="font-size: 13.5px;">
                Masuk Sekarang
              </button>
            </form>

            <!-- Divider -->
            <div class="position-relative text-center my-3">
              <hr class="text-muted" style="opacity: 0.15;">
              <span class="position-absolute top-50 start-50 translate-middle px-3 bg-white text-muted small" style="font-size: 11.5px;">
                atau
              </span>
            </div>

            <!-- Google Sign-In Button -->
            <a href="{{ route('jemaat.google', ['redirect' => $redirect]) }}"
              class="btn btn-light w-100 py-2 rounded-pill border d-flex align-items-center justify-content-center gap-2.5 font-sans fw-semibold mb-4 transition shadow-xs"
              style="font-size: 13px; color: #3c4043; border-color: #dadce0 !important;">
              <svg width="18" height="18" viewBox="0 0 18 18">
                <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.616z"/>
                <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
                <path fill="#FBBC05" d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707s.102-1.167.282-1.707V4.961H.957C.347 6.175 0 7.55 0 9s.347 2.825.957 4.039l3.007-2.332z"/>
                <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.961L3.964 7.293C4.672 5.166 6.656 3.58 9 3.58z"/>
              </svg>
              <span>Lanjutkan dengan Google</span>
            </a>

            <!-- Register Link -->
            <div class="text-center pt-2 border-top" style="border-color: rgba(140, 94, 60, 0.12) !important;">
              <p class="small text-muted mb-1" style="font-size: 12.5px;">
                Belum memiliki akun jemaat?
                <a href="{{ route('jemaat.register', ['redirect' => $redirect]) }}" class="fw-bold text-decoration-none" style="color: var(--earth-primary);">
                  Daftar di sini
                </a>
              </p>
              <a href="{{ route('admin.login') }}" class="small text-muted text-decoration-none opacity-75" style="font-size: 11px;">
                <i class="ri-shield-user-line"></i> Login Petugas / Staf Admin CMS
              </a>
            </div>

          </div>

        </div>
      </div>
    </div>
  </section>
@endsection
