@extends('homepage.layout')

@section('konten')
  <!-- BREADCRUMB HEADER BANNER -->
  <section class="breadcrumb-area"
    style="background-image: linear-gradient(rgba(28, 23, 20, 0.75), rgba(28, 23, 20, 0.85)), url('{{ asset('homepage/assets/images/banner/inner-banner.jpg') }}'); padding: 70px 0; background-size: cover; background-position: center;">
    <div class="container text-center">
      <span class="badge badge-earth px-3 py-1.5 rounded-pill mb-2 text-uppercase" style="font-size: 11px; letter-spacing: 0.8px;">
        <i class="ri-calendar-event-line me-1"></i> Agenda & Kegiatan
      </span>
      <h1 class="text-white fw-bold font-serif mb-2" style="font-size: 38px;">Event & Kegiatan Gereja</h1>
      <p class="text-white-50 small mb-0">Kalender seminar, retret, persekutuan doa, dan ibadah gabungan di seluruh wilayah GIA Surabaya.</p>
    </div>
  </section>

  <!-- EVENT LIST AREA -->
  <section class="py-5" style="background-color: var(--earth-bg); padding-top: 60px !important; padding-bottom: 90px !important;">
    <div class="container">

      <!-- FILTER & SORT BAR -->
      <div class="card-earth p-4 mb-5 shadow-sm" style="background: #FFFFFF !important;">
        <form action="{{ url('/event') }}" method="GET">
          <div class="row g-3 align-items-center justify-content-between">

            <div class="col-lg-3">
              <h4 class="font-serif fw-bold mb-0" style="color: var(--earth-dark); font-size: 20px;">
                <i class="ri-calendar-schedule-line me-1" style="color: var(--earth-primary);"></i> Kalender Kegiatan
              </h4>
            </div>

            <div class="col-lg-9">
              <div class="row g-2 justify-content-end">

                <div class="col-md-4">
                  <label class="small fw-bold mb-1" style="color: var(--earth-text-muted);">Filter Wilayah</label>
                  <select name="cabang_id" class="form-select form-select-md" style="border-radius: 10px; border-color: var(--earth-border); background-color: var(--earth-surface-warm); font-size: 13.5px;">
                    <option value="Semua" {{ request('cabang_id') == 'Semua' ? 'selected' : '' }}>Semua Wilayah Gereja</option>
                    @foreach ($cabangs ?? [] as $cabang)
                      <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                        {{ $cabang->nama_cabang }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="small fw-bold mb-1" style="color: var(--earth-text-muted);">Urutkan Berdasarkan</label>
                  <select name="sort_by" class="form-select form-select-md" style="border-radius: 10px; border-color: var(--earth-border); background-color: var(--earth-surface-warm); font-size: 13.5px;">
                    <option value="date_desc" {{ $sortBy == 'date_desc' ? 'selected' : '' }}>Tanggal Mulai (Terbaru)</option>
                    <option value="date_asc" {{ $sortBy == 'date_asc' ? 'selected' : '' }}>Tanggal Mulai (Terlama)</option>
                    <option value="name_asc" {{ $sortBy == 'name_asc' ? 'selected' : '' }}>Nama Event (A - Z)</option>
                    <option value="name_desc" {{ $sortBy == 'name_desc' ? 'selected' : '' }}>Nama Event (Z - A)</option>
                  </select>
                </div>

                <div class="col-md-3 align-self-end">
                  <button type="submit" class="btn btn-earth w-100 fw-bold rounded-3 d-inline-flex align-items-center justify-content-center gap-1.5" style="height: 42px; font-size: 13.5px;">
                    <i class="ri-refresh-line"></i> Terapkan Filter
                  </button>
                </div>

              </div>
            </div>

          </div>
        </form>
      </div>

      <!-- EVENT CARDS GRID -->
      <div class="row g-4">
        @forelse ($events as $event)
          @php
            $sekarang = \Carbon\Carbon::now();
            $mulai = \Carbon\Carbon::parse($event->waktu_mulai);
            $selesai = \Carbon\Carbon::parse($event->waktu_selesai);
          @endphp

          <div class="col-xl-4 col-md-6">
            <div class="card-earth h-100 shadow-sm overflow-hidden d-flex flex-column justify-content-between" style="background: #FFFFFF !important;">
              <div>
                <!-- GAMBAR BANNER EVENT -->
                <div class="position-relative overflow-hidden" style="width: 100%; height: 210px; background-color: var(--earth-surface-warm);">
                  @if ($event->gambar_banner)
                    <img src="{{ asset('img/admin/events/' . $event->gambar_banner) }}" alt="{{ $event->nama_acara }}"
                      class="w-100 h-100 object-cover transition" style="object-fit: cover;">
                  @else
                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="background: var(--earth-surface-warm); color: var(--earth-text-muted);">
                      <i class="ri-image-line fs-2 mb-1" style="color: var(--earth-primary);"></i>
                      <small>No Banner Available</small>
                    </div>
                  @endif

                  <!-- STATUS BADGE -->
                  @if ($sekarang->lt($mulai))
                    <span class="position-absolute badge rounded-pill fw-bold"
                      style="top: 14px; right: 14px; z-index: 5; background: rgba(30, 23, 19, 0.85); color: #FAF7F2; backdrop-filter: blur(4px); font-size: 11px;">
                      <i class="ri-calendar-line me-1" style="color: var(--earth-accent);"></i> Mendatang
                    </span>
                  @elseif ($sekarang->between($mulai, $selesai))
                    <span class="position-absolute badge bg-success text-white rounded-pill fw-bold d-inline-flex align-items-center gap-1.5"
                      style="top: 14px; right: 14px; z-index: 5; font-size: 11px;">
                      <span class="live-pulse-dot" style="background: #FFFFFF;"></span> Sedang Berlangsung
                    </span>
                  @else
                    <span class="position-absolute badge bg-secondary text-white rounded-pill fw-bold"
                      style="top: 14px; right: 14px; z-index: 5; font-size: 11px;">
                      Selesai
                    </span>
                  @endif

                  <!-- WILAYAH BADGE -->
                  <span class="position-absolute badge badge-earth rounded-pill"
                    style="bottom: 14px; left: 14px; z-index: 5; font-size: 11px; background: rgba(255,255,255,0.92) !important;">
                    <i class="ri-map-pin-line me-1"></i> {{ $event->cabang ? $event->cabang->nama_cabang : 'Semua Wilayah' }}
                  </span>
                </div>

                <!-- BODY INFO EVENT -->
                <div class="p-4">
                  <div class="d-flex align-items-center gap-1.5 mb-2 small" style="color: var(--earth-primary); font-weight: 600;">
                    <i class="ri-time-line"></i>
                    <span>{{ $mulai->translatedFormat('d M Y') }} · {{ $mulai->format('H:i') }} WIB</span>
                  </div>

                  <h4 class="font-serif fw-bold mb-2" style="color: var(--earth-dark); font-size: 19px; line-height: 1.3;">
                    {{ $event->nama_acara }}
                  </h4>

                  <p class="small mb-3" style="color: var(--earth-text-muted);">
                    <i class="ri-map-pin-2-line me-1" style="color: var(--earth-accent);"></i>
                    {{ $event->lokasi_spesifik ?: 'Gereja Isa Almasih Surabaya' }}
                  </p>

                  <p class="small leading-relaxed line-clamp-3 mb-0" style="color: #4D423B; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ $event->deskripsi ?: 'Tidak ada deskripsi rinci untuk kegiatan ini. Silakan hubungi sekretariat gereja untuk informasi selengkapnya.' }}
                  </p>
                </div>
              </div>

              <!-- FOOTER ACTION -->
              <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between" style="border-color: var(--earth-border) !important; background: var(--earth-surface-warm);">
                <span class="small" style="color: var(--earth-text-muted);">
                  <i class="ri-user-voice-line me-1"></i> Terbuka untuk Jemaat
                </span>
                <button type="button" class="btn btn-earth-outline btn-sm px-3 py-1 rounded-pill"
                  onclick="showEventModal('{{ addslashes($event->nama_acara) }}', '{{ addslashes($mulai->translatedFormat('l, d F Y · H:i') . ' WIB') }}', '{{ addslashes($event->lokasi_spesifik ?: 'GIA Surabaya') }}', '{{ addslashes($event->deskripsi) }}', '{{ $event->gambar_banner ? asset('img/admin/events/' . $event->gambar_banner) : '' }}')">
                  Detail <i class="ri-arrow-right-s-line"></i>
                </button>
              </div>

            </div>
          </div>
        @empty
          <div class="col-12">
            <div class="card-earth p-5 text-center shadow-sm" style="background: #FFFFFF !important;">
              <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                style="width: 60px; height: 60px; background: var(--earth-surface-warm); color: var(--earth-primary);">
                <i class="ri-calendar-line fs-24"></i>
              </div>
              <h4 class="font-serif fw-bold mb-1" style="color: var(--earth-dark);">Belum Ada Agenda Kegiatan</h4>
              <p class="small mb-0" style="color: var(--earth-text-muted);">
                Saat ini belum ada event mendatang yang terdaftar sesuai dengan filter yang dipilih.
              </p>
            </div>
          </div>
        @endforelse
      </div>

      <!-- PAGINATION -->
      <div class="mt-5 d-flex justify-content-center">
        {{ $events->links('pagination::bootstrap-5') }}
      </div>

    </div>
  </section>

  <!-- MODAL DETAIL EVENT -->
  <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content card-earth overflow-hidden border-0 shadow-lg" style="background: #FFFFFF !important;">
        <div id="modalBannerWrapper" style="display: none; height: 260px; width: 100%; overflow: hidden;">
          <img id="modalBannerImg" src="" alt="Banner" class="w-100 h-100 object-cover" style="object-fit: cover;">
        </div>
        <div class="modal-body p-4 p-md-5">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge badge-earth px-3 py-1 rounded-pill" style="font-size: 11px;">
              <i class="ri-calendar-check-line me-1"></i> Agenda Kegiatan
            </span>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <h3 id="modalEventTitle" class="font-serif fw-bold mb-3" style="color: var(--earth-dark);"></h3>
          
          <div class="p-3 rounded-3 mb-4 d-flex flex-wrap gap-4" style="background: var(--earth-surface-warm); border: 1px solid var(--earth-border); font-size: 13.5px;">
            <div>
              <i class="ri-time-line me-1" style="color: var(--earth-primary);"></i>
              <strong id="modalEventTime" style="color: var(--earth-dark);"></strong>
            </div>
            <div>
              <i class="ri-map-pin-2-line me-1" style="color: var(--earth-primary);"></i>
              <span id="modalEventLocation" style="color: var(--earth-text-muted);"></span>
            </div>
          </div>

          <h6 class="fw-bold mb-2 text-uppercase" style="font-size: 12px; letter-spacing: 0.5px; color: var(--earth-primary);">Deskripsi Kegiatan:</h6>
          <p id="modalEventDesc" class="leading-relaxed" style="color: #4D423B; font-size: 14.5px; white-space: pre-line;"></p>
        </div>
        <div class="modal-footer border-top p-3" style="border-color: var(--earth-border) !important; background: var(--earth-surface-warm);">
          <button type="button" class="btn btn-earth px-4 py-2 rounded-pill font-weight-bold" data-bs-dismiss="modal">
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function showEventModal(title, time, location, desc, bannerUrl) {
      document.getElementById('modalEventTitle').innerText = title;
      document.getElementById('modalEventTime').innerText = time;
      document.getElementById('modalEventLocation').innerText = location;
      document.getElementById('modalEventDesc').innerText = desc || 'Tidak ada deskripsi rinci.';
      
      const bannerWrapper = document.getElementById('modalBannerWrapper');
      const bannerImg = document.getElementById('modalBannerImg');
      if (bannerUrl) {
        bannerImg.src = bannerUrl;
        bannerWrapper.style.display = 'block';
      } else {
        bannerWrapper.style.display = 'none';
      }

      const modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
      modal.show();
    }
  </script>
@endsection
