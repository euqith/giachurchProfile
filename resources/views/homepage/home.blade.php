@extends('homepage.layout')

@section('konten')
  <!-- ========================================== -->
  <!-- 1. HERO SLIDER BANNER                      -->
  <!-- ========================================== -->
  <section class="banner-two-area position-relative" style="background-color: var(--earth-dark);">
    <div class="swiper banner-two__slider">
      <div class="swiper-wrapper">

        <!-- ======================================================== -->
        <!-- BANNER 1 (FIXED): FOTO UTAMA GEREJA & PROFIL RESMI       -->
        <!-- ======================================================== -->
        <div class="swiper-slide" style="position: relative; overflow: hidden; min-height: 520px;">
          @php
            $fotoGerejaUrl = $churchProfile->foto_gereja 
              ? asset('img/admin/church/' . $churchProfile->foto_gereja) 
              : asset('homepage/assets/images/banner/banner-image1.jpg');
          @endphp
          <div class="slide-bg"
            style="background-image: linear-gradient(rgba(28, 23, 20, 0.72), rgba(28, 23, 20, 0.84)), url('{{ $fotoGerejaUrl }}'); 
                   background-size: cover; 
                   background-position: center; 
                   position: absolute; 
                   inset: 0; 
                   z-index: 1;">
          </div>

          <div class="container h-100 d-flex align-items-center" style="position: relative; z-index: 2;">
            <div class="banner-two__content banner-content-box max-w-3xl lg:max-w-4xl text-white py-5">

              <span class="badge px-3 py-1.5 mb-3 rounded-pill text-uppercase d-inline-flex align-items-center gap-2"
                style="background: rgba(212, 163, 115, 0.22); color: #F5EFEB; border: 1px solid rgba(212, 163, 115, 0.45); font-size: 11.5px; letter-spacing: 0.8px;">
                <i class="ri-home-4-line" style="color: var(--earth-accent);"></i> Selamat Datang di Rumah Tuhan
              </span>

              <h1 class="text-white mb-3 font-serif fw-bold" style="font-size: clamp(26px, 4.8vw, 44px); line-height: 1.18; letter-spacing: -0.01em;">
                {{ $churchProfile->nama_gereja ?: 'Gereja Isa Almasih Surabaya' }}
              </h1>

              <p class="mb-4 fw-normal leading-relaxed" style="color: #E6DDD4; font-size: clamp(14px, 2vw, 17px); max-width: 780px;">
                {{ $churchProfile->tagline ?: 'Bertumbuh dalam Kristus, Berakar dalam Firman, dan Berbuah bagi Sesama.' }}
              </p>

              <div class="d-flex flex-wrap gap-2.5">
                <a href="#tentang" class="btn btn-earth px-3.5 py-2.5 rounded-pill d-inline-flex align-items-center gap-2" style="font-size: 13px;">
                  <i class="ri-information-line"></i>
                  <span>Tentang Gereja</span>
                </a>
                <a href="#jadwal" class="btn btn-earth-light px-3.5 py-2.5 rounded-pill d-inline-flex align-items-center gap-2" style="font-size: 13px;">
                  <i class="ri-calendar-schedule-line" style="color: var(--earth-primary);"></i>
                  <span>Jadwal Ibadah</span>
                </a>
                @php
                  $isCurrentlyLive = isset($activeLiveStreams) && $activeLiveStreams->count() > 0;
                @endphp
                @if($isCurrentlyLive)
                  <a href="#streaming" class="btn btn-live-stream-pop is-active-live px-3.5 py-2.5 rounded-pill d-inline-flex align-items-center gap-2" style="font-size: 13px;">
                    <span class="live-pulse-dot" style="background: #FFFFFF;"></span>
                    <i class="ri-live-line"></i>
                    <span>Live Streaming</span>
                    <span class="badge bg-white text-danger fw-bold rounded-pill px-1.5 py-0.5" style="font-size: 9.5px;">ON AIR</span>
                  </a>
                @else
                  <a href="#streaming" class="btn btn-hero-stream px-3.5 py-2.5 rounded-pill d-inline-flex align-items-center gap-2" style="font-size: 13px;">
                    <i class="ri-live-line" style="color: #EF4444; font-size: 15px;"></i>
                    <span>Live Streaming</span>
                  </a>
                @endif
              </div>

            </div>
          </div>
        </div>

        <!-- ======================================================== -->
        <!-- BANNER 2+: PREVIEW EVENT ONGOING & MENDATANG (CMS)       -->
        <!-- ======================================================== -->
        @foreach ($sliderEvents as $event)
          <div class="swiper-slide" style="position: relative; overflow: hidden; min-height: 520px;">
            @php
              $bannerEventUrl = $event->gambar_banner 
                ? asset('img/admin/events/' . $event->gambar_banner) 
                : asset('homepage/assets/images/banner/banner-image1.jpg');
              $now = \Carbon\Carbon::now();
              $start = \Carbon\Carbon::parse($event->waktu_mulai);
              $isOngoing = $now->gte($start);
            @endphp
            <div class="slide-bg"
              style="background-image: linear-gradient(rgba(28, 23, 20, 0.72), rgba(28, 23, 20, 0.85)), url('{{ $bannerEventUrl }}'); 
                     background-size: cover; 
                     background-position: center; 
                     position: absolute; 
                     inset: 0; 
                     z-index: 1;">
            </div>

            <div class="container h-100 d-flex align-items-center" style="position: relative; z-index: 2;">
              <div class="banner-two__content banner-content-box max-w-3xl lg:max-w-4xl text-white py-5">

                <span class="badge px-3 py-1.5 mb-3 rounded-pill d-inline-flex align-items-center gap-2"
                  style="background: {{ $isOngoing ? 'rgba(34, 197, 94, 0.25)' : 'rgba(212, 163, 115, 0.25)' }}; 
                         color: #FFFFFF; 
                         border: 1px solid {{ $isOngoing ? 'rgba(34, 197, 94, 0.5)' : 'rgba(212, 163, 115, 0.5)' }}; 
                         font-size: 11.5px; letter-spacing: 0.8px;">
                  @if($isOngoing)
                    <span class="live-pulse-dot" style="background: #22C55E;"></span> EVENT BERLANGSUNG
                  @else
                    <i class="ri-calendar-event-line" style="color: var(--earth-accent);"></i> AGENDA KEGIATAN
                  @endif
                  · {{ $event->cabang ? strtoupper($event->cabang->nama_cabang) : 'SEMUA WILAYAH' }}
                </span>

                <div class="d-flex align-items-center gap-2 mb-2" style="color: #D4C3B5; font-size: 13.5px;">
                  <i class="ri-time-line" style="color: var(--earth-accent);"></i>
                  <span>{{ \Carbon\Carbon::parse($event->waktu_mulai)->translatedFormat('l, d F Y') }} · {{ \Carbon\Carbon::parse($event->waktu_mulai)->format('H:i') }} WIB</span>
                </div>

                <h1 class="text-white mb-3 font-serif fw-bold" style="font-size: clamp(24px, 4.5vw, 40px); line-height: 1.18;">
                  {{ $event->nama_acara }}
                </h1>

                <p class="mb-4 fw-normal leading-relaxed" style="color: #E6DDD4; font-size: clamp(13.5px, 2vw, 15.5px);">
                  <i class="ri-map-pin-2-line me-1" style="color: var(--earth-accent);"></i>
                  {{ $event->lokasi_spesifik ?: 'Gereja Isa Almasih Surabaya' }}
                </p>

                <div class="d-flex flex-wrap gap-2.5">
                  <a href="{{ url('/event') }}" class="btn btn-earth px-3.5 py-2.5 rounded-pill d-inline-flex align-items-center gap-2" style="font-size: 13px;">
                    <i class="ri-calendar-todo-line"></i>
                    <span>Lihat Agenda</span>
                  </a>
                  <a href="{{ url('/event') }}" class="btn btn-hero-stream px-3.5 py-2.5 rounded-pill d-inline-flex align-items-center gap-2" style="font-size: 13px;">
                    <i class="ri-calendar-line"></i>
                    <span>Semua Event</span>
                  </a>
                </div>

              </div>
            </div>
          </div>
        @endforeach

      </div>
      <div class="swiper-button-next text-white d-none d-md-flex" style="right: 25px; z-index: 10;"></div>
      <div class="swiper-button-prev text-white d-none d-md-flex" style="left: 25px; z-index: 10;"></div>
    </div>
  </section>

  <!-- ========================================== -->
  <!-- 2. QUICK SHORTCUT CARDS (OVERLAY BAR)      -->
  <!-- ========================================== -->
  <section class="position-relative shortcut-cards-section" style="margin-top: -50px; z-index: 20;">
    <div class="container">
      <div class="row g-3">

        <!-- CARD 1: TENTANG GEREJA -->
        <div class="col-lg-3 col-sm-6">
          <a href="#tentang" class="text-decoration-none h-100 d-block">
            <div class="card-earth p-3.5 p-md-4 h-100 d-flex flex-column justify-between shadow-sm">
              <div>
                <div class="d-flex align-items-center justify-content-between mb-2.5">
                  <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                    style="background: var(--earth-primary); width: 40px; height: 40px;">
                    <i class="ri-church-line fs-18"></i>
                  </div>
                  <span class="badge badge-earth px-2 py-0.5 rounded-pill" style="font-size: 10.5px;">
                    Profil
                  </span>
                </div>
                <h5 class="fw-bold mb-1" style="color: var(--earth-dark); font-size: 16px;">Tentang Gereja</h5>
                <p class="small mb-0" style="color: var(--earth-text-muted); font-size: 12.5px;">Mengenal sejarah, visi, dan misi pelayanan GIA Surabaya.</p>
              </div>
              <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between small fw-bold" style="border-color: var(--earth-border) !important; color: var(--earth-primary);">
                <span>Selengkapnya</span>
                <i class="ri-arrow-right-line"></i>
              </div>
            </div>
          </a>
        </div>

        <!-- CARD 2: JADWAL IBADAH -->
        <div class="col-lg-3 col-sm-6">
          <a href="#jadwal" class="text-decoration-none h-100 d-block">
            <div class="card-earth p-3.5 p-md-4 h-100 d-flex flex-column justify-between shadow-sm">
              <div>
                <div class="d-flex align-items-center justify-content-between mb-2.5">
                  <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                    style="background: #A06A42; width: 40px; height: 40px;">
                    <i class="ri-calendar-schedule-line fs-18"></i>
                  </div>
                  <span class="badge badge-earth px-2 py-0.5 rounded-pill" style="font-size: 10.5px;">
                    5 Wilayah
                  </span>
                </div>
                <h5 class="fw-bold mb-1" style="color: var(--earth-dark); font-size: 16px;">Jadwal Ibadah</h5>
                <p class="small mb-0" style="color: var(--earth-text-muted); font-size: 12.5px;">Waktu ibadah raya minggu dan sesi doa di setiap wilayah.</p>
              </div>
              <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between small fw-bold" style="border-color: var(--earth-border) !important; color: var(--earth-primary);">
                <span>Lihat Jadwal</span>
                <i class="ri-arrow-right-line"></i>
              </div>
            </div>
          </a>
        </div>

        <!-- CARD 3: LIVE STREAMING -->
        <div class="col-lg-3 col-sm-6">
          <a href="#streaming" class="text-decoration-none h-100 d-block">
            <div class="card-earth p-3.5 p-md-4 h-100 d-flex flex-column justify-between shadow-sm">
              <div>
                <div class="d-flex align-items-center justify-content-between mb-2.5">
                  <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                    style="background: {{ $activeLiveStreams->count() > 0 ? '#DC2626' : '#6B5E55' }}; width: 40px; height: 40px;">
                    <i class="ri-live-line fs-18"></i>
                  </div>
                  <span class="badge px-2 py-0.5 rounded-pill fw-bold"
                    style="font-size: 10.5px; {{ $activeLiveStreams->count() > 0 ? 'background: #FEE2E2; color: #DC2626; border: 1px solid #FCA5A5;' : 'background: #F3ECE5; color: #786C64;' }}">
                    @if($activeLiveStreams->count() > 0)
                      🔴 {{ $activeLiveStreams->count() }} Live
                    @else
                      Offline
                    @endif
                  </span>
                </div>
                <h5 class="fw-bold mb-1" style="color: var(--earth-dark); font-size: 16px;">Live Streaming</h5>
                <p class="small mb-0" style="color: var(--earth-text-muted); font-size: 12.5px;">Siaran langsung ibadah online dan arsip khotbah YouTube.</p>
              </div>
              <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between small fw-bold" style="border-color: var(--earth-border) !important; color: var(--earth-primary);">
                <span>Buka Siaran</span>
                <i class="ri-arrow-right-line"></i>
              </div>
            </div>
          </a>
        </div>

        <!-- CARD 4: WARTA JEMAAT -->
        <div class="col-lg-3 col-sm-6">
          <a href="{{ url('/warta') }}" class="text-decoration-none h-100 d-block">
            <div class="card-earth p-3.5 p-md-4 h-100 d-flex flex-column justify-between shadow-sm">
              <div>
                <div class="d-flex align-items-center justify-content-between mb-2.5">
                  <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                    style="background: #687968; width: 40px; height: 40px;">
                    <i class="ri-newspaper-line fs-18"></i>
                  </div>
                  <span class="badge badge-earth px-2 py-0.5 rounded-pill" style="font-size: 10.5px;">
                    E-Bulletin
                  </span>
                </div>
                <h5 class="fw-bold mb-1" style="color: var(--earth-dark); font-size: 16px;">Warta Jemaat</h5>
                <p class="small mb-0" style="color: var(--earth-text-muted); font-size: 12.5px;">Informasi pokok doa, persembahan, dan buletin digital.</p>
              </div>
              <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between small fw-bold" style="border-color: var(--earth-border) !important; color: var(--earth-primary);">
                <span>Baca Edisi</span>
                <i class="ri-arrow-right-line"></i>
              </div>
            </div>
          </a>
        </div>

      </div>
    </div>
  </section>

  <!-- ========================================== -->
  <!-- 3. TENTANG GEREJA & VISI MISI              -->
  <!-- ========================================== -->
  <section id="tentang" class="py-5" style="padding-top: 80px !important; padding-bottom: 70px !important;">
    <div class="container">

      <div class="text-center mb-5">
        <span class="badge badge-earth px-3 py-1.5 rounded-pill text-uppercase" style="font-size: 11px; letter-spacing: 0.8px;">
          <i class="ri-shield-cross-line me-1"></i> Profil Jemaat
        </span>
        <h2 class="display-6 font-serif fw-bold mt-2" style="color: var(--earth-dark);">Tentang Gereja Kami</h2>
        <div class="mx-auto" style="width: 50px; height: 3px; background: var(--earth-primary); border-radius: 2px; margin-top: 10px; margin-bottom: 12px;"></div>
        <p class="small max-w-xl mx-auto" style="color: var(--earth-text-muted);">
          Gereja Isa Almasih Surabaya berakar dalam firman Tuhan dan berkomitmen melayani jemaat di berbagai penjuru kota Surabaya.
        </p>
      </div>

      <div class="row g-4 align-items-stretch">

        <!-- KIRI: PROFIL & SEJARAH SINGKAT -->
        <div class="col-lg-6">
          <div class="card-earth p-4 p-md-5 h-100 shadow-sm d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex align-items-center gap-3 mb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                  style="background: var(--earth-primary); width: 48px; height: 48px; flex-shrink: 0;">
                  <i class="ri-building-4-line fs-22"></i>
                </div>
                <div>
                  <h4 class="font-serif fw-bold mb-0" style="color: var(--earth-dark); font-size: 20px;">{{ $churchProfile->nama_gereja ?: 'Gereja Isa Almasih' }}</h4>
                  <small style="color: var(--earth-accent); font-weight: 600;">Jemaat Surabaya · 5 Wilayah</small>
                </div>
              </div>

              <div class="mb-4 leading-relaxed" style="color: #4D423B; font-size: 14.5px;">
                <p>
                  {{ $churchProfile->tentang_gereja ?: 'Gereja Isa Almasih (GIA) Surabaya adalah persekutuan jemaat orang percaya yang berpusat pada Kristus Yesus, berlandaskan kebenaran Alkitab sebagai firman Allah yang hidup, serta terpanggil untuk menghasilkan buah pelayanan kasih bagi sesama.' }}
                </p>
                @if($churchProfile->sejarah_singkat)
                  <p class="mb-0">
                    {{ $churchProfile->sejarah_singkat }}
                  </p>
                @endif
              </div>
            </div>

            <!-- Kontak Singkat -->
            <div class="p-3 rounded-3 mt-3 d-flex flex-wrap align-items-center justify-content-between gap-2" style="background: var(--earth-surface-warm); border: 1px solid var(--earth-border);">
              <div class="small" style="color: var(--earth-text-muted); font-size: 12.5px;">
                <i class="ri-map-pin-line me-1" style="color: var(--earth-primary);"></i>
                <span>{{ $churchProfile->alamat_pusat ?: 'Jl. Darmo / Surabaya' }}</span>
              </div>
              <div class="small fw-bold" style="color: var(--earth-primary); font-size: 12.5px;">
                <i class="ri-phone-line me-1"></i>
                <span>{{ $churchProfile->telepon ?: '(031) 567-890' }}</span>
              </div>
            </div>

          </div>
        </div>

        <!-- KANAN: VISI & MISI -->
        <div class="col-lg-6" id="visimisi">
          <div class="d-flex flex-column gap-3.5 h-100">

            <!-- KARTU VISI -->
            <div class="card-earth p-4 shadow-sm" style="border-left: 5px solid var(--earth-primary) !important;">
              <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge badge-earth px-2.5 py-0.5 rounded-pill" style="font-size: 10.5px;">
                  <i class="ri-eye-line me-1"></i> VISI GEREJA
                </span>
              </div>
              <h4 class="font-serif fw-bold mb-2" style="color: var(--earth-dark); font-size: 19px;">Visi Pelayanan</h4>
              <blockquote class="mb-0 fst-italic leading-relaxed" style="color: #4D423B; font-size: 14.5px;">
                "{{ $churchProfile->visi ?: 'Mewujudkan jemaat yang bertumbuh dalam iman kepada Kristus, berakar kuat dalam firman Tuhan, dan berbuah lebat dalam kasih serta kesaksian bagi lingkungan sekitar.' }}"
              </blockquote>
            </div>

            <!-- KARTU MISI -->
            <div class="card-earth p-4 shadow-sm flex-grow-1" style="border-left: 5px solid var(--earth-accent) !important;">
              <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge badge-earth px-2.5 py-0.5 rounded-pill" style="font-size: 10.5px;">
                  <i class="ri-compass-3-line me-1"></i> MISI GEREJA
                </span>
              </div>
              <h4 class="font-serif fw-bold mb-3" style="color: var(--earth-dark); font-size: 19px;">Misi Pelayanan</h4>

              @php
                $misiItems = is_array($churchProfile->misi) 
                  ? $churchProfile->misi 
                  : (is_string($churchProfile->misi) ? explode("\n", $churchProfile->misi) : []);
              @endphp

              <ul class="list-unstyled mb-0 space-y-2" style="color: #4D423B; font-size: 14px;">
                @forelse($misiItems as $misi)
                  @if(trim($misi))
                    <li class="d-flex align-items-start gap-2.5 mb-2">
                      <div class="rounded-circle d-flex align-items-center justify-content-center text-white mt-0.5"
                        style="background: var(--earth-primary); width: 18px; height: 18px; font-size: 10.5px; flex-shrink: 0;">
                        <i class="ri-check-line"></i>
                      </div>
                      <span class="leading-relaxed">{{ trim($misi) }}</span>
                    </li>
                  @endif
                @empty
                  <li class="d-flex align-items-start gap-2.5 mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white mt-0.5"
                      style="background: var(--earth-primary); width: 18px; height: 18px; font-size: 10.5px; flex-shrink: 0;">
                      <i class="ri-check-line"></i>
                    </div>
                    <span>Menyelenggarakan ibadah yang hidup, tertib, dan memuliakan Allah Tri Tunggal.</span>
                  </li>
                  <li class="d-flex align-items-start gap-2.5 mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white mt-0.5"
                      style="background: var(--earth-primary); width: 18px; height: 18px; font-size: 10.5px; flex-shrink: 0;">
                      <i class="ri-check-line"></i>
                    </div>
                    <span>Menggembalakan dan memperlengkapi jemaat melalui pengajaran firman yang utuh.</span>
                  </li>
                  <li class="d-flex align-items-start gap-2.5 mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white mt-0.5"
                      style="background: var(--earth-primary); width: 18px; height: 18px; font-size: 10.5px; flex-shrink: 0;">
                      <i class="ri-check-line"></i>
                    </div>
                    <span>Menjalin persekutuan jemaat yang erat dan saling menguatkan dalam kasih persaudaraan.</span>
                  </li>
                  <li class="d-flex align-items-start gap-2.5">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white mt-0.5"
                      style="background: var(--earth-primary); width: 18px; height: 18px; font-size: 10.5px; flex-shrink: 0;">
                      <i class="ri-check-line"></i>
                    </div>
                    <span>Mengabarkan kabar baik keselamatan dan melayani masyarakat sekitar dengan kepedulian sosial.</span>
                  </li>
                @endforelse
              </ul>

            </div>

          </div>
        </div>

      </div>

    </div>
  </section>

  <!-- ========================================== -->
  <!-- 4. JADWAL IBADAH (5 WILAYAH & HIGHLIGHT)   -->
  <!-- ========================================== -->
  <section id="jadwal" class="py-5" style="background-color: var(--earth-surface-warm); padding-top: 75px !important; padding-bottom: 75px !important;">
    <div class="container">

      <div class="text-center mb-5">
        <span class="badge badge-earth px-3 py-1.5 rounded-pill text-uppercase" style="font-size: 11px; letter-spacing: 0.8px;">
          <i class="ri-calendar-schedule-line me-1"></i> Ibadah Mingguan & Sesi
        </span>
        <h2 class="display-6 font-serif fw-bold mt-2" style="color: var(--earth-dark);">Jadwal Ibadah 5 Wilayah</h2>
        <div class="mx-auto" style="width: 50px; height: 3px; background: var(--earth-primary); border-radius: 2px; margin-top: 10px; margin-bottom: 12px;"></div>
        <p class="small max-w-xl mx-auto" style="color: var(--earth-text-muted);">
          Informasi waktu ibadah raya umum, persekutuan doa, dan ibadah pemuda/remaja di seluruh wilayah GIA Surabaya.
        </p>
      </div>

      <!-- KARTU JADWAL PER WILAYAH (DYNAMIC DENGAN GOOGLE MAPS) -->
      <div class="row g-4 justify-content-center mb-5">
        @forelse($cabangs as $index => $cs)
          <div class="col-lg-4 col-md-6">
            <div class="card-earth p-4 h-100 shadow-sm d-flex flex-column justify-content-between" style="background: #FFFFFF !important;">
              <div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="badge badge-earth px-2.5 py-0.5 rounded-pill" style="font-size: 10.5px;">
                    Wilayah {{ $index + 1 }}
                  </span>
                  <a href="{{ $cs->google_maps_link }}" target="_blank" class="text-decoration-none small d-inline-flex align-items-center gap-1 text-primary fw-semibold" title="Buka di Google Maps" style="font-size: 11.5px;">
                    <i class="ri-map-pin-2-fill text-danger"></i>
                    <span>Maps</span>
                    <i class="ri-arrow-right-up-line" style="font-size: 11px;"></i>
                  </a>
                </div>

                <h5 class="fw-bold mb-1 font-serif" style="color: var(--earth-dark); font-size: 18px;">Wilayah {{ $cs->nama_cabang }}</h5>
                <p class="small mb-2.5" style="color: var(--earth-text-muted); font-size: 12.5px; min-height: 36px;">
                  <i class="ri-direction-line me-1 text-primary"></i>{{ $cs->lokasi ?: 'Surabaya' }}
                </p>

                <!-- INTERACTIVE GOOGLE MAPS EMBED -->
                <div class="rounded-3 overflow-hidden border mb-2.5 shadow-2xs position-relative" style="height: 140px; background: #EFE9E2;">
                  <iframe 
                    src="{{ $cs->google_maps_embed_url }}" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                  </iframe>
                </div>

                <!-- TOMBOL BUKA GOOGLE MAPS / RUTE -->
                <div class="mb-3">
                  <a href="{{ $cs->google_maps_link }}" target="_blank" class="btn btn-earth-light btn-sm rounded-pill w-100 d-inline-flex align-items-center justify-content-center gap-1.5" style="font-size: 12px; padding: 7px 12px;">
                    <i class="ri-navigation-fill text-danger"></i>
                    <span>Petunjuk Arah Google Maps</span>
                    <i class="ri-arrow-right-up-line small opacity-75"></i>
                  </a>
                </div>

                <!-- WAKTU & JADWAL IBADAH WILAYAH -->
                <div class="p-3 rounded-3 mb-2" style="background: var(--earth-surface-warm); border: 1px solid var(--earth-border); font-size: 13px;">
                  <div class="fw-bold mb-1.5" style="color: var(--earth-primary);"><i class="ri-time-line me-1"></i> Waktu Ibadah:</div>
                  @foreach($cs->jadwal_lines as $line)
                    <div class="mb-1" style="color: var(--earth-text);">
                      <i class="ri-check-line text-success me-1"></i>{{ $line }}
                    </div>
                  @endforeach
                </div>
              </div>

              <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between small" style="border-color: var(--earth-border) !important;">
                <span class="text-muted" style="font-size: 12px;"><i class="ri-map-pin-user-line me-1"></i> Tatap Muka</span>
                <a href="#streaming" class="text-decoration-none fw-bold" style="color: var(--earth-primary); font-size: 12.5px;">
                  Live Streaming <i class="ri-arrow-right-s-line"></i>
                </a>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12 text-center text-muted small py-4">
            Informasi wilayah sedang diperbarui oleh sekretariat gereja.
          </div>
        @endforelse
      </div>


      <!-- HIGHLIGHT UPCOMING SCHEDULE JIKA ADA -->
      @if($upcomingSchedules->isNotEmpty())
        @php $highlight = $upcomingSchedules->first(); @endphp
        <div class="card-earth p-4 p-md-4 shadow-sm overflow-hidden" style="border: 2px solid var(--earth-primary) !important; background: #FFFFFF !important;">
          <div class="row align-items-center g-3">
            <div class="col-lg-8">
              <span class="badge badge-earth px-2.5 py-0.5 rounded-pill mb-2" style="font-size: 10.5px;">
                <i class="ri-sparkling-line me-1"></i> IBADAH RAYA TERDEKAT
              </span>
              <h3 class="font-serif fw-bold mb-2" style="color: var(--earth-dark); font-size: 22px;">"{{ $highlight->tema ?: $highlight->nama_ibadah }}"</h3>
              <p class="small mb-2" style="color: var(--earth-primary); font-weight: 600;">
                <i class="ri-book-open-line me-1"></i> {{ $highlight->ayat ?: 'Firman Tuhan Minggu Ini' }}
              </p>
              <div class="d-flex flex-wrap gap-3 small" style="color: var(--earth-text-muted); font-size: 12.5px;">
                <div>
                  <i class="ri-calendar-line me-1" style="color: var(--earth-primary);"></i>
                  <strong>{{ \Carbon\Carbon::parse($highlight->tanggal)->translatedFormat('l, d F Y') }}</strong>
                </div>
                <div>
                  <i class="ri-map-pin-line me-1" style="color: var(--earth-primary);"></i>
                  <span>{{ $highlight->nama_cabang ?: 'Surabaya' }}</span>
                </div>
                @if($highlight->pengkhotbah)
                  <div>
                    <i class="ri-mic-line me-1" style="color: var(--earth-primary);"></i>
                    <span>Pelayan Firman: <strong>{{ $highlight->pengkhotbah }}</strong></span>
                  </div>
                @endif
              </div>
            </div>
            <div class="col-lg-4 text-lg-end">
              <a href="#streaming" class="btn btn-earth px-4 py-2.5 rounded-pill d-inline-flex align-items-center gap-2" style="font-size: 13px;">
                <i class="ri-live-line"></i>
                <span>Tonton Siaran</span>
              </a>
            </div>
          </div>
        </div>
      @endif

    </div>
  </section>

  <!-- ========================================== -->
  <!-- 5. LIVE STREAMING (DENGAN DETEKSI OTOMATIS) -->
  <!-- ========================================== -->
  <section id="streaming" class="py-5" style="background-color: var(--earth-bg); padding-top: 75px !important; padding-bottom: 85px !important;">
    <div class="container">

      <div class="text-center mb-5">
        <span class="badge px-3 py-1.5 rounded-pill text-uppercase d-inline-flex align-items-center gap-2"
          style="background: {{ $activeLiveStreams->count() > 0 ? '#FEE2E2' : 'var(--earth-accent-soft)' }}; 
                 color: {{ $activeLiveStreams->count() > 0 ? '#DC2626' : 'var(--earth-primary)' }}; 
                 border: 1px solid {{ $activeLiveStreams->count() > 0 ? '#FCA5A5' : 'rgba(140, 94, 60, 0.25)' }}; 
                 font-size: 11px; letter-spacing: 0.8px;">
          @if($activeLiveStreams->count() > 0)
            <span class="live-pulse-dot"></span> LIVE STREAMING AKTIF
          @else
            <i class="ri-broadcast-line"></i> LAYANAN STREAMING IBADAH
          @endif
        </span>

        <h2 class="display-6 font-serif fw-bold mt-2" style="color: var(--earth-dark);">Ibadah Online Gereja</h2>
        <div class="mx-auto" style="width: 50px; height: 3px; background: var(--earth-primary); border-radius: 2px; margin-top: 10px; margin-bottom: 12px;"></div>
        <p class="small max-w-xl mx-auto" style="color: var(--earth-text-muted);">
          Bagi jemaat yang berhalangan hadir secara fisik di gedung gereja, Anda dapat bersekutu dalam siaran langsung ibadah raya melalui kanal YouTube resmi kami.
        </p>
      </div>

      <!-- KONDISI A: JIKA ADA CABANG YANG SEDANG LIVE STREAMING -->
      @if($activeLiveStreams->count() > 0)
        <div class="mb-5">
          <div class="row g-4 justify-content-center">
            @foreach($activeLiveStreams as $live)
              <div class="col-lg-8">
                <div class="card-earth p-3.5 p-md-4 shadow-lg overflow-hidden" style="border: 2px solid #DC2626 !important; background: #FFFFFF !important;">
                  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <span class="badge bg-danger text-white px-3 py-1.5 rounded-pill d-inline-flex align-items-center gap-2 fw-bold" style="font-size: 11.5px;">
                      <span class="live-pulse-dot" style="background: #FFFFFF;"></span> SEDANG BERLANGSUNG (LIVE)
                    </span>
                    <span class="badge badge-earth-dark px-3 py-1 rounded-pill" style="font-size: 11.5px;">
                      <i class="ri-map-pin-line me-1"></i> {{ $live->cabang ? $live->cabang->nama_cabang : $live->nama_cabang }}
                    </span>
                  </div>

                  <!-- EMBED PLAYER -->
                  <div class="ratio ratio-16x9 mb-3 rounded-4 overflow-hidden shadow-sm" style="background: #000;">
                    <iframe src="{{ $live->youtube_embed_url }}?autoplay=1" title="{{ $live->judul_stream }}" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                  </div>

                  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-2">
                    <div>
                      <h4 class="font-serif fw-bold mb-1" style="color: var(--earth-dark); font-size: 18px;">{{ $live->judul_stream }}</h4>
                      <p class="small mb-0" style="color: var(--earth-text-muted);">
                        <i class="ri-time-line me-1" style="color: var(--earth-primary);"></i> {{ $live->jadwal_tayang ?: 'Ibadah Raya Minggu' }}
                      </p>
                    </div>
                    <a href="{{ $live->youtube_url }}" target="_blank" class="btn btn-danger px-4 py-2 rounded-pill fw-bold d-inline-flex align-items-center gap-2" style="font-size: 12.5px;">
                      <i class="ri-youtube-fill"></i> Tonton di YouTube
                    </a>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @else
        <!-- KONDISI B: JIKA SEMUA CABANG OFFLINE -->
        <div class="max-w-3xl mx-auto mb-5">
          <div class="card-earth p-4 p-md-5 text-center shadow-sm" style="background: #FFFFFF !important; border-top: 4px solid var(--earth-accent) !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
              style="width: 58px; height: 58px; background: var(--earth-surface-warm); color: var(--earth-primary); border: 1px solid var(--earth-border);">
              <i class="ri-broadcast-line fs-24"></i>
            </div>

            <h4 class="font-serif fw-bold mb-2" style="color: var(--earth-dark); font-size: 20px;">Saat Ini Tidak Ada Siaran Langsung (Offline)</h4>
            <p class="small leading-relaxed mb-4" style="color: var(--earth-text-muted); max-width: 520px; margin-left: auto; margin-right: auto; font-size: 13.5px;">
              Siaran langsung ibadah raya ditayangkan setiap hari Minggu sesuai jam ibadah masing-masing wilayah. Anda tetap dapat menyaksikan tayangan ulang ibadah sebelumnya atau mengunjungi kanal YouTube resmi kami di bawah ini.
            </p>

            <div class="d-inline-flex align-items-center gap-2 p-2 px-3 rounded-pill" style="background: var(--earth-surface-warm); border: 1px solid var(--earth-border);">
              <i class="ri-information-line" style="color: var(--earth-primary);"></i>
              <span class="small" style="color: var(--earth-dark); font-weight: 600; font-size: 12.5px;">Jadwal Siaran Berikutnya: Minggu, 06.00 & 08.00 WIB</span>
            </div>
          </div>
        </div>
      @endif

      <!-- DAFTAR KANAL DAN ARSIP STREAMING 5 WILAYAH -->
      <div class="mt-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <div>
            <h5 class="font-serif fw-bold mb-0" style="color: var(--earth-dark); font-size: 18px;">Kanal Siaran 5 Wilayah Gereja</h5>
            <small style="color: var(--earth-text-muted); font-size: 12.5px;">Pilih wilayah untuk menyaksikan rekaman atau siaran ibadah</small>
          </div>
          @if($churchProfile->youtube_channel_url)
            <a href="{{ $churchProfile->youtube_channel_url }}" target="_blank" class="btn btn-earth-outline btn-sm px-3 py-1.5 rounded-pill d-inline-flex align-items-center gap-1.5" style="font-size: 12px;">
              <i class="ri-youtube-line"></i> Kanal Pusat
            </a>
          @endif
        </div>

        <div class="row g-4">
          @foreach($streams as $stream)
            <div class="col-xl-4 col-md-6">
              <div class="card-earth p-3.5 p-md-4 h-100 shadow-sm d-flex flex-column justify-content-between" style="background: #FFFFFF !important;">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2.5">
                    <span class="badge px-2.5 py-0.5 rounded-pill fw-bold"
                      style="font-size: 10.5px; {{ $stream->is_live ? 'background: #FEE2E2; color: #DC2626;' : 'background: var(--earth-surface-warm); color: var(--earth-text-muted);' }}">
                      @if($stream->is_live)
                        <span class="live-pulse-dot me-1"></span> LIVE SEKARANG
                      @else
                        🎬 ARSIP & REPLAY
                      @endif
                    </span>
                    <small class="fw-bold" style="color: var(--earth-primary); font-size: 12px;">{{ $stream->cabang ? $stream->cabang->nama_cabang : $stream->nama_cabang }}</small>
                  </div>

                  <div class="ratio ratio-16x9 mb-3 rounded-3 overflow-hidden shadow-sm" style="background: #111;">
                    <iframe src="{{ $stream->youtube_embed_url }}" title="{{ $stream->judul_stream }}" allowfullscreen></iframe>
                  </div>

                  <h5 class="fw-bold mb-1 font-serif" style="color: var(--earth-dark); font-size: 15.5px;">{{ $stream->judul_stream }}</h5>
                  <p class="small mb-0" style="color: var(--earth-text-muted); font-size: 12px;">
                    <i class="ri-time-line me-1" style="color: var(--earth-primary);"></i> {{ $stream->jadwal_tayang ?: 'Setiap Minggu' }}
                  </p>
                </div>

                <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between" style="border-color: var(--earth-border) !important;">
                  <span class="small" style="color: var(--earth-text-muted); font-size: 12px;"><i class="ri-tv-line me-1"></i> Ibadah Online</span>
                  <a href="{{ $stream->youtube_url }}" target="_blank" class="small fw-bold text-decoration-none d-inline-flex align-items-center gap-1" style="color: #DC2626; font-size: 12px;">
                    <i class="ri-youtube-line"></i> Buka YouTube
                  </a>
                </div>
              </div>
            </div>
          @endforeach
        </div>

      </div>

    </div>
  </section>

  <!-- ======================================================== -->
  <!-- 📰 SECTION ARTIKEL & RENUNGAN JEMAAT (PALING BAWAH)     -->
  <!-- ======================================================== -->
  <section class="section-gap" style="background: #FFFFFF; border-top: 1px solid rgba(140, 94, 60, 0.12);">
    <div class="container">
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4 mb-md-5">
        <div>
          <span class="badge px-3 py-1.5 rounded-pill text-uppercase d-inline-flex align-items-center gap-1.5 mb-2"
            style="background: var(--earth-accent-soft); color: var(--earth-primary); font-size: 11px; font-weight: 700; letter-spacing: 0.8px;">
            <i class="ri-newspaper-line"></i> Kabar & Firman Tuhan
          </span>
          <h2 class="font-serif fw-bold mb-1" style="color: var(--earth-dark); font-size: clamp(24px, 3.5vw, 36px);">
            Artikel & Renungan Terbaru
          </h2>
          <p class="mb-0" style="color: var(--earth-text-muted); font-size: 14.5px;">
            Inspirasi firman, pengajaran rohani, dan kabar sukacita persekutuan jemaat.
          </p>
        </div>

        <a href="{{ Route::has('articles.index') ? route('articles.index') : url('/artikel') }}" class="btn btn-earth-light px-4 py-2 rounded-pill d-inline-flex align-items-center gap-2 fw-bold" style="font-size: 13px;">
          <span>Lihat Semua Artikel</span>
          <i class="ri-arrow-right-line"></i>
        </a>
      </div>

      <div class="row g-4">
        @forelse($latestArticles as $article)
          <div class="col-lg-4 col-md-6">
            <div class="card-earth h-100 p-0 overflow-hidden shadow-sm d-flex flex-column justify-content-between transition-hover"
              style="background: #FFFFFF; border: 1px solid rgba(140, 94, 60, 0.16); border-radius: 20px;">
              <div>
                <!-- Banner Image -->
                <div style="position: relative; overflow: hidden; height: 210px;">
                  <img src="{{ $article->banner_url }}" alt="{{ $article->judul }}"
                    style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;"
                    onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                  <span class="badge position-absolute top-0 start-0 m-3 px-3 py-1.5 rounded-pill fw-bold"
                    style="background: rgba(28, 23, 20, 0.75); color: #F5EFEB; backdrop-filter: blur(4px); font-size: 11px;">
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
                    <a href="{{ Route::has('articles.show') ? route('articles.show', $article->slug) : url('/artikel/'.$article->slug) }}" class="text-decoration-none" style="color: var(--earth-dark);">
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
                  <span title="{{ $article->likes->count() }} orang menyukai"><i class="ri-heart-line text-danger me-1"></i> {{ $article->likes->count() }}</span>
                  <span title="{{ $article->comments->count() }} komentar"><i class="ri-chat-3-line text-primary me-1"></i> {{ $article->comments->count() }}</span>
                </div>

                <a href="{{ Route::has('articles.show') ? route('articles.show', $article->slug) : url('/artikel/'.$article->slug) }}" class="fw-bold small text-decoration-none d-inline-flex align-items-center gap-1" style="color: var(--earth-primary); font-size: 13px;">
                  <span>Baca</span>
                  <i class="ri-arrow-right-s-line"></i>
                </a>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12 text-center py-5">
            <div class="p-5 rounded-4" style="background: var(--earth-surface-warm); border: 1px dashed rgba(140, 94, 60, 0.25);">
              <i class="ri-book-read-line" style="font-size: 36px; color: var(--earth-primary);"></i>
              <h5 class="fw-bold font-serif mt-2 mb-1" style="color: var(--earth-dark);">Belum Ada Artikel Dipublikasikan</h5>
              <p class="text-muted small mb-0">Artikel dan renungan rohani terbaru akan segera hadir.</p>
            </div>
          </div>
        @endforelse
      </div>
    </div>
  </section>

@endsection

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const sliderElem = document.querySelector('.banner-two__slider');
    if (sliderElem && typeof Swiper !== 'undefined') {
      if (sliderElem.swiper) {
        sliderElem.swiper.destroy(true, true);
      }
      new Swiper('.banner-two__slider', {
        loop: true,
        speed: 1000,
        autoplay: {
          delay: 6000,
          disableOnInteraction: false,
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
        observer: true,
        observeParents: true,
      });
    }
  });
</script>
