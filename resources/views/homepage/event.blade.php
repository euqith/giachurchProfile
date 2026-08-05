@extends('homepage.layout')

@section('konten')
  <section class="breadcrumb-area"
    style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('homepage/assets/images/banner/inner-banner.jpg') }}'); padding: 80px 0; background-size: cover; background-position: center;">
    <div class="container text-center">
      <h2 class="text-white fw-bold mb-2" style="font-size: 36px; text-shadow: 2px 2px 5px rgba(0,0,0,0.3);">Agenda & Event
        Gereja</h2>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb justify-content-center mb-0" style="background: transparent;">
          <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white-50">Home</a></li>
          <li class="breadcrumb-item active text-white" aria-current="page">Event</li>
        </ol>
      </nav>
    </div>
  </section>
  <section class="event-page-area pt-80 pb-120 bg-light">
    <div class="container">

      <div class="bg-white p-4 mb-5" style="border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.03);">
        <form action="{{ url('/event') }}" method="GET">
          <div class="row g-3 align-items-center justify-content-between">

            <div class="col-lg-3">
              <h4 class="text-dark fw-bold mb-0" style="font-size: 20px;">
                <i class="fa-solid fa-calendar-list primary-color pe-2"></i> Kalender Kegiatan
              </h4>
            </div>

            <div class="col-lg-9">
              <div class="row g-2 justify-content-end">

                <div class="col-md-4">
                  <label class="small text-muted fw-bold mb-1">Filter Cabang</label>
                  <select name="cabang_id" class="form-select form-select-md" style="border-radius: 8px;">
                    <option value="Semua" {{ request('cabang_id') == 'Semua' ? 'selected' : '' }}>Semua Cabang Gereja
                    </option>
                    @foreach ($cabangs ?? [] as $cabang)
                      <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                        {{ $cabang->nama_cabang }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="small text-muted fw-bold mb-1">Urutkan Berdasarkan</label>
                  <select name="sort_by" class="form-select form-select-md" style="border-radius: 8px;">
                    <option value="date_desc" {{ $sortBy == 'date_desc' ? 'selected' : '' }}>Tanggal Start (Terbaru)
                    </option>
                    <option value="date_asc" {{ $sortBy == 'date_asc' ? 'selected' : '' }}>Tanggal Start (Terlama)
                    </option>
                    <option value="name_asc" {{ $sortBy == 'name_asc' ? 'selected' : '' }}>Nama Event (A - Z)</option>
                    <option value="name_desc" {{ $sortBy == 'name_desc' ? 'selected' : '' }}>Nama Event (Z - A)</option>
                  </select>
                </div>

                <div class="col-md-2 align-self-end">
                  <button type="submit" class="btn w-100 text-white fw-bold"
                    style="background-color: #ff5e14; border-radius: 8px; height: 40px; border: none; transition: 0.3s;"
                    onmouseover="this.style.backgroundColor='#d44d0f'" onmouseout="this.style.backgroundColor='#ff5e14'">
                    <i class="fa-solid fa-arrows-rotate pe-1"></i> Terapkan
                  </button>
                </div>

              </div>
            </div>

          </div>
        </form>
      </div>

      <div class="row g-4">

        @forelse ($events as $event)
          <div class="col-xl-4 col-md-6 wow fadeInUp" data-wow-delay="100ms">
            <div class="bg-white h-100 border-0 card-event-hover"
              style="border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; transition: all 0.3s ease; display: flex; flex-direction: column;">

              <div class="position-relative"
                style="width: 100%; height: 220px; background-color: #f1f5f9; overflow: hidden;">

                @php
                  $sekarang = \Carbon\Carbon::now();
                  $mulai = \Carbon\Carbon::parse($event->waktu_mulai);
                  $selesai = \Carbon\Carbon::parse($event->waktu_selesai);
                @endphp

                @if ($sekarang->lt($mulai))
                  <span class="position-absolute badge text-white style-badge-status"
                    style="top: 15px; right: 15px; z-index: 5; background-color: #3b82f6; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);">
                    🔮 Soon
                  </span>
                @elseif ($sekarang->between($mulai, $selesai))
                  <span class="position-absolute badge text-white style-badge-status style-animate-pulse"
                    style="top: 15px; right: 15px; z-index: 5; background-color: #10b981; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);">
                    🟢 Ongoing
                  </span>
                @else
                  <span class="position-absolute badge text-white style-badge-status"
                    style="top: 15px; right: 15px; z-index: 5; background-color: #64748b; box-shadow: 0 4px 10px rgba(100, 116, 139, 0.3);">
                    ⏰ Expired
                  </span>
                @endif

                @if ($event->gambar_banner)
                  <img src="{{ asset('img/admin/events/' . $event->gambar_banner) }}" alt="{{ $event->nama_acara }}"
                    class="w-100 h-100" style="object-fit: cover;">
                @else
                  <img src="{{ asset('homepage/assets/images/event/event-image1.jpg') }}" alt="Default Poster"
                    class="w-100 h-100" style="object-fit: cover; filter: grayscale(30%);">
                @endif

                <span class="position-absolute badge bg-dark text-white style-badge-branch"
                  style="top: 15px; left: 15px; z-index: 5; background: rgba(0,0,0,0.7) !important; backdrop-filter: blur(5px);">
                  📌 {{ $event->cabang ? $event->cabang->nama_cabang : 'Semua Cabang' }}
                </span>
              </div>

              <div class="p-4 d-flex flex-column justify-content-between flex-grow-1" style="min-height: 250px;">
                <div>
                  <h4 class="text-dark fw-bold mb-2 fs-18"
                    style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; color: #1e293b !important;">
                    {{ $event->nama_acara }}
                  </h4>

                  <p class="text-muted small mb-0"
                    style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ $event->deskripsi_acara ?? 'Mari hadiri persekutuan ibadah bersama untuk menerima siraman rohani yang memberkati.' }}
                  </p>
                </div>

                <div class="border-top pt-3 mt-3 text-muted small space-y-2">

                  <div class="d-flex align-items-start mb-2">
                    <i class="fa-regular fa-clock primary-color pe-2 mt-0.5" style="width: 20px;"></i>
                    <span class="lh-sm">
                      {{ \Carbon\Carbon::parse($event->waktu_mulai)->translatedFormat('d M Y') }} -
                      {{ \Carbon\Carbon::parse($event->waktu_mulai)->format('H:i') }}
                      @if ($event->waktu_selesai)
                        s/d {{ \Carbon\Carbon::parse($event->waktu_selesai)->translatedFormat('d M Y') }} -
                        {{ \Carbon\Carbon::parse($event->waktu_selesai)->format('H:i') }}
                      @endif
                      WIB
                    </span>
                  </div>

                  <div class="d-flex align-items-center bg-light p-2 rounded border border-gray-100">
                    <i class="fa-regular fa-location-dot primary-color pe-2" style="width: 20px; font-size: 14px;"></i>
                    <span class="fw-medium text-dark text-truncate" title="{{ $event->lokasi_spesifik }}">
                      {{ $event->lokasi_spesifik }}
                    </span>
                  </div>

                </div>

              </div>
            </div>
        </div> @empty
          <div class="col-12 text-center py-5">
            <div class="text-muted mb-3">
              <i class="fa-regular fa-calendar-xmark" style="font-size: 60px; color: #cbd5e1;"></i>
            </div>
            <h5 class="fw-bold text-secondary">Belum Ada Agenda Terdekat</h5>
            <p class="text-muted small">Silakan kembali berkala untuk melihat jadwal kegiatan jemaat terbaru.</p>
          </div>
        @endforelse

      </div>
      @if ($events->hasPages())
        <div class="row mt-5">
          <div class="col-12 d-flex justify-content-center ui-pagination">
            {{ $events->links() }}
          </div>
        </div>
      @endif

    </div>
  </section>

  <style>
    .card-event-hover:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 35px rgba(255, 94, 20, 0.12) !important;
    }

    .style-badge-branch {
      font-size: 11px;
      padding: 6px 12px;
      border-radius: 50px;
    }

    .ui-pagination nav svg {
      height: 20px;
    }

    .ui-pagination nav p {
      margin-top: 10px;
      font-size: 13px;
      color: #64748b;
    }

    .style-badge-status {
      font-size: 12px;
      padding: 6px 14px;
      border-radius: 50px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .style-animate-pulse {
      animation: pulse-animation 2s infinite;
    }

    @keyframes pulse-animation {
      0% {
        transform: scale(1);
        opacity: 1;
      }

      50% {
        transform: scale(1.05);
        opacity: 0.85;
      }

      100% {
        transform: scale(1);
        opacity: 1;
      }
    }
  </style>
@endsection
