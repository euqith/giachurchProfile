<!DOCTYPE html>
<html lang="en" data-mode="light" data-menu-color="dark">

<head>
  <meta charset="utf-8">
  <title>CMS Admin Panel - Gereja Isa Almasih Surabaya</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="CMS Admin Theme" name="description">
  <meta name="author" content="GIA Surabaya">

  <link rel="shortcut icon" href="{{ asset('admin/assets/images/favicon.ico') }}?v=3">
  <link rel="icon" type="image/x-icon" href="{{ asset('admin/assets/images/favicon.ico') }}?v=3">
  <link rel="icon" type="image/png" href="{{ asset('homepage/assets/images/favicon.png') }}?v=3">
  <link rel="apple-touch-icon" href="{{ asset('homepage/assets/images/favicon.png') }}?v=3">
  <link href="{{ asset('admin/assets/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('admin/assets/css/app.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">

  {{-- Vite Tailwind CSS & JS Assets --}}
  @if (file_exists(public_path('build/manifest.json')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  @else
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  @endif

  {{-- Tambahan FontAwesome jika belum ter-load dari app.min.css untuk ikon modal --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <script src="{{ asset('admin/assets/js/config.js') }}"></script>

  <style>
    /* ======================================================== */
    /* MODERN EXECUTIVE CMS THEME & SIDEBAR STYLING            */
    /* ======================================================== */
    :root {
      --tw-sidenav-width: 265px !important;
      --cms-primary: #8C5E3C;
      --cms-primary-hover: #734B2E;
      --cms-accent: #C27D38;
      --cms-sidebar-bg: #111827;
      --cms-sidebar-hover: rgba(255, 255, 255, 0.07);
    }

    body {
      background-color: #F8FAFC !important;
      color: #1E293B !important;
      font-family: 'Figtree', system-ui, -apple-system, sans-serif !important;
      -webkit-font-smoothing: antialiased;
    }

    .page-content {
      background-color: #F8FAFC !important;
      min-height: 100vh;
      flex: 1 1 0% !important;
      min-width: 0 !important;
      margin-left: 0 !important;
    }

    html[data-sidenav-view=mobile] .app-menu {
      position: fixed !important;
      top: 0 !important;
      bottom: 0 !important;
      left: 0 !important;
    }

    /* 1. SIDEBAR MODERNIZATION & SCROLLABLE EXPAND */
    .app-menu {
      width: 265px !important;
      min-width: 265px !important;
      max-width: 265px !important;
      height: 100vh !important;
      max-height: 100vh !important;
      background-color: var(--cms-sidebar-bg) !important;
      border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
      box-shadow: 4px 0 25px rgba(0, 0, 0, 0.28) !important;
      display: flex !important;
      flex-direction: column !important;
      overflow: hidden !important;
      position: sticky !important;
      top: 0 !important;
      z-index: 40 !important;
    }

    .app-menu [data-simplebar],
    .app-menu .sidebar-scroll-wrapper,
    .app-menu .simplebar-content-wrapper {
      height: calc(100vh - 82px) !important;
      max-height: calc(100vh - 82px) !important;
      overflow-y: auto !important;
      overflow-x: hidden !important;
      flex: 1 1 auto !important;
      scrollbar-width: thin !important;
      scrollbar-color: rgba(255, 255, 255, 0.2) transparent !important;
    }

    /* Sleek Custom Scrollbar for Sidebar */
    .app-menu ::-webkit-scrollbar {
      width: 5px !important;
    }
    .app-menu ::-webkit-scrollbar-track {
      background: transparent !important;
    }
    .app-menu ::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.18) !important;
      border-radius: 999px !important;
    }
    .app-menu ::-webkit-scrollbar-thumb:hover {
      background: rgba(255, 255, 255, 0.35) !important;
    }

    .app-menu .logo-box {
      background: transparent !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
      padding: 16px 14px !important;
      display: block !important;
      text-decoration: none !important;
      flex-shrink: 0 !important;
    }

    .app-menu .logo-box img {
      filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
      transition: transform 0.2s ease;
    }

    .app-menu .logo-box:hover img {
      transform: scale(1.02);
    }

    .app-menu .menu-title {
      color: #94A3B8 !important;
      font-size: 11px !important;
      font-weight: 700 !important;
      letter-spacing: 0.08em !important;
      text-transform: uppercase !important;
      padding: 16px 18px 6px !important;
    }

    .app-menu .menu-item {
      margin: 3px 10px !important;
    }

    .app-menu .menu-link {
      color: #CBD5E1 !important;
      font-size: 13px !important;
      font-weight: 500 !important;
      border-radius: 12px !important;
      padding: 10px 14px !important;
      display: flex !important;
      align-items: center !important;
      gap: 10px !important;
      transition: all 0.2s ease !important;
      text-decoration: none !important;
      white-space: nowrap !important;
    }

    .app-menu .menu-link .menu-text {
      white-space: nowrap !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
      flex: 1 1 auto !important;
    }

    .app-menu .menu-link:hover {
      background-color: var(--cms-sidebar-hover) !important;
      color: #FFFFFF !important;
      transform: translateX(2px);
    }

    .app-menu .menu-link .menu-icon {
      font-size: 18px !important;
      color: #94A3B8 !important;
      transition: color 0.2s ease;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      flex-shrink: 0 !important;
    }

    .app-menu .menu-link:hover .menu-icon {
      color: #FFFFFF !important;
    }

    /* Parent Menu Item when Open / Expanded (Clean soft container, no solid competing block) */
    .app-menu .menu-link.open {
      background: rgba(255, 255, 255, 0.08) !important;
      color: #FFFFFF !important;
      font-weight: 600 !important;
      box-shadow: none !important;
    }

    .app-menu .menu-link.open .menu-icon {
      color: #60A5FA !important;
    }

    /* Active Menu Item: Bright Modern Royal Blue / Vivid Azure (High contrast on dark navy) */
    .app-menu .menu-link.active {
      background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%) !important;
      color: #FFFFFF !important;
      font-weight: 600 !important;
      box-shadow: 0 4px 16px rgba(37, 99, 235, 0.45) !important;
    }

    .app-menu .menu-link.active .menu-icon {
      color: #FFFFFF !important;
    }

    /* Submenu tree */
    .app-menu .sub-menu {
      background: transparent !important;
      padding: 4px 0 6px 10px !important;
      margin-left: 14px !important;
      border-left: 1px solid rgba(255, 255, 255, 0.12) !important;
    }

    .app-menu .sub-menu .menu-item {
      margin: 2px 4px !important;
    }

    .app-menu .sub-menu .menu-link {
      padding: 8px 12px !important;
      font-size: 12.5px !important;
      border-radius: 8px !important;
      color: #94A3B8 !important;
      white-space: nowrap !important;
    }

    .app-menu .sub-menu .menu-link .menu-text {
      white-space: nowrap !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
    }

    .app-menu .sub-menu .menu-link:hover {
      color: #FFFFFF !important;
      background-color: rgba(255, 255, 255, 0.08) !important;
    }

    .app-menu .sub-menu .menu-link.active {
      background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%) !important;
      color: #FFFFFF !important;
      font-weight: 600 !important;
      box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4) !important;
      border: none !important;
    }

    /* 2. HEADER TOPBAR */
    .app-header {
      background: rgba(255, 255, 255, 0.95) !important;
      backdrop-filter: blur(10px) !important;
      -webkit-backdrop-filter: blur(10px) !important;
      border-bottom: 1px solid #E2E8F0 !important;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
      height: 64px !important;
    }

    /* 3. MODERN CARD & DATA GRID / TABLE STYLING */
    .table-modern-wrapper {
      background: #FFFFFF !important;
      border-radius: 16px !important;
      border: 1px solid #E2E8F0 !important;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 10px 15px -3px rgba(0, 0, 0, 0.02) !important;
      overflow: hidden !important;
    }

    .page-content table {
      width: 100% !important;
      background-color: #FFFFFF !important;
      border-collapse: separate !important;
      border-spacing: 0 !important;
    }

    .page-content table thead {
      background-color: #F8FAFC !important;
      border-bottom: 1px solid #E2E8F0 !important;
    }

    .page-content table thead th {
      color: #475569 !important;
      font-size: 11px !important;
      font-weight: 700 !important;
      letter-spacing: 0.06em !important;
      text-transform: uppercase !important;
      padding: 13px 18px !important;
      border-bottom: 1px solid #E2E8F0 !important;
      background-color: #F8FAFC !important;
    }

    .page-content table tbody tr {
      background-color: #FFFFFF !important;
      border-bottom: 1px solid #F1F5F9 !important;
      transition: background-color 0.15s ease !important;
    }

    .page-content table tbody tr:hover {
      background-color: #F8FAFC !important;
    }

    .page-content table tbody td {
      padding: 14px 18px !important;
      color: #334155 !important;
      border-bottom: 1px solid #F1F5F9 !important;
      vertical-align: middle !important;
    }

    /* 4. MODERN BUTTONS */
    .btn-cms-primary {
      background: linear-gradient(135deg, #8C5E3C 0%, #704729 100%) !important;
      color: #FFFFFF !important;
      font-weight: 600 !important;
      font-size: 13px !important;
      padding: 9px 18px !important;
      border-radius: 12px !important;
      border: none !important;
      box-shadow: 0 4px 14px rgba(140, 94, 60, 0.25) !important;
      display: inline-flex !important;
      align-items: center !important;
      gap: 7px !important;
      transition: all 0.2s ease !important;
      cursor: pointer !important;
      text-decoration: none !important;
    }

    .btn-cms-primary:hover {
      background: linear-gradient(135deg, #7A4F30 0%, #5E391E 100%) !important;
      box-shadow: 0 6px 20px rgba(140, 94, 60, 0.35) !important;
      transform: translateY(-1px) !important;
      color: #FFFFFF !important;
    }

    .btn-action-edit {
      display: inline-flex !important;
      align-items: center !important;
      gap: 5px !important;
      padding: 5px 11px !important;
      border-radius: 8px !important;
      font-size: 12px !important;
      font-weight: 600 !important;
      background-color: #FEF3C7 !important;
      color: #92400E !important;
      border: 1px solid #FDE68A !important;
      transition: all 0.15s ease !important;
      text-decoration: none !important;
      cursor: pointer !important;
    }

    .btn-action-edit:hover {
      background-color: #FDE68A !important;
      color: #78350F !important;
      transform: translateY(-1px);
    }

    .btn-action-delete {
      display: inline-flex !important;
      align-items: center !important;
      gap: 5px !important;
      padding: 5px 11px !important;
      border-radius: 8px !important;
      font-size: 12px !important;
      font-weight: 600 !important;
      background-color: #FEE2E2 !important;
      color: #B91C1C !important;
      border: 1px solid #FECACA !important;
      transition: all 0.15s ease !important;
      text-decoration: none !important;
      cursor: pointer !important;
    }

    .btn-action-delete:hover {
      background-color: #FECACA !important;
      color: #991B1B !important;
      transform: translateY(-1px);
    }
  </style>
</head>

<body>
  <!-- LOADING SCREEN CMS: DENGAN LOGO GEREJA RESMI -->
  <div id="cms-loading" style="position: fixed; inset: 0; background: #ffffff; z-index: 999999; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: opacity 0.35s ease, visibility 0.35s ease;">
    <div style="text-align: center; display: flex; flex-direction: column; align-items: center;">
      <img src="{{ asset('homepage/assets/images/logo/giaslogosmall.png') }}" alt="Gereja Isa Almasih Surabaya" style="max-width: 220px; height: auto; margin-bottom: 16px; animation: pulse 1.6s ease-in-out infinite;">
      <div style="display: flex; align-items: center; gap: 8px;">
        <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #2563eb; display: inline-block; animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
        <span style="font-size: 11px; font-weight: 700; color: #64748b; letter-spacing: 0.8px; text-transform: uppercase;">Memuat Panel CMS...</span>
      </div>
    </div>
  </div>
  <script>
    window.addEventListener('DOMContentLoaded', function() {
      setTimeout(function() {
        var loader = document.getElementById('cms-loading');
        if (loader) {
          loader.style.opacity = '0';
          loader.style.pointerEvents = 'none';
          setTimeout(function() { loader.remove(); }, 350);
        }
      }, 200);
    });
  </script>

  <div class="flex wrapper">

    {{-- SIDEBAR MENU --}}
    <div class="app-menu">
      <a href="{{ route('admin.dashboard') }}" class="logo-box block py-4 px-3 hover:opacity-95 transition" style="background: transparent;">
        <div class="flex items-center justify-center">
          <img src="{{ asset('admin/assets/images/giaslogo-darkbg.png') }}" alt="Gereja Isa Almasih Surabaya" class="h-12 w-auto object-contain" style="max-height: 48px;">
        </div>
      </a>

      <div data-simplebar class="sidebar-scroll-wrapper" style="height: calc(100vh - 82px); max-height: calc(100vh - 82px); overflow-y: auto; overflow-x: hidden;">
        <ul class="menu" id="sidenav-menu">
          <li class="menu-title">Navigation</li>

          @if(Auth::user()->canAccess('dashboard'))
          <li class="menu-item">
            <a href="{{ route('admin.dashboard') }}"
              class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
              <span class="menu-icon"><i class="ri-dashboard-line"></i></span>
              <span class="menu-text"> Dashboard </span>
            </a>
          </li>
          @endif

          @if(Auth::user()->canAccessGroup(['event', 'warta', 'stream', 'article']))
          <li class="menu-title">Apps</li>

          @if(Auth::user()->canAccess('event'))
          <li class="menu-item">
            <a href="{{ route('admin.event.index') }}"
              class="menu-link {{ request()->routeIs('admin.event.*') ? 'active' : '' }}">
              <span class="menu-icon"><i class="ri-calendar-event-line"></i></span>
              <span class="menu-text"> Kelola Event </span>
            </a>
          </li>
          @endif

          @if(Auth::user()->canAccess('warta'))
          <li class="menu-item">
            <a href="{{ route('admin.warta.index') }}"
              class="menu-link {{ request()->routeIs('admin.warta.*') ? 'active' : '' }}">
              <span class="menu-icon"><i class="ri-article-line"></i></span>
              <span class="menu-text"> Kelola Warta </span>
            </a>
          </li>
          @endif

          {{-- 📡 KELOLA LIVE STREAMING --}}
          @if(Auth::user()->canAccess('stream'))
          <li class="menu-item">
            <a href="{{ route('admin.stream.index') }}"
              class="menu-link {{ request()->routeIs('admin.stream.*') ? 'active' : '' }}">
              <span class="menu-icon"><i class="ri-live-line"></i></span>
              <span class="menu-text"> Kelola Streaming </span>
            </a>
          </li>
          @endif

          {{-- 📰 KELOLA ARTIKEL & POSTINGAN --}}
          @if(Route::has('admin.article.index') && Auth::user()->canAccess('article'))
          <li class="menu-item">
            <a href="{{ route('admin.article.index') }}"
              class="menu-link {{ request()->routeIs('admin.article.*') ? 'active' : '' }}">
              <span class="menu-icon"><i class="ri-newspaper-line"></i></span>
              <span class="menu-text"> Kelola Artikel </span>
            </a>
          </li>
          @endif
          @endif

          <!-- 📊 MENU ABSENSI (DI BAWAH KELOLA WARTA & DI ATAS MASTER DATA) -->
          @if(Auth::user()->canAccessGroup(['attendance.persiapan', 'attendance.input', 'attendance.history', 'attendance.report']))
          <li class="menu-item">
            <a href="javascript:void(0)" data-fc-type="collapse"
              class="menu-link {{ request()->routeIs('admin.attendance.*') ? 'open' : '' }}">
              <span class="menu-icon"><i class="ri-checkbox-circle-line"></i></span>
              <span class="menu-text"> Absensi </span>
              <span class="menu-arrow"></span>
            </a>

            <ul class="sub-menu {{ request()->routeIs('admin.attendance.*') ? '' : 'hidden' }} px-2 py-1.5 space-y-1">
              <!-- 1. Persiapan Ibadah -->
              @if(Auth::user()->canAccess('attendance.persiapan'))
              <li class="menu-item">
                <a href="{{ route('admin.attendance.persiapan') }}"
                  class="menu-link flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm transition-all {{ request()->routeIs('admin.attendance.persiapan*') ? 'active bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                  <i class="ri-book-open-line text-base shrink-0"></i>
                  <span class="menu-text"> Persiapan Ibadah </span>
                </a>
              </li>
              @endif

              <!-- 2. Input Kehadiran -->
              @if(Auth::user()->canAccess('attendance.input'))
              <li class="menu-item">
                <a href="{{ route('admin.attendance.input') }}"
                  class="menu-link flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm transition-all {{ request()->routeIs('admin.attendance.input') ? 'active bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                  <i class="ri-clipboard-line text-base shrink-0"></i>
                  <span class="menu-text"> Input Kehadiran </span>
                </a>
              </li>
              @endif

              <!-- 3. Riwayat Sesi -->
              @if(Auth::user()->canAccess('attendance.history'))
              <li class="menu-item">
                <a href="{{ route('admin.attendance.history') }}"
                  class="menu-link flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm transition-all {{ request()->routeIs('admin.attendance.history*') ? 'active bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                  <i class="ri-history-line text-base shrink-0"></i>
                  <span class="menu-text"> Riwayat Sesi </span>
                </a>
              </li>
              @endif

              <!-- 4. Laporan -->
              @if(Auth::user()->canAccess('attendance.report'))
              <li class="menu-item">
                <a href="{{ route('admin.attendance.report') }}"
                  class="menu-link flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm transition-all {{ request()->routeIs('admin.attendance.report') ? 'active bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                  <i class="ri-bar-chart-2-line text-base shrink-0"></i>
                  <span class="menu-text"> Laporan </span>
                </a>
              </li>
              @endif
            </ul>
          </li>
          @endif

          <!-- 💾 MENU MASTER DATA -->
          @if(Auth::user()->canAccessGroup(['church', 'cabang', 'ibadah', 'sesi', 'jemaat', 'users', 'roles']))
          <li class="menu-item">
            <a href="javascript:void(0)" data-fc-type="collapse"
              class="menu-link {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.cabang.*') || request()->routeIs('admin.ibadah.*') || request()->routeIs('admin.sesi.*') || request()->routeIs('admin.jemaat.*') || request()->routeIs('admin.church.*') || request()->routeIs('admin.roles.*') ? 'open' : '' }}">
              <span class="menu-icon"><i class="ri-database-2-line"></i></span>
              <span class="menu-text"> Master Data </span>
              <span class="menu-arrow"></span>
            </a>

            <ul
              class="sub-menu {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.cabang.*') || request()->routeIs('admin.ibadah.*') || request()->routeIs('admin.sesi.*') || request()->routeIs('admin.jemaat.*') || request()->routeIs('admin.church.*') || request()->routeIs('admin.roles.*') ? '' : 'hidden' }}">
              <!-- 🏛️ 1. Profil Gereja -->
              @if(Auth::user()->canAccess('church'))
              <li class="menu-item">
                <a href="{{ route('admin.church.index') }}"
                  class="menu-link {{ request()->routeIs('admin.church.*') ? 'active' : '' }}">
                  <i class="ri-building-line text-sm me-1"></i>
                  <span class="menu-text"> Profil Gereja </span>
                </a>
              </li>
              @endif

              <!-- 🏬 2. Kelola Wilayah -->
              @if(Auth::user()->canAccess('cabang'))
              <li class="menu-item">
                <a href="{{ route('admin.cabang.index') }}"
                  class="menu-link {{ request()->routeIs('admin.cabang.*') ? 'active' : '' }}">
                  <i class="ri-map-pin-line text-sm me-1"></i>
                  <span class="menu-text"> Kelola Wilayah </span>
                </a>
              </li>
              @endif

              <!-- ⛪ 3. Kelola Ibadah -->
              @if(Auth::user()->canAccess('ibadah'))
              <li class="menu-item">
                <a href="{{ route('admin.ibadah.index') }}"
                  class="menu-link {{ request()->routeIs('admin.ibadah.*') ? 'active' : '' }}">
                  <i class="ri-service-line text-sm me-1"></i>
                  <span class="menu-text"> Kelola Ibadah </span>
                </a>
              </li>
              @endif

              <!-- ⏰ 4. Kelola Sesi -->
              @if(Auth::user()->canAccess('sesi'))
              <li class="menu-item">
                <a href="{{ route('admin.sesi.index') }}"
                  class="menu-link {{ request()->routeIs('admin.sesi.*') ? 'active' : '' }}">
                  <i class="ri-time-line text-sm me-1"></i>
                  <span class="menu-text"> Kelola Sesi </span>
                </a>
              </li>
              @endif

              <!-- 👤 5. Kelola Jemaat -->
              @if(Auth::user()->canAccess('jemaat'))
              <li class="menu-item">
                <a href="{{ route('admin.jemaat.index') }}"
                  class="menu-link {{ request()->routeIs('admin.jemaat.*') ? 'active' : '' }}">
                  <i class="ri-user-shared-line text-sm me-1"></i>
                  <span class="menu-text"> Kelola Jemaat </span>
                </a>
              </li>
              @endif

              <!-- 👤 6. Kelola Staf / Pengguna -->
              @if(Auth::user()->canAccess('users'))
              <li class="menu-item">
                <a href="{{ route('admin.users.index') }}"
                  class="menu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                  <i class="ri-shield-user-line text-sm me-1"></i>
                  <span class="menu-text"> Kelola Staf / Role </span>
                </a>
              </li>
              @endif

              <!-- 🛡️ 7. Role Setting -->
              @if(Route::has('admin.roles.index') && Auth::user()->canAccess('roles'))
              <li class="menu-item">
                <a href="{{ route('admin.roles.index') }}"
                  class="menu-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                  <i class="ri-shield-keyhole-line text-sm me-1"></i>
                  <span class="menu-text"> Role Setting </span>
                </a>
              </li>
              @endif
            </ul>
          </li>
          @endif
        </ul>
        </ul>
      </div>
    </div>

    {{-- KONTEN HALAMAN UTAMA --}}
    <div class="page-content">

      <header class="app-header flex items-center px-5 gap-3 bg-white">
        <button id="button-toggle-menu" class="nav-link p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
          <span class="sr-only">Toggle Menu</span>
          <span class="ri-menu-fold-line text-xl"></span>
        </button>

        <div class="ms-auto flex items-center gap-3">
          <div class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100/80 border border-slate-200/60 text-xs font-semibold text-slate-700">
            <span class="w-6 h-6 rounded-full bg-amber-600/15 text-amber-800 flex items-center justify-center font-bold text-[11px]">
              {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </span>
            <span>Selamat Datang, <b>{{ Auth::user()->name }}</b></span>
            <span class="bg-amber-100 text-amber-900 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">
              {{ Auth::user()->role }}
            </span>
          </div>

          {{-- 🔑 Tombol Pemicu Modal Ganti Password --}}
          <button type="button" onclick="document.getElementById('changePasswordModal').style.display='flex'"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold transition border border-slate-200 shadow-2xs cursor-pointer">
            <i class="fa-solid fa-key text-[11px] text-amber-600"></i>
            <span class="hidden md:inline">Ganti Password</span>
          </button>

          {{-- 🚪 Form Tombol Keluar --}}
          <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0; padding: 0;">
            @csrf
            <button type="submit"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold transition border border-rose-200/80 shadow-2xs cursor-pointer">
              <i class="fa-solid fa-right-from-bracket text-[11px]"></i>
              <span>Keluar</span>
            </button>
          </form>
        </div>
      </header>

      <main class="p-6">
        @yield('content')
      </main>

      <footer
        class="footer h-15 flex items-center px-6 border-t border-gray-200 mt-auto bg-white text-sm text-gray-500">
        <div>{{ date('Y') }} © CMS Gereja - Attex Tailwind Engine</div>
      </footer>

    </div>
  </div>

  {{-- 🔑 POP UP MODAL: GANTI PASSWORD MANDIRI (Ditaruh aman di dalam body sebelum tag script) --}}
  <div id="changePasswordModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: {{ $errors->any() ? 'flex' : 'none' }}; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-xl w-full max-w-md shadow-xl mx-4">
      <div class="flex justify-between items-center mb-4">
        <h5 class="font-bold text-slate-900 flex items-center gap-2">
          <i class="fa-solid fa-shield-halved text-primary"></i> Ganti Password Saya
        </h5>
        <button type="button" onclick="document.getElementById('changePasswordModal').style.display='none'"
          class="text-gray-400 hover:text-gray-600 font-bold">✕</button>
      </div>

      <form action="{{ route('admin.profile.password.update') }}" method="POST"
        style="display: flex; flex-direction: column; gap: 14px;">
        @csrf @method('PATCH')

        {{-- 🔴 MENAMPILKAN ERROR VALIDASI PASSWORD JIKA ADA --}}
        @if ($errors->any())
          <div
            style="background-color: #fef2f2; border: 1px solid #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">
            <ul style="margin: 0; padding-left: 16px; list-style-type: disc;">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- Sisa input form di bawahnya tetap sama --}}

        <div>
          <label class="block text-slate-500 text-xs font-bold mb-1">Password Saat Ini (Lama)</label>
          <input type="password" name="current_password"
            class="w-full text-sm border border-gray-200 rounded px-3 py-2 outline-none focus:border-primary"
            placeholder="Masukkan password lama..." required>
        </div>

        <div>
          <label class="block text-slate-500 text-xs font-bold mb-1">Password Baru</label>
          <input type="password" name="password"
            class="w-full text-sm border border-gray-200 rounded px-3 py-2 outline-none focus:border-primary"
            placeholder="Minimal 8 karakter..." required>
        </div>

        <div>
          <label class="block text-slate-500 text-xs font-bold mb-1">Konfirmasi Password Baru</label>
          <input type="password" name="password_confirmation"
            class="w-full text-sm border border-gray-200 rounded px-3 py-2 outline-none focus:border-primary"
            placeholder="Ulangi password baru..." required>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 8px;">
          <button type="button" onclick="document.getElementById('changePasswordModal').style.display='none'"
            style="background-color: #f3f4f6 !important; color: #4b5563 !important; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: bold; border: 1px solid #e5e7eb !important; cursor: pointer;">
            Batal
          </button>
          <button type="submit"
            class="bg-primary hover:bg-primary-dark text-white text-sm font-bold px-4 py-2 rounded-lg shadow transition">
            Simpan Password
          </button>
        </div>
      </form>
    </div>
  </div>

  <script src="{{ asset('admin/assets/libs/simplebar/simplebar.min.js') }}"></script>
  <script src="{{ asset('admin/assets/libs/lucide/umd/lucide.min.js') }}"></script>
  <script src="{{ asset('admin/assets/libs/@frostui/tailwindcss/frostui.js') }}"></script>
  <script src="{{ asset('admin/assets/js/app.js') }}"></script>
</body>

</html>
