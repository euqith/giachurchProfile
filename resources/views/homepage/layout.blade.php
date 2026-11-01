<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gereja Isa Almasih Surabaya - Website Profil Gereja</title>
  <link rel="shortcut icon" href="{{ asset('homepage/assets/images/favicon.png') }}">
  <link rel="stylesheet" href="{{ asset('homepage/assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('homepage/assets/css/meanmenu.css') }}">
  <link rel="stylesheet" href="{{ asset('homepage/assets/css/all.min.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('homepage/assets/css/swiper-bundle.min.css') }}">
  <link rel="stylesheet" href="{{ asset('homepage/assets/css/magnific-popup.css') }}">
  <link rel="stylesheet" href="{{ asset('homepage/assets/css/animate.css') }}">
  <link rel="stylesheet" href="{{ asset('homepage/assets/css/nice-select.css') }}">
  <link rel="stylesheet" href="{{ asset('homepage/assets/css/style.css') }}">

  {{-- Vite Tailwind Assets --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

  <div id="loading">
    <div id="loading-center">
      <div id="loading-center-absolute">
        <div class="loading-icon text-center d-flex flex-column align-items-center justify-content-center">
          <img class="loading-logo" src="{{ asset('homepage/assets/images/preloader.svg') }}" alt="icon">
        </div>
      </div>
    </div>
  </div>

  <!-- TOP HEADER INFO -->
  <div class="header-top d-none d-lg-block" style="background-color: #0b1727; border-bottom: 1px solid rgba(255,255,255,0.08);">
    <div class="container">
      <div class="header-top-wrp">
        <ul class="info">
          <li>
            <i class="ri-mail-line text-primary me-1"></i>
            <a href="mailto:sekretariat@giasurabaya.org" class="ms-1">sekretariat@giasurabaya.org</a>
          </li>
          <li class="ms-4">
            <i class="ri-phone-line text-primary me-1"></i>
            <a href="tel:+6231567890" class="ms-1">+62-31-567-890 / +62-812-3456-789</a>
          </li>
          <li class="ms-4">
            <i class="ri-map-pin-line text-primary me-1"></i>
            <span class="text-white-50 ms-1">Surabaya, Jawa Timur</span>
          </li>
        </ul>
        <ul class="link-info d-flex align-items-center gap-3">
          <li><a href="{{ route('ibadah.public') }}" class="text-white text-decoration-none small"><i class="ri-calendar-check-line me-1 text-primary"></i> Jadwal Ibadah</a></li>
          <li class="text-white-50">|</li>
          <li><a href="{{ route('admin.login') }}" class="text-white text-decoration-none small"><i class="ri-lock-line me-1"></i> CMS Login</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- MAIN HEADER -->
  <header class="header-area bg-white shadow-sm sticky-top">
    <div class="container">
      <div class="header__main d-flex align-items-center justify-content-between py-2">
        <a href="{{ url('/') }}" class="logo d-flex align-items-center gap-2 text-decoration-none">
          <img src="{{ asset('homepage/assets/images/logo/giaslogosmall.png') }}" alt="GIA Logo" style="height: 48px; width: auto;">
          <div class="d-none d-sm-block">
            <strong class="d-block text-dark fw-bold" style="font-size: 16px; line-height: 1.2;">Gereja Isa Almasih</strong>
            <span class="text-muted" style="font-size: 11px; letter-spacing: 0.5px;">JEMAAT SURABAYA</span>
          </div>
        </a>
        <div class="main-menu">
          <nav>
            <ul>
              <li><a href="{{ url('/') }}">Home</a></li>
              <li><a href="{{ url('/#streaming') }}">Live Streaming</a></li>
              <li><a href="{{ url('/warta') }}">Warta Jemaat</a></li>
              <li><a href="{{ url('/event') }}">Event</a></li>
              <li>
                <a href="{{ route('ibadah.public') }}" class="fw-bold" style="color: #0d6efd !important;">
                  <i class="ri-book-open-line me-1"></i> Jadwal Ibadah
                  <span class="badge bg-primary text-white ms-1" style="font-size: 10px; border-radius: 20px; padding: 2px 7px;">Liturgi</span>
                </a>
              </li>
            </ul>
          </nav>
        </div>
        <div class="d-none d-lg-flex align-items-center gap-2">
          <a href="{{ route('ibadah.public') }}" class="btn btn-primary btn-sm px-3 py-2 rounded-pill fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="font-size: 12px;">
            <i class="ri-calendar-event-line"></i>
            <span>LIHAT JADWAL & LITURGI</span>
          </a>
        </div>
        <div class="bars d-block d-lg-none">
          <i id="openButton" class="fa-solid fa-bars"></i>
        </div>
      </div>
    </div>
  </header>

  <!-- MOBILE SIDEBAR -->
  <div id="targetElement" class="sidebar-area sidebar__hide">
    <div class="sidebar__overlay"></div>
    <div class="p-4">
      <a href="{{ url('/') }}" class="logo d-block mb-4 text-decoration-none">
        <img src="{{ asset('homepage/assets/images/logo/giaslogosmall.png') }}" alt="logo" style="height: 50px;">
        <span class="text-white d-block fw-bold mt-2">Gereja Isa Almasih Surabaya</span>
      </a>
      <div class="mobile-menu overflow-hidden">
        <ul class="list-unstyled">
          <li class="py-2 border-bottom border-secondary"><a href="{{ url('/') }}" class="text-white text-decoration-none">Home</a></li>
          <li class="py-2 border-bottom border-secondary"><a href="{{ url('/#streaming') }}" class="text-white text-decoration-none">Live Streaming</a></li>
          <li class="py-2 border-bottom border-secondary"><a href="{{ url('/warta') }}" class="text-white text-decoration-none">Warta Jemaat</a></li>
          <li class="py-2 border-bottom border-secondary"><a href="{{ url('/event') }}" class="text-white text-decoration-none">Event</a></li>
          <li class="py-2 border-bottom border-secondary">
            <a href="{{ route('ibadah.public') }}" class="text-warning text-decoration-none fw-bold">
              ⛪ Jadwal Ibadah & Liturgi
            </a>
          </li>
          <li class="py-2"><a href="{{ route('admin.login') }}" class="text-info text-decoration-none">CMS Login</a></li>
        </ul>
      </div>
      <button id="closeButton" class="text-white mt-4 btn btn-outline-light btn-sm"><i class="fa-solid fa-xmark me-1"></i> Tutup Menu</button>
    </div>
  </div>

  <main>
    @yield('konten')
  </main>

  <!-- FOOTER -->
  <footer class="footer-area text-white pt-5 pb-3" style="background-color: #0b1727; border-top: 4px solid #0d6efd;">
    <div class="container mb-4">
      <div class="row g-4">
        <!-- Col 1: Church Bio -->
        <div class="col-lg-4 col-md-6">
          <div class="d-flex align-items-center gap-2 mb-3">
            <img src="{{ asset('homepage/assets/images/logo/giaslogosmall.png') }}" alt="GIA Logo" style="height: 44px; filter: brightness(1.2);">
            <div>
              <h5 class="mb-0 fw-bold text-white">Gereja Isa Almasih</h5>
              <small class="text-white-50">Jemaat Surabaya</small>
            </div>
          </div>
          <p class="text-white-50 small pe-lg-3 leading-relaxed">
            Mewujudkan jemaat yang bertumbuh dalam iman kepada Kristus, berakar dalam firman Tuhan, dan berbuah dalam pelayanan kasih kepada sesama.
          </p>
          <div class="d-flex gap-2 mt-3">
            <a href="#0" class="btn btn-outline-light btn-sm rounded-circle" style="width: 34px; height: 34px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="fab fa-facebook-f"></i></a>
            <a href="#0" class="btn btn-outline-light btn-sm rounded-circle" style="width: 34px; height: 34px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="fab fa-instagram"></i></a>
            <a href="#0" class="btn btn-outline-light btn-sm rounded-circle" style="width: 34px; height: 34px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="fab fa-youtube"></i></a>
          </div>
        </div>

        <!-- Col 2: Jadwal Ibadah Rutin -->
        <div class="col-lg-3 col-md-6">
          <h6 class="fw-bold text-white mb-3 text-uppercase" style="letter-spacing: 0.5px;">Jadwal Ibadah Raya</h6>
          <ul class="list-unstyled text-white-50 small space-y-2">
            <li class="mb-2"><strong class="text-white">Ibadah Pagi:</strong> Minggu, 06.30 & 08.30 WIB</li>
            <li class="mb-2"><strong class="text-white">Ibadah Siang:</strong> Minggu, 10.30 WIB</li>
            <li class="mb-2"><strong class="text-white">Ibadah Sore:</strong> Minggu, 16.30 WIB</li>
            <li class="mb-2"><strong class="text-white">Doa Tengah Minggu:</strong> Rabu, 18.30 WIB</li>
          </ul>
          <a href="{{ route('ibadah.public') }}" class="btn btn-sm btn-outline-primary text-white rounded-pill mt-2" style="font-size: 11px;">
            <i class="ri-book-read-line me-1"></i> Buka Liturgi & Lagu Lengkap
          </a>
        </div>

        <!-- Col 3: Akses Cepat -->
        <div class="col-lg-2 col-md-6">
          <h6 class="fw-bold text-white mb-3 text-uppercase" style="letter-spacing: 0.5px;">Akses Cepat</h6>
          <ul class="list-unstyled small">
            <li class="mb-2"><a href="{{ url('/') }}" class="text-white-50 text-decoration-none hover-white">Beranda</a></li>
            <li class="mb-2"><a href="{{ route('ibadah.public') }}" class="text-white text-decoration-none hover-white fw-bold"><i class="ri-arrow-right-s-line text-primary"></i> Jadwal Ibadah</a></li>
            <li class="mb-2"><a href="{{ url('/#streaming') }}" class="text-white-50 text-decoration-none hover-white">Live Streaming</a></li>
            <li class="mb-2"><a href="{{ url('/warta') }}" class="text-white-50 text-decoration-none hover-white">Warta Jemaat Digital</a></li>
            <li class="mb-2"><a href="{{ url('/event') }}" class="text-white-50 text-decoration-none hover-white">Agenda Event</a></li>
            <li class="mb-2"><a href="{{ route('admin.login') }}" class="text-primary text-decoration-none hover-white">CMS Admin Login</a></li>
          </ul>
        </div>

        <!-- Col 4: Kontak Sekretariat -->
        <div class="col-lg-3 col-md-6">
          <h6 class="fw-bold text-white mb-3 text-uppercase" style="letter-spacing: 0.5px;">Sekretariat Gereja</h6>
          <p class="text-white-50 small mb-2"><i class="ri-map-pin-2-line text-primary me-2"></i> Jl. Darmo Raya / Cabang Surabaya</p>
          <p class="text-white-50 small mb-2"><i class="ri-phone-line text-primary me-2"></i> (031) 567-890</p>
          <p class="text-white-50 small mb-2"><i class="ri-whatsapp-line text-emerald-400 me-2"></i> +62 812-3456-789 (Pelayanan Jemaat)</p>
          <p class="text-white-50 small"><i class="ri-time-line text-primary me-2"></i> Selasa - Minggu, 08.00 - 17.00 WIB</p>
        </div>
      </div>
    </div>

    <div class="footer__bottom-wrapper text-center py-3 border-top border-secondary" style="border-color: rgba(255,255,255,0.08) !important;">
      <p class="text-white-50 small mb-0">&copy; {{ date('Y') }} Gereja Isa Almasih Surabaya. All Rights Reserved.</p>
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
