@extends('admin.layoutCMS')

@section('content')
  <div class="flex justify-between items-center mb-6">
    <div>
      <h4 class="text-slate-900 dark:text-slate-200 text-lg font-bold mb-1">Master Manajemen Pengguna & Pekerja</h4>
      <p class="text-slate-400 text-sm">Kelola hak akses admin, fulltimer, dan pembagian tugas situs.</p>
    </div>
    {{-- Tombol Utama Tambah User --}}
    <button type="button" onclick="document.getElementById('createUserModal').style.display='flex'"
      class="bg-primary hover:bg-primary-dark text-white text-sm font-bold px-4 py-2.5 rounded-lg shadow transition flex items-center gap-2">
      <i class="fa-solid fa-user-plus"></i> Tambah User
    </button>
  </div>

  {{-- Alert Notifikasi Sukses (Warna Hijau Solid) --}}
  @if (session('success'))
    <div id="success-alert"
      style="background-color: #10b981 !important; color: white !important; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 600; font-size: 14px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: none !important; opacity: 1 !important;">
      ✅ {{ session('success') }}
    </div>
    <script>
      setTimeout(() => {
        document.getElementById('success-alert').style.display = 'none';
      }, 3000);
    </script>
  @endif

  {{-- Alert Notifikasi Gagal/Error --}}
  @if (session('error'))
    <div id="error-alert" class="bg-red-500 text-white rounded-lg p-4 mb-6 font-semibold text-sm shadow">
      ❌ {{ session('error') }}
    </div>
    <script>
      setTimeout(() => {
        document.getElementById('error-alert').style.display = 'none';
      }, 3000);
    </script>
  @endif

  @if ($errors->any())
    <div class="bg-red-500 text-white rounded-lg p-4 mb-6 font-semibold text-sm shadow">
      <ul class="list-disc list-inside">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- 📊 GRID TABLE DATA USER (FULL WIDTH) --}}
  <div class="w-full bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border border-gray-100">
    <table class="w-full text-sm text-left">
      <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
        <tr>
          <th class="px-6 py-3.5">Nama Lengkap</th>
          <th class="px-6 py-3.5">Alamat Email</th>
          <th class="px-6 py-3.5 text-center">Hak Akses / Role</th>
          <th class="px-6 py-3.5 text-center">Aksi Manajemen</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @forelse($users as $u)
          <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 font-bold text-slate-800">{{ $u->name }}</td>
            <td class="px-6 py-4 text-slate-600">{{ $u->email }}</td>
            <td class="px-6 py-4 text-center">
              <span
                style="padding: 4px 12px; border-radius: 999px; font-size: 10px; font-weight: bold; color: white;
              background-color: {{ $u->role === 'admin' ? '#ef4444' : '#3b82f6' }};">
                {{ strtoupper($u->role) }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div style="display: flex; gap: 4px; justify-content: center; align-items: center;">
                <!-- Tombol Edit -->
                <button type="button"
                  onclick="openEditUserModal('{{ $u->id }}', '{{ $u->name }}', '{{ $u->email }}', '{{ $u->role }}')"
                  style="background-color: #f59e0b; color: white !important; padding: 5px 0; border-radius: 4px; font-size: 10px; font-weight: bold; border: none; cursor: pointer; width: 55px; text-align: center;">
                  EDIT
                </button>

                <!-- Tombol Hapus -->
                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST"
                  onsubmit="return confirm('Hapus staf ini?')" style="margin: 0; padding: 0;">
                  @csrf @method('DELETE')
                  <!-- Tombol Hapus (Pemicu Modal) -->
                  <button type="button" onclick="openDeleteUserModal('{{ $u->id }}', '{{ $u->name }}')"
                    style="background-color: #ef4444; color: white !important; padding: 5px 0; border-radius: 4px; font-size: 10px; font-weight: bold; border: none; cursor: pointer; width: 55px; text-align: center;">
                    HAPUS
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center p-6 text-gray-400 italic">Belum ada data staf terdaftar di database.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- ➕ POP UP MODAL 1: TAMBAH USER BARU --}}
  <div id="createUserModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <!-- Perubahan di baris bawah ini: w-96 diganti w-full max-w-xl -->
    <div class="bg-white p-6 rounded-xl w-full max-w-xl shadow-xl mx-4">
      <div class="flex justify-between items-center mb-4">
        <h5 class="font-bold text-slate-900 flex items-center gap-2">
          <i class="fa-solid fa-user-plus text-primary"></i> Tambah Staf Baru
        </h5>
        <button type="button" onclick="document.getElementById('createUserModal').style.display='none'"
          class="text-gray-400 hover:text-gray-600 font-bold">✕</button>
      </div>

      <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-3.5">
        @csrf
        <div>
          <label class="block text-slate-500 text-xs font-bold mb-1">Nama Lengkap</label>
          <input type="text" name="name" class="w-full text-sm border border-gray-200 rounded px-3 py-2"
            placeholder="Nama pekerja..." required>
        </div>
        <div>
          <label class="block text-slate-500 text-xs font-bold mb-1">Email</label>
          <input type="email" name="email" class="w-full text-sm border border-gray-200 rounded px-3 py-2"
            placeholder="nama@domain.com" required>
        </div>
        <div>
          <label class="block text-slate-500 text-xs font-bold mb-1">Password</label>
          <input type="password" name="password" class="w-full text-sm border border-gray-200 rounded px-3 py-2"
            placeholder="Minimal 8 karakter" required>
        </div>
        <div>
          <label class="block text-slate-500 text-xs font-bold mb-1">Konfirmasi Password</label>
          <input type="password" name="password_confirmation"
            class="w-full text-sm border border-gray-200 rounded px-3 py-2" placeholder="Ulangi password" required>
        </div>
        <div>
          <label class="block text-slate-500 text-xs font-bold mb-1">Posisi / Akses Role</label>
          <select name="role" class="w-full text-sm border border-gray-200 rounded px-3 py-2 bg-white" required>
            <option value="fulltimer">Fulltimer (Editor)</option>
            <option value="admin">Admin (Akses Penuh)</option>
          </select>
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="document.getElementById('createUserModal').style.display='none'"
            class="px-4 py-2 bg-gray-100 text-sm font-semibold rounded-lg text-gray-600">Batal</button>
          <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-bold rounded-lg shadow">Daftarkan
            Staf</button>
        </div>
      </form>
    </div>
  </div>

  {{-- 🔄 POP UP MODAL 2: EDIT USER EXIST --}}
  <div id="editUserModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <!-- Perubahan di baris bawah ini: w-96 diganti w-full max-w-xl -->
    <div class="bg-white p-6 rounded-xl w-full max-w-xl shadow-xl mx-4">
      <div class="flex justify-between items-center mb-4">
        <h5 class="font-bold text-slate-900 flex items-center gap-2">
          <i class="fa-solid fa-user-pen text-amber-500"></i> Edit Data Staf
        </h5>
        <button type="button" onclick="document.getElementById('editUserModal').style.display='none'"
          class="text-gray-400 hover:text-gray-600 font-bold">✕</button>
      </div>

      <form id="editUserForm" method="POST" class="space-y-3.5">
        @csrf @method('PUT')
        <div>
          <label class="block text-xs font-bold text-gray-500 mb-1">Nama Lengkap</label>
          <input type="text" name="name" id="edit_user_name"
            class="w-full border border-gray-200 p-2 text-sm rounded" required>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-500 mb-1">Email</label>
          <input type="email" name="email" id="edit_user_email"
            class="w-full border border-gray-200 p-2 text-sm rounded" required>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-500 mb-1">Ganti Password (Kosongkan jika tetap)</label>
          <input type="password" name="password" class="w-full border border-gray-200 p-2 text-sm rounded"
            placeholder="Password baru...">
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-500 mb-1">Role / Akses</label>
          <select name="role" id="edit_user_role" class="w-full border border-gray-200 p-2 text-sm rounded bg-white"
            required>
            <option value="fulltimer">Fulltimer (Editor)</option>
            <option value="admin">Admin (Akses Penuh)</option>
          </select>
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="document.getElementById('editUserModal').style.display='none'"
            class="px-4 py-2 bg-gray-100 text-sm font-semibold rounded-lg text-gray-600">Batal</button>
          <button type="submit" class="px-4 py-2 bg-primary text-white text-sm rounded-lg font-bold shadow">Simpan
            Perubahan</button>
        </div>
      </form>
    </div>
  </div>
  {{-- 🗑️ POP UP MODAL 3: KONFIRMASI HAPUS USER --}}
  <div id="deleteUserModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-xl w-full max-w-md shadow-xl mx-4 text-center">
      <div class="text-red-500 mb-3">
        <i class="fa-solid fa-circle-exclamation" style="font-size: 48px;"></i>
      </div>
      <h5 class="font-bold text-slate-900 text-lg mb-2">Konfirmasi Hapus</h5>
      <p class="text-sm text-gray-500 mb-5">Apakah Anda yakin ingin menghapus staf <b id="delete_user_name"
          class="text-slate-800"></b> secara permanen? Tindakan ini tidak dapat dibatalkan.</p>

      <form id="deleteUserForm" method="POST"
        style="display: flex; justify-content: center; gap: 12px; margin-top: 20px;">
        @csrf @method('DELETE')

        <!-- Tombol Batal -->
        <button type="button" onclick="document.getElementById('deleteUserModal').style.display='none'"
          style="background-color: #f3f4f6 !important; color: #4b5563 !important; padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: bold; border: 1px solid #e5e7eb !important; cursor: pointer; min-width: 100px; text-align: center;">
          Batal
        </button>

        <!-- Tombol Hapus (Merah Tegas) -->
        <button type="submit"
          style="background-color: #ef4444 !important; color: white !important; padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: bold; border: none !important; cursor: pointer; min-width: 100px; text-align: center; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);">
          Hapus
        </button>
      </form>
    </div>
  </div>

  <script>
    function openEditUserModal(id, name, email, role) {
      const modal = document.getElementById('editUserModal');
      const form = document.getElementById('editUserForm');

      form.action = `/admin/users/${id}`;
      document.getElementById('edit_user_name').value = name;
      document.getElementById('edit_user_email').value = email;
      document.getElementById('edit_user_role').value = role;

      modal.style.display = 'flex';
    }
    // Fungsi pemicu Modal Hapus
    function openDeleteUserModal(id, name) {
      const modal = document.getElementById('deleteUserModal');
      const form = document.getElementById('deleteUserForm');

      // Set action form secara dinamis mengarah ke route destroy Laravel
      form.action = `/admin/users/${id}`;
      // Tampilkan nama user di dalam teks konfirmasi agar admin tidak salah hapus
      document.getElementById('delete_user_name').innerText = name;

      modal.style.display = 'flex';
    }
  </script>

@endsection
