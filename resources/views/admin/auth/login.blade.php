<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CMS Login - Sadaka Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">

  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
    <div class="text-center mb-6">
      <div
        class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-orange-100 text-orange-600 font-bold text-2xl mb-3 shadow-sm">
        S
      </div>
      <h4 class="text-xl font-bold text-slate-950">Log In Admin CMS</h4>
      <p class="text-sm text-slate-400 mt-1">Gunakan akun staf terdaftar untuk mengelola situs.</p>
    </div>

    {{-- Notifikasi --}}
    @if (session('error'))
      <div class="mb-4 p-3 bg-red-500 text-white rounded-lg text-xs font-semibold shadow-sm">
        ❌ {{ session('error') }}
      </div>
    @endif
    @if (session('success'))
      <div class="mb-4 p-3 bg-emerald-500 text-white rounded-lg text-xs font-semibold shadow-sm">
        ✅ {{ session('success') }}
      </div>
    @endif

    <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
      @csrf

      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Alamat Email</label>
        <div class="relative">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
            <i class="fa-regular fa-envelope"></i>
          </span>
          <input type="email" name="email" value="{{ old('email') }}"
            class="w-full text-sm border border-gray-200 rounded-lg pl-10 pr-3 py-2.5 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition"
            placeholder="nama@domain.com" required autofocus>
        </div>
        @error('email')
          <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span>
        @enderror
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kata Sandi</label>
        <div class="relative">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
            <i class="fa-solid fa-lock"></i>
          </span>
          <input type="password" name="password"
            class="w-full text-sm border border-gray-200 rounded-lg pl-10 pr-3 py-2.5 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition"
            placeholder="••••••••" required>
        </div>
      </div>

      <div class="flex items-center justify-between pt-1">
        <label class="flex items-center text-xs text-slate-500 font-medium cursor-pointer select-none">
          <input type="checkbox" name="remember"
            class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 me-2">
          Ingat Saya
        </label>
      </div>

      <button type="submit"
        class="w-full bg-orange-600 hover:bg-orange-700 text-white text-sm font-bold py-2.5 rounded-lg shadow-md hover:shadow-lg transition cursor-pointer mt-2">
        Masuk Dashboard
      </button>
    </form>
  </div>

</body>

</html>
