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
        <div class="swiper-slide" style="position: relative; overflow: hidden; min-height: 560px;">
          @php
            $fotoGerejaUrl = $churchProfile->foto_gereja 
              ? asset('img/admin/church/' . $churchProfile->foto_gereja) 
              : asset('homepage/assets/images/banner/banner-image1.jpg');
          @endphp
          <div class="slide-bg"
            style="background-image: linear-gradient(rgba(28, 23, 20, 0.72), rgba(28, 23, 20, 0.82)), url('{{ $fotoGerejaUrl }}'); 
                   background-size: cover; 
                   background-position: center; 
                   position: absolute; 
                   inset: 0; 
                   z-index: 1;">
          </div>

          <div class="container h-100 d-flex align-items-center" style="position: relative; z-index: 2; padding: 110px 15px 120px;">
            <div class="banner-two__content max-w-2xl text-white">

              <span class="badge px-3.5 py-2 mb-3 rounded-pill text-uppercase d-inline-flex align-items-center gap-2"
                style="background: rgba(212, 163, 115, 0.22); color: #F5EFEB; border: 1px solid rgba(212, 163, 115, 0.45); font-size: 12px; letter-spacing: 1px;">
                <i class="ri-home-4-line" style="color: var(--earth-accent);"></i> Selamat Datang di Rumah Tuhan
              </span>

              <h1 class="text-white mb-3 display-4 font-serif fw-bold" style="line-height: 1.15; letter-spacing: -0.02em;">
                {{ $churchProfile->nama_gereja ?: 'Gereja Isa Almasih Surabaya' }}
              </h1>

              <p class="fs-18 mb-4 fw-normal leading-relaxed" style="color: #E6DDD4; max-width: 600px;">
                {{ $churchProfile->tagline ?: 'Bertumbuh dalam Kristus, Berakar dalam Firman, dan Berbuah bagi Sesama.' }}
              </p>

              <div class="d-flex flex-wrap gap-3">
                <a href="#tentang" class="btn btn-earth px-4 py-3 rounded-pill d-inline-flex align-items-center gap-2" style="font-size: 13.5px;">
                  <i class="ri-information-line"></i>
                  <span>Tentang Gereja</span>
                </a>
                <a href="#jadwal" class="btn btn-earth-light px-4 py-3 rounded-pill d-inline-flex align-items-center gap-2" style="font-size: 13.5px;">
                  <i class="ri-calendar-schedule-line"></i>
                  <span>Jadwal Ibadah</span>
                </a>
                <a href="#streaming" class="btn btn-outline-light px-4 py-3 rounded-pill d-inline-flex align-items-center gap-2" style="font-size: 13.5px; border-color: rgba(255,255,255,0.4);">
                  <i class="ri-live-line" style="color: #F87171;"></i>
                  <span>Live Streaming</span>
                </a>
              </div>

            </div>
          </div>
        </div>

        <!-- ======================================================== -->
        <!-- BANNER 2+: PREVIEW EVENT ONGOING & MENDATANG (CMS)       -->
        <!-- ======================================================== -->
        @foreach ($sliderEvents as $event)
          <div class="swiper-slide" style="position: relative; overflow: hidden; min-height: 560px;">
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

            <div class="container h-100 d-flex align-items-center" style="position: relative; z-index: 2; padding: 110px 15px 120px;">
              <div class="banner-two__content max-w-2xl text-white">

                <span class="badge px-3.5 py-2 mb-3 rounded-pill d-inline-flex align-items-center gap-2"
                  style="background: {{ $isOngoing ? 'rgba(34, 197, 94, 0.25)' : 'rgba(212, 163, 115, 0.25)' }}; 
                         color: #FFFFFF; 
                         border: 1px solid {{ $isOngoing ? 'rgba(34, 197, 94, 0.5)' : 'rgba(212, 163, 115, 0.5)' }}; 
                         font-size: 12px; letter-spacing: 0.8px;">
                  @if($isOngoing)
                    <span class="live-pulse-dot" style="background: #22C55E;"></span> EVENT SEDANG BERLANGSUNG
                  @else
                    <i class="ri-calendar-event-line" style="color: var(--earth-accent);"></i> AGENDA KEGIATAN GEREJA
                  @endif
                  · {{ $event->cabang ? strtoupper($event->cabang->nama_cabang) : 'SEMUA CABANG' }}
                </span>

                <div class="d-flex align-items-center gap-2 mb-2" style="color: #D4C3B5; font-size: 14.5px;">
                  <i class="ri-time-line" style="color: var(--earth-accent);"></i>
                  <span>{{ \Carbon\Carbon::parse($event->waktu_mulai)->translatedFormat('l, d F Y') }} · {{ \Carbon\Carbon::parse($event->waktu_mulai)->format('H:i') }} WIB</span>
                </div>

                <h1 class="text-white mb-3 display-5 font-serif fw-bold" style="line-height: 1.15;">
                  {{ $event->nama_acara }}
                </h1>

                <p class="fs-16 mb-4 fw-normal leading-relaxed" style="color: #E6DDD4;">
                  <i class="ri-map-pin-2-line me-1" style="color: var(--earth-accent);"></i>
                  {{ $event->lokasi_spesifik ?: 'Gereja Isa Almasih Surabaya' }}
                </p>

                <div class="d-flex flex-wrap gap-3">
                  <a href="{{ url('/event') }}" class="btn btn-earth px-4 py-3 rounded-pill d-inline-flex align-items-center gap-2" style="font-size: 13.5px;">
                    <i class="ri-calendar-todo-line"></i>
                    <span>Lihat Detail Agenda</span>
                  </a>
                  <a href="{{ url('/event') }}" class="btn btn-outline-light px-4 py-3 rounded-pill d-inline-flex align-items-center gap-2" style="font-size: 13.5px; border-color: rgba(255,255,255,0.4);">
                    <i class="ri-calendar-line"></i>
                    <span>Semua Event</span>
                  </a>
                </div>

              </div>
            </div>
          </div>
        @endforeach

      </div>
      <div class="swiper-button-next text-white d-none d-md-flex" style="right: 30px; z-index: 10;"></div>
      <div class="swiper-button-prev text-white d-none d-md-flex" style="left: 30px; z-index: 10;"></div>
    </div>
  </section>

  <!-- ========================================== -->
  <!-- 2. QUICK SHORTCUT CARDS (OVERLAY BAR)      -->
  <!-- ========================================== -->
  <section class="position-relative" style="margin-top: -50px; z-index: 20;">
    <div class="container">
      <div class="row g-3">

        <!-- CARD 1: TENTANG GEREJA -->
        <div class="col-lg-3 col-md-6">
          <a href="#tentang" class="text-decoration-none h-100 d-block">
            <div class="card-earth p-4 h-100 d-flex flex-column justify-between shadow-sm">
              <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                    style="background: var(--earth-primary); width: 44px; height: 44px;">
                    <i class="ri-church-line fs-20"></i>
                  </div>
                  <span class="badge badge-earth px-2.5 py-1 rounded-pill" style="font-size: 11px;">
                    Profil
                  </span>
                </div>
                <h5 class="fw-bold mb-1" style="color: var(--earth-dark); font-size: 17px;">Tentang Gereja</h5>
                <p class="small mb-0" style="color: var(--earth-text-muted);">Mengenal sejarah singkat, visi, dan misi pelayanan GIA Surabaya.</p>
              </div>
              <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between small fw-bold" style="border-color: var(--earth-border) !important; color: var(--earth-primary);">
                <span>Selengkapnya</span>
                <i class="ri-arrow-right-line"></i>
              </div>
            </div>
          </a>
        </div>

        <!-- CARD 2: JADWAL IBADAH -->
        <div class="col-lg-3 col-md-6">
          <a href="#jadwal" class="text-decoration-none h-100 d-block">
            <div class="card-earth p-4 h-100 d-flex flex-column justify-between shadow-sm">
              <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                    style="background: #A06A42; width: 44px; height: 44px;">
                    <i class="ri-calendar-schedule-line fs-20"></i>
                  </div>
                  <span class="badge badge-earth px-2.5 py-1 rounded-pill" style="font-size: 11px;">
                    5 Cabang
                  </span>
                </div>
                <h5 class="fw-bold mb-1" style="color: var(--earth-dark); font-size: 17px;">Jadwal Ibadah</h5>
                <p class="small mb-0" style="color: var(--earth-text-muted);">Waktu ibadah raya minggu dan sesi doa di setiap cabang jemaat.</p>
              </div>
              <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between small fw-bold" style="border-color: var(--earth-border) !important; color: var(--earth-primary);">
                <span>Lihat Jadwal</span>
                <i class="ri-arrow-right-line"></i>
              </div>
            </div>
          </a>
        </div>

        <!-- CARD 3: LIVE STREAMING -->
        <div class="col-lg-3 col-md-6">
          <a href="#streaming" class="text-decoration-none h-100 d-block">
            <div class="card-earth p-4 h-100 d-flex flex-column justify-between shadow-sm">
              <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                    style="background: {{ $activeLiveStreams->count() > 0 ? '#DC2626' : '#6B5E55' }}; width: 44px; height: 44px;">
                    <i class="ri-live-line fs-20"></i>
                  </div>
                  <span class="badge px-2.5 py-1 rounded-pill fw-bold"
                    style="font-size: 11px; {{ $activeLiveStreams->count() > 0 ? 'background: #FEE2E2; color: #DC2626; border: 1px solid #FCA5A5;' : 'background: #F3ECE5; color: #786C64;' }}">
                    @if($activeLiveStreams->count() > 0)
                      🔴 {{ $activeLiveStreams->count() }} Live
                    @else
                      Offline
                    @endif
                  </span>
                </div>
                <h5 class="fw-bold mb-1" style="color: var(--earth-dark); font-size: 17px;">Live Streaming</h5>
                <p class="small mb-0" style="color: var(--earth-text-muted);">Siaran langsung ibadah online dan arsip khotbah YouTube.</p>
              </div>
              <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between small fw-bold" style="border-color: var(--earth-border) !important; color: var(--earth-primary);">
                <span>Buka Siaran</span>
                <i class="ri-arrow-right-line"></i>
              </div>
            </div>
          </a>
        </div>

        <!-- CARD 4: WARTA JEMAAT -->
        <div class="col-lg-3 col-md-6">
          <a href="{{ url('/warta') }}" class="text-decoration-none h-100 d-block">
            <div class="card-earth p-4 h-100 d-flex flex-column justify-between shadow-sm">
              <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                    style="background: #687968; width: 44px; height: 44px;">
                    <i class="ri-newspaper-line fs-20"></i>
                  </div>
                  <span class="badge badge-earth px-2.5 py-1 rounded-pill" style="font-size: 11px;">
                    E-Bulletin
                  </span>
                </div>
                <h5 class="fw-bold mb-1" style="color: var(--earth-dark); font-size: 17px;">Warta Jemaat</h5>
                <p class="small mb-0" style="color: var(--earth-text-muted);">Informasi pokok doa, persembahan, dan buletin digital mingguan.</p>
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
  <section id="tentang" class="py-5" style="padding-top: 90px !important; padding-bottom: 80px !important;">
    <div class="container">

      <div class="text-center mb-5">
        <span class="badge badge-earth px-3.5 py-1.5 rounded-pill text-uppercase" style="font-size: 11px; letter-spacing: 0.8px;">
          <i class="ri-shield-cross-line me-1"></i> Profil Jemaat
        </span>
        <h2 class="display-6 font-serif fw-bold mt-2" style="color: var(--earth-dark);">Tentang Gereja Kami</h2>
        <div class="mx-auto" style="width: 50px; height: 3px; background: var(--earth-primary); border-radius: 2px; margin-top: 12px; margin-bottom: 14px;"></div>
        <p class="small max-w-xl mx-auto" style="color: var(--earth-text-muted);">
          Gereja Isa Almasih Surabaya berakar dalam firman Tuhan dan berkomitmen melayani jemaat di berbagai penjuru kota Surabaya.
        </p>
      </div>

      <div class="row g-4 align-items-stretch">

        <!-- KIRI: FOTO & TENTANG GEREJA -->
        <div class="col-lg-6">
          <div class="card-earth p-4 p-md-5 h-100 shadow-sm d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex align-items-center gap-3 mb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                  style="background: var(--earth-primary); width: 50px; height: 50px; flex-shrink: 0;">
                  <i class="ri-building-4-line fs-22"></i>
                </div>
                <div>
                  <h4 class="font-serif fw-bold mb-0" style="color: var(--earth-dark);">{{ $churchProfile->nama_gereja ?: 'Gereja Isa Almasih' }}</h4>
                  <small style="color: var(--earth-accent); font-weight: 600;">Jemaat Surabaya · 5 Cabang</small>
                </div>
              </div>

              <div class="mb-4 text-justify leading-relaxed" style="color: #4D423B; font-size: 15px;">
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
              <div class="small" style="color: var(--earth-text-muted);">
                <i class="ri-map-pin-line me-1" style="color: var(--earth-primary);"></i>
                <span>{{ $churchProfile->alamat_pusat ?: 'Jl. Darmo / Surabaya' }}</span>
              </div>
              <div class="small fw-bold" style="color: var(--earth-primary);">
                <i class="ri-phone-line me-1"></i>
                <span>{{ $churchProfile->telepon ?: '(031) 567-890' }}</span>
              </div>
            </div>

          </div>
        </div>

        <!-- KANAN: VISI & MISI -->
        <div class="col-lg-6" id="visimisi">
          <div class="d-flex flex-column gap-4 h-100">

            <!-- KARTU VISI -->
            <div class="card-earth p-4 p-md-4 shadow-sm" style="border-left: 5px solid var(--earth-primary) !important;">
              <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge badge-earth px-2.5 py-1 rounded-pill" style="font-size: 11px;">
                  <i class="ri-eye-line me-1"></i> VISI GEREJA
                </span>
              </div>
              <h4 class="font-serif fw-bold mb-2" style="color: var(--earth-dark);">Visi Pelayanan</h4>
              <blockquote class="mb-0 fst-italic" style="color: #4D423B; font-size: 15.5px; line-height: 1.6;">
                "{{ $churchProfile->visi ?: 'Mewujudkan jemaat yang bertumbuh dalam iman kepada Kristus, berakar kuat dalam firman Tuhan, dan berbuah lebat dalam kasih serta kesaksian bagi lingkungan sekitar.' }}"
              </blockquote>
            </div>

            <!-- KARTU MISI -->
            <div class="card-earth p-4 p-md-4 shadow-sm flex-grow-1" style="border-left: 5px solid var(--earth-accent) !important;">
              <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge badge-earth px-2.5 py-1 rounded-pill" style="font-size: 11px;">
                  <i class="ri-compass-3-line me-1"></i> MISI GEREJA
                </span>
              </div>
              <h4 class="font-serif fw-bold mb-3" style="color: var(--earth-dark);">Misi Pelayanan</h4>

              @php
                $misiItems = is_array($churchProfile->misi) 
                  ? $churchProfile->misi 
                  : (is_string($churchProfile->misi) ? explode("\n", $churchProfile->misi) : []);
              @endphp

              <ul class="list-unstyled mb-0 space-y-2.5" style="color: #4D423B; font-size: 14.5px;">
                @forelse($misiItems as $misi)
                  @if(trim($misi))
                    <li class="d-flex align-items-start gap-2.5 mb-2">
                      <div class="rounded-circle d-flex align-items-center justify-content-center text-white mt-1"
                        style="background: var(--earth-primary); width: 20px; height: 20px; font-size: 11px; flex-shrink: 0;">
                        <i class="ri-check-line"></i>
                      </div>
                      <span>{{ trim($misi) }}</span>
                    </li>
                  @endif
                @empty
                  <li class="d-flex align-items-start gap-2.5 mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white mt-1"
                      style="background: var(--earth-primary); width: 20px; height: 20px; font-size: 11px; flex-shrink: 0;">
                      <i class="ri-check-line"></i>
                    </div>
                    <span>Menyelenggarakan ibadah yang hidup, tertib, dan memuliakan Allah Tri Tunggal.</span>
                  </li>
                  <li class="d-flex align-items-start gap-2.5 mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white mt-1"
                      style="background: var(--earth-primary); width: 20px; height: 20px; font-size: 11px; flex-shrink: 0;">
                      <i class="ri-check-line"></i>
                    </div>
                    <span>Menggembalakan dan memperlengkapi jemaat melalui pengajaran firman yang utuh.</span>
                  </li>
                  <li class="d-flex align-items-start gap-2.5 mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white mt-1"
                      style="background: var(--earth-primary); width: 20px; height: 20px; font-size: 11px; flex-shrink: 0;">
                      <i class="ri-check-line"></i>
                    </div>
                    <span>Menjalin persekutuan jemaat yang erat dan saling menguatkan dalam kasih persaudaraan.</span>
                  </li>
                  <li class="d-flex align-items-start gap-2.5">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white mt-1"
                      style="background: var(--earth-primary); width: 20px; height: 20px; font-size: 11px; flex-shrink: 0;">
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
  <!-- 4. JADWAL IBADAH (5 CABANG & HIGHLIGHT)    -->
  <!-- ========================================== -->
  <section id="jadwal" class="py-5" style="background-color: var(--earth-surface-warm); padding-top: 80px !important; padding-bottom: 80px !important;">
    <div class="container">

      <div class="text-center mb-5">
        <span class="badge badge-earth px-3.5 py-1.5 rounded-pill text-uppercase" style="font-size: 11px; letter-spacing: 0.8px;">
          <i class="ri-calendar-schedule-line me-1"></i> Ibadah Mingguan & Sesi
        </span>
        <h2 class="display-6 font-serif fw-bold mt-2" style="color: var(--earth-dark);">Jadwal Ibadah 5 Cabang</h2>
        <div class="mx-auto" style="width: 50px; height: 3px; background: var(--earth-primary); border-radius: 2px; margin-top: 12px; margin-bottom: 14px;"></div>
        <p class="small max-w-xl mx-auto" style="color: var(--earth-text-muted);">
          Informasi waktu ibadah raya umum, persekutuan doa, dan ibadah pemuda/remaja di seluruh cabang GIA Surabaya.
        </p>
      </div>

      <!-- KARTU JADWAL PER CABANG (GRID 5 CABANG) -->
      <div class="row g-4 mb-5">
        @php
          $cabangSchedules = [
            ['nama' => 'Cabang Darmo (Pusat)', 'alamat' => 'Jl. Darmo No. 45, Surabaya', 'sesi1' => 'Ibadah 1: Minggu 06.00 WIB', 'sesi2' => 'Ibadah 2: Minggu 08.00 WIB', 'sesi3' => 'Ibadah 3: Minggu 10.00 WIB', 'doa' => 'Doa Malam: Rabu 19.00 WIB'],
            ['nama' => 'Cabang Bromo', 'alamat' => 'Jl. Bromo No. 12, Surabaya', 'sesi1' => 'Ibadah Pagi: Minggu 07.00 WIB', 'sesi2' => 'Ibadah Siang: Minggu 09.30 WIB', 'sesi3' => 'Ibadah Sore: Minggu 17.00 WIB', 'doa' => 'Doa Syafaat: Kamis 18.30 WIB'],
            ['nama' => 'Cabang Koblen', 'alamat' => 'Jl. Koblen Kidul No. 8, Surabaya', 'sesi1' => 'Ibadah Pagi: Minggu 06.30 WIB', 'sesi2' => 'Ibadah Siang: Minggu 09.00 WIB', 'sesi3' => 'Ibadah Remaja: Minggu 16.00 WIB', 'doa' => 'Doa Tengah Minggu: Rabu 18.30 WIB'],
            ['nama' => 'Cabang Gateway', 'alamat' => 'Kompleks Gateway Waru, Sidoarjo - Surabaya', 'sesi1' => 'Ibadah 1: Minggu 08.00 WIB', 'sesi2' => 'Ibadah 2: Minggu 10.30 WIB', 'sesi3' => 'Ibadah Pemuda: Sabtu 18.00 WIB', 'doa' => 'Doa Bersama: Rabu 19.00 WIB'],
            ['nama' => 'Cabang Tengger', 'alamat' => 'Jl. Raya Tengger Kandangan No. 20, Surabaya', 'sesi1' => 'Ibadah Pagi: Minggu 07.30 WIB', 'sesi2' => 'Ibadah Siang: Minggu 10.00 WIB', 'sesi3' => 'Ibadah Sore: Minggu 16.30 WIB', 'doa' => 'Doa Semalam: Jumat 19.00 WIB'],
          ];
        @endphp

        @foreach($cabangSchedules as $index => $cs)
          <div class="col-lg-4 col-md-6">
            <div class="card-earth p-4 h-100 shadow-sm d-flex flex-column justify-content-between" style="background: #FFFFFF !important;">
              <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <span class="badge badge-earth px-2.5 py-1 rounded-pill" style="font-size: 11px;">
                    Cabang {{ $index + 1 }}
                  </span>
                  <i class="ri-map-pin-2-line" style="color: var(--earth-primary); font-size: 18px;"></i>
                </div>

                <h5 class="fw-bold mb-1 font-serif" style="color: var(--earth-dark); font-size: 18px;">{{ $cs['nama'] }}</h5>
                <p class="small mb-3" style="color: var(--earth-text-muted);"><i class="ri-direction-line me-1"></i>{{ $cs['alamat'] }}</p>

                <div class="p-3 rounded-3 mb-2" style="background: var(--earth-surface-warm); border: 1px solid var(--earth-border); font-size: 13px;">
                  <div class="fw-bold mb-1.5" style="color: var(--earth-primary);"><i class="ri-time-line me-1"></i> Waktu Ibadah:</div>
                  <div class="mb-1" style="color: var(--earth-text);"><i class="ri-check-line text-success me-1"></i>{{ $cs['sesi1'] }}</div>
                  <div class="mb-1" style="color: var(--earth-text);"><i class="ri-check-line text-success me-1"></i>{{ $cs['sesi2'] }}</div>
                  <div class="mb-1" style="color: var(--earth-text);"><i class="ri-check-line text-success me-1"></i>{{ $cs['sesi3'] }}</div>
                  <div style="color: var(--earth-text);"><i class="ri-heart-pulse-line me-1" style="color: var(--earth-accent);"></i>{{ $cs['doa'] }}</div>
                </div>
              </div>

              <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between small" style="border-color: var(--earth-border) !important;">
                <span class="text-muted"><i class="ri-map-pin-user-line me-1"></i> Kehadiran Tatap Muka</span>
                <a href="#streaming" class="text-decoration-none fw-bold" style="color: var(--earth-primary);">
                  Streaming <i class="ri-arrow-right-s-line"></i>
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="text-center mb-5">
        <a href="{{ route('ibadah.public') }}" class="btn btn-earth-outline btn-sm px-4 py-2.5 rounded-pill d-inline-flex align-items-center gap-2">
          <i class="ri-book-open-line"></i>
          <span>Buka Arsip & Detail Liturgi Pelayanan</span>
        </a>
      </div>

      <!-- HIGHLIGHT UPCOMING SCHEDULE JIKA ADA -->
      @if($upcomingSchedules->isNotEmpty())
        @php $highlight = $upcomingSchedules->first(); @endphp
        <div class="card-earth p-4 p-md-5 shadow-sm overflow-hidden" style="border: 2px solid var(--earth-primary) !important; background: #FFFFFF !important;">
          <div class="row align-items-center g-4">
            <div class="col-lg-8">
              <span class="badge badge-earth px-3 py-1 rounded-pill mb-2" style="font-size: 11px;">
                <i class="ri-sparkling-line me-1"></i> IBADAH RAYA TERDEKAT
              </span>
              <h3 class="font-serif fw-bold mb-2" style="color: var(--earth-dark);">"{{ $highlight->tema ?: $highlight->nama_ibadah }}"</h3>
              <p class="small mb-3" style="color: var(--earth-primary); font-weight: 600;">
                <i class="ri-book-open-line me-1"></i> {{ $highlight->ayat ?: 'Firman Tuhan Minggu Ini' }}
              </p>
              <div class="d-flex flex-wrap gap-4 small" style="color: var(--earth-text-muted);">
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
              <a href="#streaming" class="btn btn-earth px-4 py-2.5 rounded-pill d-inline-flex align-items-center gap-2">
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
  <section id="streaming" class="py-5" style="background-color: var(--earth-bg); padding-top: 80px !important; padding-bottom: 90px !important;">
    <div class="container">

      <div class="text-center mb-5">
        <span class="badge px-3.5 py-1.5 rounded-pill text-uppercase d-inline-flex align-items-center gap-2"
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
        <div class="mx-auto" style="width: 50px; height: 3px; background: var(--earth-primary); border-radius: 2px; margin-top: 12px; margin-bottom: 14px;"></div>
        <p class="small max-w-xl mx-auto" style="color: var(--earth-text-muted);">
          Bagi jemaat yang berhalangan hadir secara fisik di gedung gereja, Anda dapat bersekutu dalam siaran langsung ibadah raya melalui kanal YouTube resmi kami.
        </p>
      </div>

      <!-- ======================================================== -->
      <!-- KONDISI A: JIKA ADA CABANG YANG SEDANG LIVE STREAMING   -->
      <!-- ======================================================== -->
      @if($activeLiveStreams->count() > 0)
        <div class="mb-5">
          <div class="row g-4 justify-content-center">
            @foreach($activeLiveStreams as $live)
              <div class="col-lg-8">
                <div class="card-earth p-4 p-md-4 shadow-lg overflow-hidden" style="border: 2px solid #DC2626 !important; background: #FFFFFF !important;">
                  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <span class="badge bg-danger text-white px-3 py-1.5 rounded-pill d-inline-flex align-items-center gap-2 fw-bold" style="font-size: 12px;">
                      <span class="live-pulse-dot" style="background: #FFFFFF;"></span> SEDANG BERLANGSUNG (LIVE)
                    </span>
                    <span class="badge badge-earth-dark px-3 py-1 rounded-pill" style="font-size: 12px;">
                      <i class="ri-map-pin-line me-1"></i> {{ $live->cabang ? $live->cabang->nama_cabang : $live->nama_cabang }}
                    </span>
                  </div>

                  <!-- EMBED PLAYER -->
                  <div class="ratio ratio-16x9 mb-3 rounded-4 overflow-hidden shadow-sm" style="background: #000;">
                    <iframe src="{{ $live->youtube_embed_url }}?autoplay=1" title="{{ $live->judul_stream }}" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                  </div>

                  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-2">
                    <div>
                      <h4 class="font-serif fw-bold mb-1" style="color: var(--earth-dark);">{{ $live->judul_stream }}</h4>
                      <p class="small mb-0" style="color: var(--earth-text-muted);">
                        <i class="ri-time-line me-1" style="color: var(--earth-primary);"></i> {{ $live->jadwal_tayang ?: 'Ibadah Raya Minggu' }}
                      </p>
                    </div>
                    <a href="{{ $live->youtube_url }}" target="_blank" class="btn btn-danger px-4 py-2 rounded-pill fw-bold d-inline-flex align-items-center gap-2" style="font-size: 13px;">
                      <i class="ri-youtube-fill"></i> Tonton di YouTube
                    </a>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @else
        <!-- ======================================================== -->
        <!-- KONDISI B: JIKA TIDAK ADA YANG LIVE (SEMUA CABANG OFF)   -->
        <!-- ======================================================== -->
        <div class="max-w-3xl mx-auto mb-5">
          <div class="card-earth p-4 p-md-5 text-center shadow-sm" style="background: #FFFFFF !important; border-top: 4px solid var(--earth-accent) !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
              style="width: 64px; height: 64px; background: var(--earth-surface-warm); color: var(--earth-primary); border: 1px solid var(--earth-border);">
              <i class="ri-broadcast-line fs-28"></i>
            </div>

            <h4 class="font-serif fw-bold mb-2" style="color: var(--earth-dark);">Saat Ini Tidak Ada Siaran Langsung (Offline)</h4>
            <p class="small leading-relaxed mb-4" style="color: var(--earth-text-muted); max-width: 540px; margin-left: auto; margin-right: auto;">
              Siaran langsung ibadah raya ditayangkan setiap hari Minggu sesuai jam ibadah masing-masing cabang. Anda tetap dapat menyaksikan tayangan ulang ibadah sebelumnya atau mengunjungi kanal YouTube resmi kami di bawah ini.
            </p>

            <div class="d-inline-flex align-items-center gap-2 p-2 px-3 rounded-pill" style="background: var(--earth-surface-warm); border: 1px solid var(--earth-border);">
              <i class="ri-information-line" style="color: var(--earth-primary);"></i>
              <span class="small" style="color: var(--earth-dark); font-weight: 600;">Jadwal Siaran Berikutnya: Minggu, 06.00 & 08.00 WIB</span>
            </div>
          </div>
        </div>
      @endif

      <!-- DAFTAR KANAL DAN ARSIP STREAMING 5 CABANG -->
      <div class="mt-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <div>
            <h5 class="font-serif fw-bold mb-0" style="color: var(--earth-dark);">Kanal Siaran 5 Cabang Gereja</h5>
            <small style="color: var(--earth-text-muted);">Pilih cabang untuk menyaksikan rekaman atau siaran ibadah</small>
          </div>
          @if($churchProfile->youtube_channel_url)
            <a href="{{ $churchProfile->youtube_channel_url }}" target="_blank" class="btn btn-earth-outline btn-sm px-3.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1.5" style="font-size: 12.5px;">
              <i class="ri-youtube-line"></i> Kanal Pusat
            </a>
          @endif
        </div>

        <div class="row g-4">
          @foreach($streams as $stream)
            <div class="col-xl-4 col-md-6">
              <div class="card-earth p-4 h-100 shadow-sm d-flex flex-column justify-content-between" style="background: #FFFFFF !important;">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge px-2.5 py-1 rounded-pill fw-bold"
                      style="font-size: 11px; {{ $stream->is_live ? 'background: #FEE2E2; color: #DC2626;' : 'background: var(--earth-surface-warm); color: var(--earth-text-muted);' }}">
                      @if($stream->is_live)
                        <span class="live-pulse-dot me-1"></span> LIVE SEKARANG
                      @else
                        🎬 ARSIP & REPLAY
                      @endif
                    </span>
                    <small class="fw-bold" style="color: var(--earth-primary);">{{ $stream->cabang ? $stream->cabang->nama_cabang : $stream->nama_cabang }}</small>
                  </div>

                  <div class="ratio ratio-16x9 mb-3 rounded-3 overflow-hidden shadow-sm" style="background: #111;">
                    <iframe src="{{ $stream->youtube_embed_url }}" title="{{ $stream->judul_stream }}" allowfullscreen></iframe>
                  </div>

                  <h5 class="fw-bold mb-1 font-serif" style="color: var(--earth-dark); font-size: 16px;">{{ $stream->judul_stream }}</h5>
                  <p class="small mb-0" style="color: var(--earth-text-muted);">
                    <i class="ri-time-line me-1" style="color: var(--earth-primary);"></i> {{ $stream->jadwal_tayang ?: 'Setiap Minggu' }}
                  </p>
                </div>

                <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between" style="border-color: var(--earth-border) !important;">
                  <span class="small" style="color: var(--earth-text-muted);"><i class="ri-tv-line me-1"></i> Ibadah Online</span>
                  <a href="{{ $stream->youtube_url }}" target="_blank" class="small fw-bold text-decoration-none d-inline-flex align-items-center gap-1" style="color: #DC2626;">
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
