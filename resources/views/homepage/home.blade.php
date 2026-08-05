@extends('homepage.layout')

@section('konten')
  <section class="banner-two-area">
    <div class="swiper banner-two__slider">
      <div class="swiper-wrapper">

        @forelse ($sliderEvents as $event)
          <div class="swiper-slide" style="position: relative; overflow: hidden;">

            <div class="slide-bg"
              style="background-image: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), url('{{ $event->gambar_banner ? asset('img/admin/events/' . $event->gambar_banner) : asset('homepage/assets/images/banner/banner-image1.jpg') }}'); 
                     background-size: cover; 
                     background-position: center; 
                     position: absolute; 
                     inset: 0; 
                     z-index: 1;">
            </div>

            <div class="container" style="position: relative; z-index: 2;">
              <div class="banner-two__content">

                <span class="badge bg-primary text-white mb-3 px-3 py-2"
                  style="font-size: 14px; border-radius: 50px; display: inline-block; opacity: 1; visibility: visible;">
                  📌 UPCOMING EVENT - {{ $event->cabang ? strtoupper($event->cabang->nama_cabang) : 'SEMUA CABANG' }}
                </span>

                <h4 class="text-white mb-2" style="opacity: 1; visibility: visible;">
                  {{ \Carbon\Carbon::parse($event->waktu_mulai)->translatedFormat('l, d F Y') }} |
                  {{ \Carbon\Carbon::parse($event->waktu_mulai)->format('H:i') }} WIB
                </h4>

                <h1 class="text-white mb-3" style="opacity: 1; visibility: visible; display: block;">
                  {{ $event->nama_acara }}
                </h1>

                <p class="text-white fs-18 mb-4 fw-medium" style="opacity: 1; visibility: visible;">
                  <i class="fa-regular fa-location-dot primary-color pe-2"></i> {{ $event->lokasi_spesifik }}
                </p>

                <div class="btn-one mt-4" style="opacity: 1; visibility: visible;">
                  <span class="btn-circle"></span>
                  <a href="{{ url('/event') }}" class="btn-inner">
                    <span class="btn-text">LIHAT SEMUA EVENT</span>
                  </a>
                </div>

              </div>
            </div>
        </div> @empty
          <div class="swiper-slide" style="position: relative;">
            <div class="slide-bg"
              style="background-image: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), url('{{ asset('homepage/assets/images/banner/banner-image1.jpg') }}'); 
                     background-size: cover; background-position: center; position: absolute; inset: 0; z-index: 1;">
            </div>
            <div class="container" style="position: relative; z-index: 2;">
              <div class="banner-two__content">
                <span class="badge bg-secondary text-white mb-3 px-3 py-2" style="font-size: 14px; border-radius: 50px;">
                  📌 NO UPCOMING EVENT
                </span>
                <h1 class="text-white">Belum Ada Agenda Event Baru</h1>
                <p class="text-white fs-18 mb-4 fw-medium">Silakan cek kembali nanti untuk pembaruan jadwal ibadah.</p>
              </div>
            </div>
          </div>
        @endforelse

      </div>
      <div class="swiper-button-next text-white d-none d-md-flex" style="right: 30px; z-index: 10;"></div>
      <div class="swiper-button-prev text-white d-none d-md-flex" style="left: 30px; z-index: 10;"></div>
    </div>
  </section>

  <section id="streaming" class="service-area pt-100 pb-100" style="background-color: #f8f9fa;">
    <div class="container">
      <div class="section-header text-center mb-50">
        <h5 class="primary-color"><i class="fa-regular fa-angles-left pe-1"></i> LIVE STREAMING <i
            class="fa-regular fa-angles-right ps-1"></i></h5>
        <h2 class="text-dark">Ibadah Online 5 Cabang Gereja</h2>
        <p class="text-muted">Silakan pilih saluran streaming berdasarkan cabang dan jadwal ibadah Anda.</p>
      </div>

      <div class="row g-4">
        @for ($j = 1; $j <= 5; $j++)
          <div class="col-xl-4 col-md-6 wow fadeInUp" data-wow-delay="{{ $j * 100 }}ms" data-wow-duration="1500ms">
            <div class="bg-white p-4"
              style="border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #eee;">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge @if ($j == 1) bg-danger @else bg-secondary @endif text-white"
                  style="font-size: 11px; padding: 5px 10px;">
                  @if ($j == 1)
                    🔴 LIVE NOW
                  @else
                    🎬 REPLAY
                  @endif
                </span>
                <small class="text-muted fw-bold">Cabang {{ $j }}</small>
              </div>
              <div class="ratio ratio-16x9 mb-3"
                style="border-radius: 8px; overflow: hidden; box-shadow: 0 3px 10px rgba(0,0,0,0.1);">
                <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player"
                  allowfullscreen></iframe>
              </div>
              <div class="service__content pt-2">
                <h4 class="text-dark fs-18 mb-2 fw-bold" style="color: #222 !important;">Ibadah Raya Minggu
                  {{ $j }}</h4>
                <p class="text-muted small mb-0"><i class="fa-regular fa-clock primary-color pe-1"></i> Minggu, Pkl 09:00
                  WIB</p>
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
    // 1. Cari elemen slider swiper template
    const sliderElem = document.querySelector('.banner-two__slider');

    if (sliderElem && typeof Swiper !== 'undefined') {
      // 2. Jika swiper sudah berjalan tapi macet, kita paksa hancurkan dulu instansi lamanya
      if (sliderElem.swiper) {
        sliderElem.swiper.destroy(true, true);
      }

      // 3. Kita bangun ulang Swiper murni khusus untuk mendeteksi slide dinamis databasemu
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
        // Memaksa swiper memperbarui layout secara berkala jika ada perubahan komponen internal
        observer: true,
        observeParents: true,
      });
    }
  });
</script>
