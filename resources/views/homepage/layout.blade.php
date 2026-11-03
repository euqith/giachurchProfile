<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gereja Isa Almasih Surabaya - Website Resmi</title>
  <link rel="shortcut icon" href="{{ asset('homepage/assets/images/favicon.png') }}?v=3">
  <link rel="icon" type="image/png" href="{{ asset('homepage/assets/images/favicon.png') }}?v=3">
  <link rel="apple-touch-icon" href="{{ asset('homepage/assets/images/favicon.png') }}?v=3">
  <link rel="stylesheet" href="{{ asset('homepage/assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('homepage/assets/css/meanmenu.css') }}">
  <link rel="stylesheet" href="{{ asset('homepage/assets/css/all.min.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('homepage/assets/css/swiper-bundle.min.css') }}">
  <link rel="stylesheet" href="{{ asset('homepage/assets/css/magnific-popup.css') }}">
  <link rel="stylesheet" href="{{ asset('homepage/assets/css/animate.css') }}">
  <link rel="stylesheet" href="{{ asset('homepage/assets/css/nice-select.css') }}">
  <link rel="stylesheet" href="{{ asset('homepage/assets/css/style.css') }}">

  {{-- Google Fonts: Plus Jakarta Sans & Playfair Display --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  {{-- Vite Tailwind Assets --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    :root {
      --earth-bg: #FAF7F2;
      --earth-surface: #FFFFFF;
      --earth-surface-warm: #F5EFEB;
      --earth-primary: #8C5E3C;
      --earth-primary-hover: #734B2E;
      --earth-accent: #C27D38;
      --earth-accent-soft: #EFE5DC;
      --earth-dark: #1E1713;
      --earth-dark-soft: #2B231D;
      --earth-border: #E8DFD5;
      --earth-text: #2D2520;
      --earth-text-muted: #786C64;
    }

    body {
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
      background-color: var(--earth-bg) !important;
      color: var(--earth-text) !important;
      -webkit-font-smoothing: antialiased;
      overflow-x: hidden;
    }

    h1, h2, h3, .font-serif {
      font-family: 'Playfair Display', Georgia, serif;
      letter-spacing: -0.01em;
    }

    /* Loading Icon: Logo Gereja Animasi */
    #loading {
      background-color: #FAF7F2 !important;
    }
    #loading .loading-icon .loading-logo {
      max-width: 230px !important;
      width: auto !important;
      height: auto !important;
      max-height: 80px !important;
      object-fit: contain !important;
      animation: logoPulse 1.8s ease-in-out infinite alternate !important;
    }
    @keyframes logoPulse {
      0% {
        transform: scale(0.96);
        opacity: 0.85;
      }
      100% {
        transform: scale(1.02);
        opacity: 1;
      }
    }

    /* Top bar */
    .header-top-earth {
      background-color: var(--earth-dark) !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      font-size: 13px;
    }
    .header-top-earth a {
      color: #E6DDD4 !important;
      transition: color 0.2s;
    }
    .header-top-earth a:hover {
      color: var(--earth-accent) !important;
    }

    /* Main Header */
    .header-area-earth {
      background: rgba(255, 255, 255, 0.98) !important;
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--earth-border);
      transition: all 0.3s ease;
    }

    .header-church-logo {
      height: 48px;
      max-height: 52px;
      width: auto;
      max-width: 260px;
      object-fit: contain;
      transition: all 0.2s ease;
    }

    /* Balanced 3-Column Center Nav Layout */
    .header__main {
      position: relative;
    }
    @media (min-width: 992px) {
      .header-col-left,
      .header-col-right {
        flex: 1 1 0;
        min-width: 0;
      }
      .main-menu {
        flex: 0 0 auto;
      }
    }
    .main-menu ul {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 24px;
    }

    .main-menu ul li a {
      color: var(--earth-dark) !important;
      font-weight: 600;
      font-size: 14.5px;
      padding: 12px 18px !important;
      transition: all 0.2s ease;
      position: relative;
    }
    .main-menu ul li a:hover,
    .main-menu ul li a.active {
      color: var(--earth-primary) !important;
    }
    .main-menu ul li a::after {
      content: '';
      position: absolute;
      bottom: 6px;
      left: 18px;
      right: 18px;
      height: 2px;
      background: var(--earth-primary);
      transform: scaleX(0);
      transition: transform 0.2s ease;
    }
    .main-menu ul li a:hover::after,
    .main-menu ul li a.active::after {
      transform: scaleX(1);
    }

    /* Soft Earth Buttons */
    .btn-earth {
      background: var(--earth-primary) !important;
      color: #FFFFFF !important;
      border: 1px solid var(--earth-primary) !important;
      font-weight: 600;
      transition: all 0.25s ease;
      box-shadow: 0 4px 14px rgba(140, 94, 60, 0.2);
    }
    .btn-earth:hover {
      background: var(--earth-primary-hover) !important;
      border-color: var(--earth-primary-hover) !important;
      color: #FFFFFF !important;
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(140, 94, 60, 0.28);
    }

    .btn-earth-outline {
      background: transparent !important;
      color: var(--earth-primary) !important;
      border: 1.5px solid var(--earth-primary) !important;
      font-weight: 600;
      transition: all 0.25s ease;
    }
    .btn-earth-outline:hover {
      background: var(--earth-primary) !important;
      color: #FFFFFF !important;
      transform: translateY(-1px);
    }

    .btn-earth-light {
      background: var(--earth-surface-warm) !important;
      color: var(--earth-dark) !important;
      border: 1px solid var(--earth-border) !important;
      font-weight: 600;
      transition: all 0.25s ease;
    }
    .btn-earth-light:hover {
      background: #EFE4DC !important;
      color: var(--earth-dark) !important;
    }

    /* Badges */
    .badge-earth {
      background-color: var(--earth-accent-soft) !important;
      color: var(--earth-primary) !important;
      border: 1px solid rgba(140, 94, 60, 0.25) !important;
      font-weight: 700;
    }
    .badge-earth-dark {
      background-color: var(--earth-dark) !important;
      color: #FAF7F2 !important;
      font-weight: 600;
    }

    /* Earth Tone Cards */
    .card-earth {
      background: var(--earth-surface) !important;
      border: 1px solid var(--earth-border) !important;
      border-radius: 20px;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .card-earth:hover {
      transform: translateY(-4px);
      box-shadow: 0 16px 32px rgba(43, 35, 29, 0.08);
      border-color: #D6C8BC !important;
    }

    /* Footer */
    .footer-area-earth {
      background-color: var(--earth-dark) !important;
      border-top: 3px solid var(--earth-primary);
      color: #E2D7CE;
    }
    .footer-area-earth a {
      color: #B5A89E;
      text-decoration: none;
      transition: color 0.2s;
    }
    .footer-area-earth a:hover {
      color: #FFFFFF;
    }

    /* Pulsing Live Dot */
    .live-pulse-dot {
      display: inline-block;
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: #EF4444;
      box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
      animation: pulseLive 1.8s infinite;
    }
    @keyframes pulseLive {
      0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
      }
      70% {
        transform: scale(1);
        box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
      }
      100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
      }
    }

    /* Live Streaming Pop-Up Button (High Contrast & Noticed) */
    .btn-live-stream-pop {
      background: linear-gradient(135deg, #DC2626, #B91C1C) !important;
      color: #FFFFFF !important;
      font-weight: 700 !important;
      border: none !important;
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
      box-shadow: 0 4px 14px rgba(220, 38, 38, 0.38) !important;
      letter-spacing: 0.2px;
    }
    .btn-live-stream-pop:hover {
      background: linear-gradient(135deg, #B91C1C, #991B1B) !important;
      color: #FFFFFF !important;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(220, 38, 38, 0.55) !important;
    }
    .btn-live-stream-pop.is-active-live {
      animation: liveButtonPulse 1.9s infinite !important;
    }
    @keyframes liveButtonPulse {
      0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.65), 0 4px 14px rgba(220, 38, 38, 0.4);
      }
      50% {
        transform: scale(1.04);
        box-shadow: 0 0 0 9px rgba(220, 38, 38, 0), 0 6px 22px rgba(220, 38, 38, 0.55);
      }
      100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(220, 38, 38, 0), 0 4px 14px rgba(220, 38, 38, 0.4);
      }
    }

    /* =================================================== */
    /* RESPONSIVE OPTIMIZATIONS (TABLET & MOBILE HP)       */
    /* =================================================== */
    @media (max-width: 991.98px) {
      .header-church-logo {
        height: 42px !important;
        max-width: 220px !important;
      }
      .shortcut-cards-section {
        margin-top: 25px !important;
      }
      .banner-two-area .swiper-slide {
        min-height: 480px !important;
      }
      .banner-content-box {
        padding: 60px 15px 70px !important;
      }
      .banner-content-box h1 {
        font-size: 32px !important;
      }
    }

    @media (max-width: 575.98px) {
      .header-church-logo {
        height: 38px !important;
        max-width: 190px !important;
      }
      .shortcut-cards-section {
        margin-top: 20px !important;
      }
      .banner-two-area .swiper-slide {
        min-height: 440px !important;
      }
      .banner-content-box {
        padding: 50px 15px 60px !important;
      }
      .banner-content-box h1 {
        font-size: 24px !important;
      }
      .banner-content-box p {
        font-size: 14px !important;
      }
      .btn-earth, .btn-earth-light, .btn-earth-outline {
        padding: 9px 18px !important;
        font-size: 12.5px !important;
      }
      .card-earth {
        border-radius: 16px;
      }
    }
  </style>
</head>

<body>

  <!-- LOADING SCREEN: DENGAN LOGO GEREJA RESMI -->
  <div id="loading">
    <div id="loading-center">
      <div id="loading-center-absolute">
        <div class="loading-icon text-center d-flex flex-column align-items-center justify-content-center">
          <img class="loading-logo mb-3" src="{{ asset('homepage/assets/images/logo/giaslogosmall.png') }}" alt="Gereja Isa Almasih Surabaya">
          <div class="d-flex align-items-center gap-2 mt-1">
            <span class="spinner-grow text-secondary" style="width: 7px; height: 7px; background-color: var(--earth-primary) !important;"></span>
            <span class="spinner-grow text-secondary" style="width: 7px; height: 7px; background-color: var(--earth-accent) !important; animation-delay: 0.15s;"></span>
            <span class="spinner-grow text-secondary" style="width: 7px; height: 7px; background-color: var(--earth-primary) !important; animation-delay: 0.3s;"></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  @php
    $churchProfileGlobal = \App\Models\ChurchProfile::getProfile();
    $isAnyLive = \App\Models\Stream::where('isDelete', 0)->where('isActive', 1)->where('is_live', 1)->exists();
  @endphp

  <!-- STRIP PEMBERITAHUAN SIARAN LANGSUNG (MUNCUL OTOMATIS SAAT ADA CABANG LIVE) -->
  @if($isAnyLive)
    <div class="py-2 px-3 text-center text-white" style="background: linear-gradient(90deg, #991B1B, #DC2626, #991B1B); font-size: 12.5px; font-weight: 700; letter-spacing: 0.3px; box-shadow: 0 2px 10px rgba(220,38,38,0.25);">
      <a href="{{ url('/#streaming') }}" class="text-white text-decoration-none d-inline-flex align-items-center justify-content-center gap-2 flex-wrap">
        <span class="live-pulse-dot" style="background: #FFFFFF; width: 8px; height: 8px;"></span>
        <span>IBADAH RAYA SEDANG BERLANGSUNG (LIVE STREAMING) — KLIK DI SINI UNTUK BERGABUNG</span>
        <i class="ri-arrow-right-line"></i>
      </a>
    </div>
  @endif

  <!-- MAIN HEADER (STICKY) -->
  <header class="header-area header-area-earth sticky-top">
    <div class="container">
      <div class="header__main d-flex align-items-center justify-content-between py-2">
        <!-- LOGO BRANDING (KIRI) -->
        <div class="header-col-left d-flex align-items-center">
          <a href="{{ url('/') }}" class="logo d-flex align-items-center text-decoration-none py-1">
            <img src="{{ asset('homepage/assets/images/logo/giaslogosmall.png') }}" alt="Gereja Isa Almasih Surabaya" class="header-church-logo">
          </a>
        </div>

        <!-- MAIN NAVBAR (RATA TENGAH) -->
        <div class="main-menu d-none d-lg-flex justify-content-center">
          <nav>
            <ul class="d-flex align-items-center justify-content-center mb-0 list-unstyled">
              <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a></li>
              <li>
                <a href="{{ url('/#streaming') }}" class="d-inline-flex align-items-center gap-1.5">
                  <i class="ri-live-line text-danger"></i> Live Streaming
                  @if($isAnyLive)
                    <span class="badge bg-danger text-white rounded-pill ms-1" style="font-size: 9px; padding: 2px 6px;">LIVE</span>
                  @endif
                </a>
              </li>
              <li><a href="{{ url('/warta') }}" class="{{ request()->is('warta*') ? 'active' : '' }}">Warta Jemaat</a></li>
              <li><a href="{{ url('/event') }}" class="{{ request()->is('event*') ? 'active' : '' }}">Event</a></li>
            </ul>
          </nav>
        </div>

        <!-- RIGHT BALANCER / MOBILE TOGGLE (KANAN) -->
        <div class="header-col-right d-flex align-items-center justify-content-end">
          <!-- MOBILE HAMBURGER TOGGLE -->
          <div class="bars d-block d-lg-none">
            <i id="openButton" class="fa-solid fa-bars" style="font-size: 22px; color: var(--earth-dark); cursor: pointer;"></i>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- MOBILE SIDEBAR -->
  <div id="targetElement" class="sidebar-area sidebar__hide">
    <div class="sidebar__overlay"></div>
    <div class="p-4" style="background-color: var(--earth-dark); min-height: 100vh;">
      <a href="{{ url('/') }}" class="logo d-block mb-4 text-decoration-none">
        <img src="{{ asset('homepage/assets/images/logo/giaslogo-white.png') }}" alt="Gereja Isa Almasih Surabaya" style="height: 44px; width: auto; object-fit: contain;">
      </a>
      <div class="mobile-menu overflow-hidden">
        <ul class="list-unstyled">
          <li class="py-2.5 border-bottom" style="border-color: rgba(255,255,255,0.08) !important;">
            <a href="{{ url('/') }}" class="text-white text-decoration-none d-block">Home</a>
          </li>
          <li class="py-2.5 border-bottom" style="border-color: rgba(255,255,255,0.08) !important;">
            <a href="{{ url('/#streaming') }}" class="text-white text-decoration-none d-flex align-items-center justify-content-between">
              <span><i class="ri-live-line text-danger me-2"></i>Live Streaming</span>
              @if($isAnyLive)
                <span class="badge bg-danger text-white rounded-pill px-2 py-0.5" style="font-size: 10px;">🔴 ON AIR</span>
              @endif
            </a>
          </li>
          <li class="py-2.5 border-bottom" style="border-color: rgba(255,255,255,0.08) !important;">
            <a href="{{ url('/warta') }}" class="text-white text-decoration-none d-block">Warta Jemaat</a>
          </li>
          <li class="py-2.5 border-bottom" style="border-color: rgba(255,255,255,0.08) !important;">
            <a href="{{ url('/event') }}" class="text-white text-decoration-none d-block">Event</a>
          </li>
          <li class="py-2.5">
            <a href="{{ route('admin.login') }}" class="text-decoration-none d-inline-flex align-items-center gap-1.5" style="color: var(--earth-accent);">
              <i class="ri-lock-line"></i> CMS Login
            </a>
          </li>
        </ul>
      </div>
      <button id="closeButton" class="text-white mt-4 btn btn-outline-light btn-sm rounded-pill px-3">
        <i class="fa-solid fa-xmark me-1"></i> Tutup Menu
      </button>
    </div>
  </div>

  <main>
    @yield('konten')
  </main>

  <!-- FOOTER -->
  <footer class="footer-area footer-area-earth pt-5 pb-3">
    <div class="container mb-4">
      <div class="row g-4">
        <!-- Col 1: Church Bio -->
        <div class="col-lg-4 col-md-6">
          <div class="d-flex align-items-center gap-3 mb-3">
            <img src="{{ asset('homepage/assets/images/logo/giaslogo-white.png') }}" alt="Gereja Isa Almasih Surabaya" style="height: 48px; width: auto; object-fit: contain;">
          </div>
          <p class="small pe-lg-3 leading-relaxed" style="color: #C0B4A9;">
            {{ $churchProfileGlobal->tentang_gereja ?: 'Mewujudkan jemaat yang bertumbuh dalam iman kepada Kristus, berakar dalam firman Tuhan, dan berbuah dalam pelayanan kasih kepada sesama.' }}
          </p>
          <div class="d-flex gap-2 mt-3">
            @if($churchProfileGlobal->instagram_url)
              <a href="{{ $churchProfileGlobal->instagram_url }}" target="_blank" class="btn btn-outline-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 34px; height: 34px; border-color: rgba(255,255,255,0.2);"><i class="fab fa-instagram"></i></a>
            @endif
            @if($churchProfileGlobal->youtube_channel_url)
              <a href="{{ $churchProfileGlobal->youtube_channel_url }}" target="_blank" class="btn btn-outline-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 34px; height: 34px; border-color: rgba(255,255,255,0.2);"><i class="fab fa-youtube"></i></a>
            @endif
            <a href="mailto:{{ $churchProfileGlobal->email ?: 'sekretariat@giasurabaya.org' }}" class="btn btn-outline-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 34px; height: 34px; border-color: rgba(255,255,255,0.2);"><i class="ri-mail-line"></i></a>
          </div>
        </div>

        <!-- Col 2: Jadwal Ibadah Ringkas -->
        <div class="col-lg-3 col-md-6">
          <h6 class="fw-bold text-white mb-3 text-uppercase" style="letter-spacing: 0.8px; font-size: 13px;">Ibadah Raya Mingguan</h6>
          <ul class="list-unstyled small space-y-2" style="color: #C0B4A9;">
            <li class="mb-2"><strong class="text-white">Ibadah Pagi 1:</strong> Minggu, 06.00 WIB</li>
            <li class="mb-2"><strong class="text-white">Ibadah Pagi 2:</strong> Minggu, 08.00 WIB</li>
            <li class="mb-2"><strong class="text-white">Ibadah Siang:</strong> Minggu, 10.00 WIB</li>
            <li class="mb-2"><strong class="text-white">Ibadah Sore:</strong> Minggu, 17.00 WIB</li>
            <li class="mb-2"><strong class="text-white">Doa Tengah Minggu:</strong> Rabu, 18.30 WIB</li>
          </ul>
        </div>

        <!-- Col 3: Navigasi -->
        <div class="col-lg-2 col-md-6">
          <h6 class="fw-bold text-white mb-3 text-uppercase" style="letter-spacing: 0.8px; font-size: 13px;">Navigasi</h6>
          <ul class="list-unstyled small" style="color: #C0B4A9;">
            <li class="mb-2"><a href="{{ url('/') }}" class="text-decoration-none">Beranda</a></li>
            <li class="mb-2"><a href="{{ url('/#tentang') }}" class="text-decoration-none">Tentang Gereja</a></li>
            <li class="mb-2"><a href="{{ url('/#visimisi') }}" class="text-decoration-none">Visi & Misi</a></li>
            <li class="mb-2"><a href="{{ url('/#jadwal') }}" class="text-decoration-none">Jadwal Ibadah</a></li>
            <li class="mb-2"><a href="{{ url('/#streaming') }}" class="text-decoration-none">Live Streaming</a></li>
            <li class="mb-2"><a href="{{ url('/warta') }}" class="text-decoration-none">Warta Jemaat</a></li>
            <li class="mb-2"><a href="{{ url('/event') }}" class="text-decoration-none">Event Gereja</a></li>
          </ul>
        </div>

        <!-- Col 4: Kontak Sekretariat -->
        <div class="col-lg-3 col-md-6">
          <h6 class="fw-bold text-white mb-3 text-uppercase" style="letter-spacing: 0.8px; font-size: 13px;">Sekretariat Gereja</h6>
          <p class="small mb-2" style="color: #C0B4A9;"><i class="ri-map-pin-2-line me-2" style="color: var(--earth-accent);"></i> {{ $churchProfileGlobal->alamat_pusat ?: 'Jl. Darmo Raya / Cabang Surabaya' }}</p>
          <p class="small mb-2" style="color: #C0B4A9;"><i class="ri-phone-line me-2" style="color: var(--earth-accent);"></i> {{ $churchProfileGlobal->telepon ?: '(031) 567-890' }}</p>
          <p class="small mb-2" style="color: #C0B4A9;"><i class="ri-mail-line me-2" style="color: var(--earth-accent);"></i> {{ $churchProfileGlobal->email ?: 'sekretariat@giasurabaya.org' }}</p>
          <p class="small" style="color: #C0B4A9;"><i class="ri-time-line me-2" style="color: var(--earth-accent);"></i> Selasa - Minggu, 08.00 - 17.00 WIB</p>
        </div>
      </div>
    </div>

    <div class="footer__bottom-wrapper text-center py-3 border-top" style="border-color: rgba(255,255,255,0.08) !important;">
      <p class="small mb-0" style="color: #9E9286;">&copy; {{ date('Y') }} {{ $churchProfileGlobal->nama_gereja ?: 'Gereja Isa Almasih Surabaya' }}. All Rights Reserved.</p>
    </div>
  </footer>

  <div class="scroll-up">
    <svg class="scroll-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
      <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
    </svg>
  </div>

  <script src="{{ asset('homepage/assets/js/jquery-3.7.1.min.js') }}"></script>
  <script src="{{ asset('homepage/assets/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('homepage/assets/js/meanmenu.js') }}"></script>
  <script src="{{ asset('homepage/assets/js/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('homepage/assets/js/jquery.counterup.min.js') }}"></script>
  <script src="{{ asset('homepage/assets/js/wow.min.js') }}"></script>
  <script src="{{ asset('homepage/assets/js/jquery.magnific-popup.min.js') }}"></script>
  <script src="{{ asset('homepage/assets/js/jquery.nice-select.min.js') }}"></script>
  <script src="{{ asset('homepage/assets/js/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('homepage/assets/js/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('homepage/assets/js/script.js') }}"></script>
</body>

</html>
