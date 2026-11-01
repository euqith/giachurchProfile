@extends('homepage.layout')

@section('konten')
  <!-- ========================================== -->
  <!-- 1. HERO SLIDER BANNER                      -->
  <!-- ========================================== -->
  <section class="banner-two-area position-relative">
    <div class="swiper banner-two__slider">
      <div class="swiper-wrapper">

        @forelse ($sliderEvents as $event)
          <div class="swiper-slide" style="position: relative; overflow: hidden; min-height: 520px;">

            <div class="slide-bg"
              style="background-image: linear-gradient(rgba(11, 23, 39, 0.75), rgba(11, 23, 39, 0.75)), url('{{ $event->gambar_banner ? asset('img/admin/events/' . $event->gambar_banner) : asset('homepage/assets/images/banner/banner-image1.jpg') }}'); 
                     background-size: cover; 
                     background-position: center; 
                     position: absolute; 
                     inset: 0; 
                     z-index: 1;">
            </div>

            <div class="container h-100 d-flex align-items-center" style="position: relative; z-index: 2; padding: 90px 15px 120px;">
              <div class="banner-two__content max-w-2xl">

                <span class="badge bg-primary text-white mb-3 px-3 py-2 fw-bold"
                  style="font-size: 13px; border-radius: 50px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(13,110,253,0.3);">
                  <i class="ri-calendar-event-line"></i> UPCOMING EVENT - {{ $event->cabang ? strtoupper($event->cabang->nama_cabang) : 'SEMUA CABANG' }}
                </span>

                <h5 class="text-white-50 mb-2 font-monospace" style="font-size: 15px;">
                  <i class="ri-time-line text-primary me-1"></i>
                  {{ \Carbon\Carbon::parse($event->waktu_mulai)->translatedFormat('l, d F Y') }} ·
                  {{ \Carbon\Carbon::parse($event->waktu_mulai)->format('H:i') }} WIB
                </h5>

                <h1 class="text-white mb-3 fw-bold display-5 tracking-tight" style="line-height: 1.15;">
                  {{ $event->nama_acara }}
                </h1>

                <p class="text-white-50 fs-16 mb-4 fw-normal">
                  <i class="ri-map-pin-2-fill text-primary me-1"></i> {{ $event->lokasi_spesifik ?: 'Gereja Isa Almasih Surabaya' }}
                </p>

                <div class="d-flex flex-wrap gap-3">
                  <a href="{{ route('ibadah.public') }}" class="btn btn-primary px-4 py-3 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 13px;">
                    <i class="ri-book-open-line"></i>
                    <span>LIHAT JADWAL & LITURGI</span>
                  </a>
                  <a href="{{ url('/event') }}" class="btn btn-outline-light px-4 py-3 rounded-pill fw-bold d-inline-flex align-items-center gap-2" style="font-size: 13px;">
                    <i class="ri-calendar-line"></i>
                    <span>SEMUA EVENT</span>
                  </a>
                </div>

              </div>
            </div>
          </div>
        @empty
          <div class="swiper-slide" style="position: relative; min-height: 520px;">
            <div class="slide-bg"
              style="background-image: linear-gradient(rgba(11, 23, 39, 0.78), rgba(11, 23, 39, 0.78)), url('{{ asset('homepage/assets/images/banner/banner-image1.jpg') }}'); 
                     background-size: cover; background-position: center; position: absolute; inset: 0; z-index: 1;">
            </div>
            <div class="container h-100 d-flex align-items-center" style="position: relative; z-index: 2; padding: 100px 15px 120px;">
              <div class="banner-two__content max-w-2xl">
                <span class="badge bg-primary text-white mb-3 px-3 py-2 fw-bold" style="font-size: 13px; border-radius: 50px;">
                  ⛪ SELAMAT DATANG DI GIA SURABAYA
                </span>
                <h1 class="text-white mb-3 fw-bold display-5">Bertumbuh dalam Kristus, Berbuah bagi Sesama</h1>
                <p class="text-white-50 fs-16 mb-4">Mari bersekutu dan melayani bersama jemaat Gereja Isa Almasih Surabaya dalam hadirat Tuhan.</p>
                <div class="d-flex flex-wrap gap-3">
                  <a href="{{ route('ibadah.public') }}" class="btn btn-primary px-4 py-3 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="ri-book-open-line"></i>
                    <span>JADWAL & LITURGI IBADAH</span>
                  </a>
                  <a href="#streaming" class="btn btn-outline-light px-4 py-3 rounded-pill fw-bold d-inline-flex align-items-center gap-2">
                    <i class="ri-live-line text-danger"></i>
                    <span>LIVE STREAMING</span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        @endforelse

      </div>
      <div class="swiper-button-next text-white d-none d-md-flex" style="right: 30px; z-index: 10;"></div>
      <div class="swiper-button-prev text-white d-none d-md-flex" style="left: 30px; z-index: 10;"></div>
    </div>
  </section>

  <!-- ========================================== -->
  <!-- 2. QUICK ACCESS CARDS (OVERLAY BAR)        -->
  <!-- ========================================== -->
  <section class="position-relative" style="margin-top: -55px; z-index: 20;">
    <div class="container">
      <div class="row g-3">
        
        <!-- CARD 1: JADWAL & LITURGI IBADAH (HERO CTA) -->
        <div class="col-lg-3 col-md-6">
          <a href="{{ route('ibadah.public') }}" class="text-decoration-none h-100 d-block">
            <div class="p-4 rounded-4 shadow-sm border bg-white hover-shadow transition h-100 d-flex flex-column justify-between position-relative overflow-hidden group"
              style="border-color: #dbeafe !important; border-top: 4px solid #2563eb !important;">
              <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div class="w-12 h-12 rounded-3 d-flex align-items-center justify-content-center text-white"
                    style="background: linear-gradient(135deg, #1d4ed8, #2563eb); width: 44px; height: 44px;">
                    <i class="ri-book-open-line fs-20"></i>
                  </div>
                  <span class="badge bg-blue-50 text-primary border border-primary-subtle fw-bold" style="font-size: 11px;">
                    Publik & Petugas
                  </span>
                </div>
                <h5 class="fw-bold text-dark mb-1 group-hover-text-primary">Jadwal & Liturgi</h5>
                <p class="text-muted small mb-0">Tema firman, ayat, petugas pelayanan, dan daftar lagu pujian.</p>
              </div>
              <div class="mt-3 pt-2 border-top border-light-subtle d-flex align-items-center justify-content-between text-primary small fw-bold">
                <span>Buka Jadwal</span>
                <i class="ri-arrow-right-line"></i>
              </div>
            </div>
          </a>
        </div>

        <!-- CARD 2: LIVE STREAMING -->
        <div class="col-lg-3 col-md-6">
          <a href="#streaming" class="text-decoration-none h-100 d-block">
            <div class="p-4 rounded-4 shadow-sm border bg-white hover-shadow transition h-100 d-flex flex-column justify-between"
              style="border-color: #fee2e2 !important; border-top: 4px solid #dc2626 !important;">
              <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div class="w-12 h-12 rounded-3 d-flex align-items-center justify-content-center text-white"
                    style="background: linear-gradient(135deg, #b91c1c, #dc2626); width: 44px; height: 44px;">
                    <i class="ri-broadcast-line fs-20"></i>
                  </div>
                  <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold" style="font-size: 11px;">
                    5 Cabang
                  </span>
                </div>
                <h5 class="fw-bold text-dark mb-1">Live Streaming</h5>
                <p class="text-muted small mb-0">Saluran ibadah online mingguan melalui YouTube resmi gereja.</p>
              </div>
              <div class="mt-3 pt-2 border-top border-light-subtle d-flex align-items-center justify-content-between text-danger small fw-bold">
                <span>Tonton Live</span>
                <i class="ri-arrow-right-line"></i>
              </div>
            </div>
          </a>
        </div>

        <!-- CARD 3: WARTA JEMAAT -->
        <div class="col-lg-3 col-md-6">
          <a href="{{ url('/warta') }}" class="text-decoration-none h-100 d-block">
            <div class="p-4 rounded-4 shadow-sm border bg-white hover-shadow transition h-100 d-flex flex-column justify-between"
              style="border-color: #dcfce7 !important; border-top: 4px solid #16a34a !important;">
              <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div class="w-12 h-12 rounded-3 d-flex align-items-center justify-content-center text-white"
                    style="background: linear-gradient(135deg, #15803d, #16a34a); width: 44px; height: 44px;">
                    <i class="ri-newspaper-line fs-20"></i>
                  </div>
                  <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold" style="font-size: 11px;">
                    E-Bulletin
                  </span>
                </div>
                <h5 class="fw-bold text-dark mb-1">Warta Jemaat Digital</h5>
                <p class="text-muted small mb-0">Informasi persembahan, pokok doa jemaat, dan pengumuman gereja.</p>
              </div>
              <div class="mt-3 pt-2 border-top border-light-subtle d-flex align-items-center justify-content-between text-success small fw-bold">
                <span>Baca Warta</span>
                <i class="ri-arrow-right-line"></i>
              </div>
            </div>
          </a>
        </div>

        <!-- CARD 4: AGENDA EVENT -->
        <div class="col-lg-3 col-md-6">
          <a href="{{ url('/event') }}" class="text-decoration-none h-100 d-block">
            <div class="p-4 rounded-4 shadow-sm border bg-white hover-shadow transition h-100 d-flex flex-column justify-between"
              style="border-color: #f3e8ff !important; border-top: 4px solid #9333ea !important;">
              <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div class="w-12 h-12 rounded-3 d-flex align-items-center justify-content-center text-white"
                    style="background: linear-gradient(135deg, #7e22ce, #9333ea); width: 44px; height: 44px;">
                    <i class="ri-calendar-check-line fs-20"></i>
                  </div>
                  <span class="badge bg-purple-subtle text-purple border border-purple-subtle fw-bold" style="font-size: 11px;">
                    Kegiatan
                  </span>
                </div>
                <h5 class="fw-bold text-dark mb-1">Agenda Event</h5>
                <p class="text-muted small mb-0">Kalender seminar, retret, persekutuan doa, dan ibadah gabungan.</p>
              </div>
              <div class="mt-3 pt-2 border-top border-light-subtle d-flex align-items-center justify-content-between text-purple small fw-bold" style="color: #9333ea;">
                <span>Lihat Kalender</span>
                <i class="ri-arrow-right-line"></i>
              </div>
            </div>
          </a>
        </div>

      </div>
    </div>
  </section>

  <!-- ========================================== -->
  <!-- 3. HIGHLIGHT JADWAL IBADAH MINGGU INI      -->
  <!-- ========================================== -->
  <section class="py-5" style="background-color: #f8fafc; padding-top: 80px !important; padding-bottom: 70px !important;">
    <div class="container">
      
      <div class="text-center mb-5">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
          <i class="ri-church-line me-1"></i> Ibadah & Firman Tuhan Minggu Ini
        </span>
        <h2 class="text-dark fw-bold mt-2 display-6 tracking-tight">Persiapan Ibadah & Liturgi Pelayanan</h2>
        <p class="text-muted small max-w-xl mx-auto">
          Setiap jemaat dan pelayan dapat melihat tema ibadah, ayat renungan, petugas yang melayani, serta susunan lagu pujian secara transparan.
        </p>
      </div>

      @if($upcomingSchedule)
        <!-- FEATURED UPCOMING WORSHIP CARD -->
        <div class="max-w-4xl mx-auto bg-white rounded-4 shadow-sm border overflow-hidden" style="border-color: #e2e8f0 !important;">
          
          <!-- Banner Head -->
          <div class="p-4 p-md-5 text-white position-relative" style="background: linear-gradient(135deg, #0b1f48, #1e3a8a);">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
              <span class="badge bg-white text-dark fw-bold px-3 py-1.5 rounded-pill" style="font-size: 12px;">
                <i class="ri-calendar-line me-1 text-primary"></i> {{ $upcomingSchedule->nama_ibadah ?: 'Ibadah Umum' }}
              </span>
              <span class="badge bg-primary text-white fw-bold px-3 py-1.5 rounded-pill" style="font-size: 12px;">
                <i class="ri-map-pin-line me-1"></i> {{ $upcomingSchedule->nama_cabang ?: 'Surabaya' }}
              </span>
              @if($upcomingSchedule->nama_sesi)
                <span class="badge bg-warning text-dark fw-bold px-3 py-1.5 rounded-pill" style="font-size: 12px;">
                  Sesi: {{ $upcomingSchedule->nama_sesi }}
                </span>
              @endif
            </div>

            <div class="text-white-50 small mb-1">
              {{ \Carbon\Carbon::parse($upcomingSchedule->tanggal)->translatedFormat('l, d F Y') }}
            </div>

            <h3 class="text-white fw-bold display-6 mb-2">
              "{{ $upcomingSchedule->tema ?: $upcomingSchedule->nama_ibadah }}"
            </h3>

            @if($upcomingSchedule->ayat)
              <div class="d-flex align-items-center gap-2 text-info fw-semibold" style="font-size: 15px;">
                <i class="ri-book-open-line"></i>
                <span>Ayat Bacaan: {{ $upcomingSchedule->ayat }}</span>
              </div>
            @endif
          </div>

          <!-- Body Info: Pelayan & Liturgi Lagu -->
          <div class="p-4 p-md-5">
            <div class="row g-4 mb-4">
              
              <!-- Pelayan Firman -->
              <div class="col-sm-6">
                <div class="p-3 rounded-3 border bg-light-subtle d-flex align-items-center gap-3">
                  <div class="rounded-3 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="ri-mic-line fs-20"></i>
                  </div>
                  <div>
                    <span class="text-muted d-block text-uppercase" style="font-size: 10px; font-weight: 700;">Pelayan Firman / Pengkhotbah</span>
                    <strong class="text-dark fs-15">{{ $upcomingSchedule->pengkhotbah ?: 'Belum ditentukan' }}</strong>
                  </div>
                </div>
              </div>

              <!-- Pemimpin Pujian (WL) -->
              <div class="col-sm-6">
                <div class="p-3 rounded-3 border bg-light-subtle d-flex align-items-center gap-3">
                  <div class="rounded-3 bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="ri-music-2-line fs-20"></i>
                  </div>
                  <div>
                    <span class="text-muted d-block text-uppercase" style="font-size: 10px; font-weight: 700;">Pemimpin Pujian (Worship Leader)</span>
                    <strong class="text-dark fs-15">{{ $upcomingSchedule->pemimpin_pujian ?: 'Belum ditentukan' }}</strong>
                  </div>
                </div>
              </div>

            </div>

            <!-- Preview Daftar Lagu -->
            @if($upcomingSchedule->scheduleSongs && $upcomingSchedule->scheduleSongs->isNotEmpty())
              <div class="mb-4">
                <h6 class="fw-bold text-dark text-uppercase mb-3 d-flex align-items-center gap-2" style="font-size: 12px; letter-spacing: 0.5px;">
                  <i class="ri-play-list-line text-primary"></i>
                  <span>Susunan Lagu Pujian ({{ $upcomingSchedule->scheduleSongs->count() }} Lagu)</span>
                </h6>
                <div class="d-flex flex-wrap gap-2">
                  @foreach($upcomingSchedule->scheduleSongs->take(5) as $sSong)
                    @if($sSong->song)
                      <span class="badge bg-light text-dark border px-3 py-2 rounded-3 text-start d-inline-flex align-items-center gap-1.5" style="font-size: 12px;">
                        <span class="badge bg-primary text-white rounded-pill px-1.5 py-0.5" style="font-size: 9px;">{{ $loop->iteration }}</span>
                        <strong>{{ $sSong->song->judul_lagu }}</strong>
                        @if($sSong->nada_dasar || $sSong->song->nada_dasar)
                          <small class="text-muted">({{ $sSong->nada_dasar ?: $sSong->song->nada_dasar }})</small>
                        @endif
                      </span>
                    @endif
                  @endforeach
                  @if($upcomingSchedule->scheduleSongs->count() > 5)
                    <span class="badge bg-light text-muted border px-3 py-2 rounded-3" style="font-size: 12px;">
                      +{{ $upcomingSchedule->scheduleSongs->count() - 5 }} lagu lainnya
                    </span>
                  @endif
                </div>
              </div>
            @endif

            <!-- Action Buttons -->
            <div class="pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
              <a href="{{ route('ibadah.public', ['id' => $upcomingSchedule->id]) }}"
                class="btn btn-primary px-4 py-2.5 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 13px;">
                <i class="ri-eye-line"></i>
                <span>Buka Detail Liturgi & Petugas Pelayanan</span>
              </a>

              <a href="{{ route('ibadah.public') }}"
                class="btn btn-outline-secondary px-4 py-2.5 rounded-pill fw-bold d-inline-flex align-items-center gap-2" style="font-size: 13px;">
                <i class="ri-calendar-check-line"></i>
                <span>Lihat Semua Jadwal & Arsip</span>
              </a>
            </div>

          </div>

        </div>
      @else
        <!-- EMPTY / INVITATION BANNER -->
        <div class="max-w-2xl mx-auto bg-white p-5 rounded-4 border text-center shadow-sm">
          <div class="w-16 h-16 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 54px; height: 54px;">
            <i class="ri-calendar-todo-line fs-24"></i>
          </div>
          <h4 class="fw-bold text-dark">Informasi Jadwal Ibadah Gereja</h4>
          <p class="text-muted small mb-4">
            Jadwal ibadah raya dan liturgi pelayanan diperbarui secara berkala oleh tim pastoral dan majelis jemaat.
          </p>
          <a href="{{ route('ibadah.public') }}" class="btn btn-primary px-4 py-2.5 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-2">
            <i class="ri-book-open-line"></i>
            <span>Buka Halaman Jadwal Ibadah</span>
          </a>
        </div>
      @endif

    </div>
  </section>

  <!-- ========================================== -->
  <!-- 4. LIVE STREAMING SECTION                 -->
  <!-- ========================================== -->
  <section id="streaming" class="service-area py-5" style="background-color: #ffffff;">
    <div class="container py-4">
      <div class="section-header text-center mb-5">
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fw-bold text-uppercase" style="font-size: 11px;">
          <i class="ri-live-line me-1"></i> LIVE STREAMING IBADAH
        </span>
        <h2 class="text-dark fw-bold mt-2 display-6 tracking-tight">Ibadah Online 5 Cabang Gereja</h2>
        <p class="text-muted small max-w-xl mx-auto">
          Bagi jemaat yang berhalangan hadir secara tatap muka, silakan bergabung dalam siaran langsung ibadah raya melalui kanal YouTube resmi kami.
        </p>
      </div>

      <div class="row g-4">
        @for ($j = 1; $j <= 5; $j++)
          <div class="col-xl-4 col-md-6 wow fadeInUp" data-wow-delay="{{ $j * 100 }}ms" data-wow-duration="1500ms">
            <div class="bg-white p-4 rounded-4 border hover-shadow transition h-100 d-flex flex-column justify-between"
              style="border-color: #e2e8f0 !important;">
              <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <span class="badge @if ($j == 1) bg-danger text-white @else bg-secondary-subtle text-secondary @endif fw-bold px-2.5 py-1 rounded-pill"
                    style="font-size: 11px;">
                    @if ($j == 1)
                      🔴 LIVE STREAMING
                    @else
                      🎬 REPLAY & ARSIP
                    @endif
                  </span>
                  <small class="text-muted fw-bold">Cabang {{ $j }}</small>
                </div>
                <div class="ratio ratio-16x9 mb-3 rounded-3 overflow-hidden shadow-sm">
                  <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player"
                    allowfullscreen></iframe>
                </div>
                <h5 class="text-dark fw-bold mb-1" style="font-size: 16px;">Ibadah Raya Minggu Cabang {{ $j }}</h5>
                <p class="text-muted small mb-0"><i class="ri-time-line text-primary me-1"></i> Minggu, Pkl 09:00 WIB</p>
              </div>

              <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                <a href="{{ route('ibadah.public') }}" class="text-primary small fw-bold text-decoration-none d-inline-flex align-items-center gap-1">
                  <i class="ri-book-open-line"></i> Lihat Liturgi
                </a>
                <a href="https://youtube.com" target="_blank" class="text-danger small fw-bold text-decoration-none d-inline-flex align-items-center gap-1">
                  <i class="ri-youtube-line"></i> YouTube
                </a>
              </div>
            </div>
          </div>
        @endfor
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
          delay: 5000,
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
