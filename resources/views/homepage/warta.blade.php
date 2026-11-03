@extends('homepage.layout')

@section('konten')
  <!-- BREADCRUMB HEADER BANNER -->
  <section class="breadcrumb-area"
    style="background-image: linear-gradient(rgba(28, 23, 20, 0.75), rgba(28, 23, 20, 0.85)), url('{{ asset('homepage/assets/images/banner/inner-banner.jpg') }}'); padding: 70px 0; background-size: cover; background-position: center;">
    <div class="container text-center">
      <span class="badge badge-earth px-3 py-1.5 rounded-pill mb-2 text-uppercase" style="font-size: 11px; letter-spacing: 0.8px;">
        <i class="ri-newspaper-line me-1"></i> E-Bulletin Jemaat
      </span>
      <h1 class="text-white fw-bold font-serif mb-2" style="font-size: 38px;">Warta Jemaat Digital</h1>
      <p class="text-white-50 small mb-0">Informasi kegiatan pelayanan, pokok doa, dan pengumuman jemaat GIA Surabaya.</p>
    </div>
  </section>

  <!-- KONTEN WARTA -->
  <section class="py-5" style="background-color: var(--earth-bg); padding-top: 60px !important; padding-bottom: 90px !important;">
    <div class="container-fluid px-md-5">
      <div class="row g-4">

        {{-- KIRI: KONTEN UTAMA WARTA --}}
        <div class="col-lg-9 col-md-12">
          <div class="card-earth p-4 p-md-5 shadow-sm" style="background: #FFFFFF !important;">
            @if ($wartaUtama)
              <div class="d-flex justify-content-between align-items-md-center flex-column flex-md-row border-bottom pb-3 mb-4 gap-2" style="border-color: var(--earth-border) !important;">
                <div>
                  <span class="badge badge-earth px-2.5 py-1 rounded-pill mb-2" style="font-size: 11px;">
                    <i class="ri-file-text-line me-1"></i> Edisi Terbit
                  </span>
                  <h2 class="font-serif fw-bold mb-1" style="color: var(--earth-dark); font-size: 26px;">{{ $wartaUtama->judul_edisi }}</h2>
                  <p class="small mb-0" style="color: var(--earth-text-muted);">
                    <i class="ri-calendar-check-line me-1" style="color: var(--earth-primary);"></i> Tanggal Rilis:
                    <strong>{{ \Carbon\Carbon::parse($wartaUtama->tanggal_rilis)->translatedFormat('l, d F Y') }}</strong>
                  </p>
                </div>
                <div>
                  @if ($wartaUtama->slides->count() > 0)
                    @php $document = $wartaUtama->slides->first(); @endphp
                    <a href="{{ asset('img/admin/wartas/' . $document->file_gambar) }}" target="_blank"
                      class="btn btn-earth px-4 py-2 rounded-pill font-weight-bold d-inline-flex align-items-center gap-2" style="font-size: 13px;">
                      <i class="ri-download-2-line"></i> Download PDF
                    </a>
                  @endif
                </div>
              </div>

              {{-- DOKUMEN PREVIEW --}}
              @if ($wartaUtama->slides->count() > 0)
                @php $document = $wartaUtama->slides->first(); @endphp

                <div class="p-3 rounded-3 d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4"
                  style="background: var(--earth-surface-warm); border: 1px solid var(--earth-border);">
                  <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                      style="background: var(--earth-primary); width: 44px; height: 44px; flex-shrink: 0;">
                      <i class="ri-file-pdf-line fs-22"></i>
                    </div>
                    <div>
                      <span class="fw-bold d-block small" style="color: var(--earth-dark);">Dokumen E-Bulletin Warta Jemaat</span>
                      <small style="color: var(--earth-text-muted);">{{ $document->file_gambar }}</small>
                    </div>
                  </div>
                  <a href="{{ asset('img/admin/wartas/' . $document->file_gambar) }}" target="_blank"
                    class="btn btn-earth-outline btn-sm px-3.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1.5" style="font-size: 12.5px;">
                    <i class="ri-external-link-line"></i> Buka Dokumen Penuh
                  </a>
                </div>

                {{-- INTERACTIVE LOCAL PDF PREVIEW --}}
                <div class="w-100 rounded-3 shadow-sm overflow-hidden"
                  style="height: clamp(420px, 70vh, 750px); background-color: #F8F5F2; border: 1px solid var(--earth-border);">
                  <object data="{{ asset('img/admin/wartas/' . $document->file_gambar) }}" type="application/pdf"
                    class="w-100 h-100">
                    <iframe src="{{ asset('img/admin/wartas/' . $document->file_gambar) }}" class="w-100 h-100"
                      style="border: none;">
                      <div class="p-5 text-center">
                        <p style="color: var(--earth-text-muted);">Browser Anda tidak mendukung preview PDF interaktif.</p>
                        <a href="{{ asset('img/admin/wartas/' . $document->file_gambar) }}" class="btn btn-earth px-4 py-2 rounded-pill">
                          Download PDF Warta
                        </a>
                      </div>
                    </iframe>
                  </object>
                </div>
              @else
                <div class="text-center py-5" style="color: var(--earth-text-muted);">
                  <i class="ri-file-close-line fs-1 mb-2 d-block" style="color: var(--earth-primary);"></i>
                  <p class="mb-0">Dokumen PDF belum diunggah untuk edisi warta ini.</p>
                </div>
              @endif
            @else
              <div class="text-center py-5" style="color: var(--earth-text-muted);">
                <i class="ri-newspaper-line fs-1 mb-2 d-block" style="color: var(--earth-primary);"></i>
                <p class="mb-0">Belum ada edisi warta jemaat aktif yang diterbitkan.</p>
              </div>
            @endif
          </div>
        </div>

        {{-- KANAN: ARSIP WARTA --}}
        <div class="col-lg-3 col-md-12">
          <div class="card-earth p-4 shadow-sm" style="background: #FFFFFF !important;">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3" style="border-color: var(--earth-border) !important;">
              <h5 class="fw-bold mb-0 font-serif" style="color: var(--earth-dark); font-size: 18px;">
                <i class="ri-archive-line me-1" style="color: var(--earth-primary);"></i> Arsip Edisi
              </h5>
              <span class="badge badge-earth px-2 py-0.5 rounded-pill" style="font-size: 11px;">
                {{ $arsipWartas->count() }} Edisi
              </span>
            </div>

            <div class="d-flex flex-column gap-2 overflow-auto" style="max-height: 520px; padding-right: 4px;">
              @forelse($arsipWartas as $arsip)
                @php
                  $isActiveWarta = isset($wartaUtama) && $wartaUtama->id == $arsip->id;
                @endphp
                <a href="{{ url('warta?warta_id=' . $arsip->id) }}"
                  class="d-flex justify-content-between align-items-center p-3 rounded-3 text-decoration-none transition"
                  style="background-color: {{ $isActiveWarta ? 'var(--earth-accent-soft)' : 'var(--earth-surface-warm)' }}; 
                         border: 1px solid {{ $isActiveWarta ? 'var(--earth-primary)' : 'var(--earth-border)' }};">
                  <div class="d-flex align-items-center gap-2.5">
                    <i class="ri-file-pdf-fill fs-20" style="color: {{ $isActiveWarta ? 'var(--earth-primary)' : '#9E9286' }};"></i>
                    <div>
                      <span class="d-block small fw-bold mb-0" style="color: var(--earth-dark);">{{ $arsip->judul_edisi }}</span>
                      <small style="color: var(--earth-text-muted); font-size: 11px;">
                        {{ \Carbon\Carbon::parse($arsip->tanggal_rilis)->translatedFormat('d M Y') }}
                      </small>
                    </div>
                  </div>
                  @if($isActiveWarta)
                    <span class="badge rounded-pill text-white" style="background: var(--earth-primary); font-size: 9.5px;">Aktif</span>
                  @endif
                </a>
              @empty
                <div class="text-center py-4 small" style="color: var(--earth-text-muted);">
                  Belum ada arsip warta lainnya.
                </div>
              @endforelse
            </div>

          </div>
        </div>

      </div>
    </div>
  </section>
@endsection
