<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CMS Login - Gereja Isa Almasih Surabaya</title>
  <link rel="shortcut icon" href="{{ asset('admin/assets/images/favicon.ico') }}?v=3">
  <link rel="icon" type="image/x-icon" href="{{ asset('admin/assets/images/favicon.ico') }}?v=3">
  <link rel="icon" type="image/png" href="{{ asset('homepage/assets/images/favicon.png') }}?v=3">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Figtree', system-ui, -apple-system, sans-serif;
      background: radial-gradient(1200px circle at 50% 0%, #1e293b 0%, #0f172a 45%, #0b0f19 100%);
      min-height: 100vh;
    }

    .btn-login-gradient {
      background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%) !important;
      box-shadow: 0 4px 16px rgba(37, 99, 235, 0.4) !important;
      transition: all 0.2s ease !important;
    }

    .btn-login-gradient:hover {
      background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 100%) !important;
      box-shadow: 0 6px 22px rgba(37, 99, 235, 0.55) !important;
      transform: translateY(-1px);
    }

    .btn-login-gradient:active {
      transform: translateY(0);
    }
  </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4 relative overflow-x-hidden selection:bg-blue-600 selection:text-white">

  {{-- Ambient Decorative Radial Glows (Tone CMS Navy & Blue) --}}
  <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-blue-600/10 blur-[120px] pointer-events-none rounded-full"></div>
  <div class="absolute bottom-0 right-1/4 w-[400px] h-[250px] bg-indigo-600/10 blur-[100px] pointer-events-none rounded-full"></div>

  <div class="w-full max-w-md bg-[#111827]/95 backdrop-blur-xl rounded-2xl shadow-2xl shadow-black/70 border border-slate-800/80 p-8 relative z-10">

    {{-- Header Logo & Title --}}
    <div class="text-center mb-6">
      <div class="inline-flex items-center justify-center p-2 mb-2">
        <img src="{{ asset('admin/assets/images/giaslogo-darkbg.png') }}" alt="Gereja Isa Almasih Surabaya"
          class="h-14 w-auto object-contain filter drop-shadow(0 2px 8px rgba(0,0,0,0.4)) transition-transform duration-200 hover:scale-105">
      </div>
      <h4 class="text-xl font-bold text-white tracking-tight mt-1">Log In Admin CMS</h4>
      <p class="text-xs text-slate-400 mt-1">Gereja Isa Almasih Surabaya</p>
    </div>

    {{-- Notifikasi --}}
    @if (session('error'))
      <div class="mb-5 p-3.5 bg-rose-500/15 border border-rose-500/30 text-rose-300 rounded-xl text-xs font-semibold shadow-sm flex items-center gap-2.5">
        <i class="ri-error-warning-fill text-rose-400 text-base shrink-0"></i>
        <span>{{ session('error') }}</span>
      </div>
    @endif

    @if (session('success'))
      <div class="mb-5 p-3.5 bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 rounded-xl text-xs font-semibold shadow-sm flex items-center gap-2.5">
        <i class="ri-checkbox-circle-fill text-emerald-400 text-base shrink-0"></i>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
      @csrf

      <div>
        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Alamat Email</label>
        <div class="relative">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 pointer-events-none">
            <i class="fa-regular fa-envelope text-sm"></i>
          </span>
          <input type="email" name="email" value="{{ old('email') }}"
            class="w-full text-sm bg-slate-900/80 border border-slate-700/80 text-white placeholder-slate-500 rounded-xl pl-10 pr-3.5 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition duration-150"
            placeholder="nama@domain.com" required autofocus>
        </div>
        @error('email')
          <span class="text-xs text-rose-400 mt-1.5 block font-medium flex items-center gap-1">
            <i class="ri-error-warning-line text-xs"></i> {{ $message }}
          </span>
        @enderror
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Kata Sandi</label>
        <div class="relative">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 pointer-events-none">
            <i class="fa-solid fa-lock text-sm"></i>
          </span>
          <input type="password" name="password"
            class="w-full text-sm bg-slate-900/80 border border-slate-700/80 text-white placeholder-slate-500 rounded-xl pl-10 pr-3.5 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition duration-150"
            placeholder="••••••••" required>
        </div>
      </div>

      <div class="flex items-center justify-between pt-1">
        <label class="flex items-center text-xs text-slate-400 hover:text-slate-300 font-medium cursor-pointer select-none transition">
          <input type="checkbox" name="remember"
            class="rounded border-slate-700 bg-slate-900 text-blue-600 focus:ring-blue-500/30 me-2 cursor-pointer">
          Ingat Saya
        </label>
      </div>

      <button type="submit"
        class="w-full btn-login-gradient text-white text-sm font-bold py-3 rounded-xl cursor-pointer mt-2 flex items-center justify-center gap-2">
        <span>Masuk Dashboard</span>
        <i class="ri-arrow-right-line text-base"></i>
      </button>
    </form>

    {{-- Footer Link: Kembali ke Beranda --}}
    <div class="mt-6 pt-5 border-t border-slate-800/80 text-center">
      <a href="{{ route('home') }}"
        class="text-xs text-slate-400 hover:text-blue-400 transition inline-flex items-center gap-1.5 font-medium">
        <i class="ri-arrow-left-line text-sm"></i>
        <span>Kembali ke Beranda Publik</span>
      </a>
    </div>

  </div>

</body>

</html>