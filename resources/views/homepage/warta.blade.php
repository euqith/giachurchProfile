@extends('homepage.layout')

@section('konten')
  <section class="breadcrumb-area"
    style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('homepage/assets/images/banner/inner-banner.jpg') }}'); padding: 80px 0; background-size: cover; background-position: center;">
    <div class="container text-center">
      <h2 class="text-white fw-bold mb-2" style="font-size: 36px; text-shadow: 2px 2px 5px rgba(0,0,0,0.3);">Warta Jemaat
      </h2>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb justify-content-center mb-0" style="background: transparent;">
          <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white-50">Home</a></li>
          <li class="breadcrumb-item active text-white" aria-current="page">Warta Jemaat</li>
        </ol>
      </nav>
    </div>
  </section>

  <section class="about-area pt-60 pb-120 bg-light">
    <div class="container-fluid px-md-5">
      <div class="row">

        {{-- KIRI: KONTEN UTAMA WARTA --}}
        <div class="col-lg-9 col-md-12 mb-4">
          <div class="bg-white p-4 p-md-5 shadow-sm border-0" style="border-radius: 20px;">
            @if ($wartaUtama)
              <div
                class="d-flex justify-content-between align-items-md-center flex-column flex-md-row border-bottom pb-3 mb-4 gap-2">
                <div>
                  <h3 class="text-dark fw-bold mb-1" style="font-size: 24px;">{{ $wartaUtama->judul_edisi }}</h3>
                  <p class="text-muted small mb-0">
                    <i class="fa-regular fa-calendar-check primary-color pe-1"></i> Tanggal Rilis:
                    {{ \Carbon\Carbon::parse($wartaUtama->tanggal_rilis)->translatedFormat('d F Y') }}
                  </p>
                </div>
              </div>

              {{-- DETEKSI DOKUMEN PDF --}}
              @if ($wartaUtama->slides->count() > 0)
                @php $document = $wartaUtama->slides->first(); @endphp

                <div
                  class="text-center mb-4 p-3 bg-light rounded d-flex justify-content-between align-items-center flex-wrap gap-2">
                  <div class="d-flex align-items-center text-start">
                    <i class="fa-solid fa-file-pdf text-danger fs-1 pe-3"></i>
                    <div>
                      <span class="fw-bold d-block text-dark small">Dokumen Warta Jemaat (PDF)</span>
                      <small class="text-muted">{{ $document->file_gambar }}</small>
                    </div>
                  </div>
                  <a href="{{ asset('img/admin/wartas/' . $document->file_gambar) }}" target="_blank"
                    class="btn btn-danger px-4 py-2 rounded-pill font-weight-bold">
                    <i class="fa-solid fa-download me-2"></i> Download PDF
                  </a>
                </div>

                {{-- INTERACTIVE LOCAL PDF PREVIEW (Bisa dibaca langsung di localhost) --}}
                <div class="w-100 border rounded shadow-sm overflow-hidden"
                  style="height: 750px; background-color: #eee;">
                  <object data="{{ asset('img/admin/wartas/' . $document->file_gambar) }}" type="application/pdf"
                    class="w-100 h-100">
                    <iframe src="{{ asset('img/admin/wartas/' . $document->file_gambar) }}" class="w-100 h-100"
                      style="border: none;">
                      <p>Browser Anda tidak mendukung preview PDF. Silakan klik tombol download di atas.</p>
                    </iframe>
                  </object>
                </div>
              @else
                <div class="text-center py-5 text-muted">
                  <i class="fa-solid fa-file-circle-xmark fs-1 mb-3 text-muted"></i>
                  <p class="mb-0">Dokumen PDF belum diunggah untuk edisi warta ini.</p>
                </div>
              @endif
            @else
              <div class="text-center py-5 text-muted">
                <i class="fa-solid fa-newspaper fs-1 mb-3 text-muted"></i>
                <p class="mb-0">Belum ada edisi warta jemaat aktif yang diterbitkan.</p>
              </div>
            @endif
          </div>
        </div>

        {{-- KANAN: ARSIP WARTA --}}
        <div class="col-lg-3 col-md-12">
          <div class="bg-white p-4 shadow-sm border-0 mb-4" style="border-radius: 20px;">
            <h5 class="text-dark fw-bold mb-3 border-bottom pb-2" style="font-size: 18px;">
              <i class="fa-solid fa-box-archive primary-color pe-2"></i>Arsip Edisi
            </h5>
            <div class="d-flex flex-column gap-2 overflow-auto" style="max-height: 450px; padding-right: 4px;">
              @forelse($arsipWartas as $arsip)
                <a href="{{ url('warta?warta_id=' . $arsip->id) }}"
                  class="d-flex justify-content-between align-items-center p-3 rounded border text-decoration-none btn-archive-hover {{ isset($wartaUtama) && $wartaUtama->id == $arsip->id ? 'warta-active' : '' }}"
                  style="background-color: #fdfdfd; transition: all 0.3s ease;">
                  <div class="d-flex align-items-center">
                    <i class="fa-regular fa-file-pdf text-danger fs-20 pe-3"></i>
                    <div>
                      <span class="text-dark fw-bold d-block small mb-0">{{ $arsip->judul_edisi }}</span>
                      <small
                        class="text-muted text-xs">{{ \Carbon\Carbon::parse($arsip->tanggal_rilis)->translatedFormat('d M Y') }}</small>
                    </div>
                  </div>
                  <i class="fa-solid fa-angle-right text-muted small"></i>
                </a>
              @empty
                <p class="text-muted small italic p-3 text-center">Belum ada arsip warta.</p>
              @endforelse
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <style>
    .btn-archive-hover:hover,
    .warta-active {
      background-color: #fff4ef !important;
      border-color: #ff5e14 !important;
    }

    .warta-active span {
      color: #ff5e14 !important;
    }

    .primary-color {
      color: #ff5e14 !important;
    }
  </style>
@endsection
