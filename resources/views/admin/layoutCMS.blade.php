<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>CMS Admin Panel - Attex Tailwind</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="CMS Admin Theme" name="description">
  <meta name="author" content="coderthemes">

  <link rel="shortcut icon" href="{{ asset('admin/assets/images/favicon.ico') }}">
  <link href="{{ asset('admin/assets/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('admin/assets/css/app.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">

  {{-- Tambahan FontAwesome jika belum ter-load dari app.min.css untuk ikon modal --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <script src="{{ asset('admin/assets/js/config.js') }}"></script>
</head>

<body>
  <div class="flex wrapper">

    {{-- SIDEBAR MENU --}}
    <div class="app-menu">
      <a href="{{ route('admin.dashboard') }}" class="logo-box">
        <div class="logo-light">
          <span class="logo-lg text-white font-bold text-xl flex items-center justify-center h-16 bg-primary">
            ⛪ CMS GEREJA
          </span>
          <span class="logo-sm text-white font-bold text-lg bg-primary h-16 flex items-center justify-center">
            ⛪
          </span>
        </div>
      </a>

      <div data-simplebar>
        <ul class="menu" id="sidenav-menu">
          <li class="menu-title">Navigation</li>

          <li class="menu-item">
            <a href="{{ route('admin.dashboard') }}"
              class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
              <span class="menu-icon"><i class="ri-dashboard-line"></i></span>
              <span class="menu-text"> Dashboard </span>
            </a>
          </li>

          <li class="menu-title">Apps</li>

          <li class="menu-item">
            <a href="{{ route('admin.event.index') }}"
              class="menu-link {{ request()->routeIs('admin.event.*') ? 'active' : '' }}">
              <span class="menu-icon"><i class="ri-calendar-event-line"></i></span>
              <span class="menu-text"> Kelola Event </span>
            </a>
          </li>

          <li class="menu-item">
            <a href="{{ route('admin.warta.index') }}"
              class="menu-link {{ request()->routeIs('admin.warta.*') ? 'active' : '' }}">
              <span class="menu-icon"><i class="ri-article-line"></i></span>
              <span class="menu-text"> Kelola Warta </span>
            </a>
          </li>

          <!-- 📊 MENU ABSENSI (DI BAWAH KELOLA WARTA & DI ATAS MASTER DATA) -->
          <li class="menu-item">
            <a href="javascript:void(0)" data-fc-type="collapse"
              class="menu-link {{ request()->routeIs('admin.attendance.*') ? 'open' : '' }}">
              <span class="menu-icon"><i class="ri-checkbox-circle-line"></i></span>
              <span class="menu-text"> Absensi </span>
              <span class="menu-arrow"></span>
            </a>

            <ul class="sub-menu {{ request()->routeIs('admin.attendance.*') ? '' : 'hidden' }}">
              <!-- 1. Input Kehadiran -->
              <li class="menu-item">
                <a href="{{ route('admin.attendance.input') }}"
                  class="menu-link {{ request()->routeIs('admin.attendance.input') ? 'active' : '' }}">
                  <span class="menu-text"> Input Kehadiran </span>
                </a>
              </li>

              <!-- 2. Riwayat Sesi Ibadah -->
              <li class="menu-item">
                <a href="{{ route('admin.attendance.history') }}"
                  class="menu-link {{ request()->routeIs('admin.attendance.history') ? 'active' : '' }}">
                  <span class="menu-text"> Riwayat Sesi Ibadah </span>
                </a>
              </li>

              <!-- 3. Laporan -->
              <li class="menu-item">
                <a href="{{ route('admin.attendance.report') }}"
                  class="menu-link {{ request()->routeIs('admin.attendance.report') ? 'active' : '' }}">
                  <span class="menu-text"> Laporan </span>
                </a>
              </li>
            </ul>
          </li>

          <!-- 💾 MENU MASTER DATA -->
          <li class="menu-item">
            <a href="javascript:void(0)" data-fc-type="collapse"
              class="menu-link {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.cabang.*') || request()->routeIs('admin.ibadah.*') ? 'open' : '' }}">
              <span class="menu-icon"><i class="ri-database-2-line"></i></span>
              <span class="menu-text"> Master Data </span>
              <span class="menu-arrow"></span>
            </a>

            <ul
              class="sub-menu {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.cabang.*') || request()->routeIs('admin.ibadah.*') ? '' : 'hidden' }}">
              <!-- 🏬 1. Kelola Cabang -->
              <li class="menu-item">
                <a href="{{ route('admin.cabang.index') }}"
                  class="menu-link {{ request()->routeIs('admin.cabang.*') ? 'active' : '' }}">
                  <span class="menu-text"> Kelola Cabang </span>
                </a>
              </li>

              <!-- ⛪ 2. Kelola Ibadah -->
              <li class="menu-item">
                <a href="{{ route('admin.ibadah.index') }}"
                  class="menu-link {{ request()->routeIs('admin.ibadah.*') ? 'active' : '' }}">
                  <span class="menu-text"> Kelola Ibadah </span>
                </a>
              </li>

              <!-- 👤 Kelola Jemaat -->
              <li class="menu-item">
                <a href="{{ route('admin.jemaat.index') }}"
                  class="menu-link {{ request()->routeIs('admin.jemaat.*') ? 'active' : '' }}">
                  <span class="menu-text"> Kelola Jemaat </span>
                </a>
              </li>

              <!-- 👤 3. Kelola Staf / Role -->
              <li class="menu-item">
                <a href="{{ route('admin.users.index') }}"
                  class="menu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                  <span class="menu-text"> Kelola Staf / Role </span>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>

    {{-- KONTEN HALAMAN UTAMA --}}
    <div class="page-content">

      <header class="app-header flex items-center px-4 gap-3 bg-white shadow">
        <button id="button-toggle-menu" class="nav-link p-2">
          <span class="sr-only">Toggle Menu</span>
          <span class="ri-menu-fold-line text-2xl"></span>
        </button>

        <div class="ms-auto flex items-center gap-5">
          <span class="text-gray-600 font-semibold text-sm">
            Selamat Datang, <b>{{ Auth::user()->name }}</b> ({{ ucfirst(Auth::user()->role) }})
          </span>

          {{-- 🔑 Tombol Pemicu Modal Ganti Password (Ditaruh rapi di Header sebelah Logout) --}}
          <button type="button" onclick="document.getElementById('changePasswordModal').style.display='flex'"
            class="flex items-center gap-1.5 text-slate-600 hover:text-primary transition text-sm font-semibold bg-transparent border-none cursor-pointer">
            <i class="fa-solid fa-key"></i> Ganti Password
          </button>

          {{-- 🚪 Form Tombol Keluar --}}
          <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0; padding: 0;">
            @csrf
            <button type="submit"
              class="flex items-center gap-1.5 text-slate-600 hover:text-red-600 transition text-sm font-semibold bg-transparent border-none cursor-pointer">
              <i class="fa-solid fa-right-from-bracket"></i> Keluar
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
