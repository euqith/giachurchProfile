@extends('admin.layoutCMS')

@section('content')
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-800 text-xs font-semibold mb-1.5 border border-blue-200/80">
        <i class="ri-shield-keyhole-line"></i> Master Data & Hak Akses
      </div>
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Role Setting & Manajemen Hak Akses</h1>
      <p class="text-xs text-slate-500 mt-0.5">Atur menu, sub-menu, dan halaman yang dapat diakses oleh masing-masing tingkatan staf gereja.</p>
    </div>

    {{-- Tombol Utama Tambah Role --}}
    <button type="button" onclick="document.getElementById('createRoleModal').style.display='flex'"
      class="btn-cms-primary">
      <i class="ri-add-line text-base"></i> Tambah Role Baru
    </button>
  </div>

  {{-- Alert Notifikasi Sukses --}}
  @if (session('success'))
    <div id="success-alert"
      class="p-4 mb-6 rounded-xl bg-emerald-500 text-white font-semibold text-sm shadow-md flex items-center justify-between">
      <div class="flex items-center gap-2">
        <i class="ri-checkbox-circle-fill text-lg"></i>
        <span>{{ session('success') }}</span>
      </div>
      <button type="button" onclick="document.getElementById('success-alert').remove()" class="text-white/80 hover:text-white font-bold">✕</button>
    </div>
    <script>
      setTimeout(() => {
        const el = document.getElementById('success-alert');
        if (el) el.style.display = 'none';
      }, 4000);
    </script>
  @endif

  {{-- Alert Notifikasi Gagal/Error --}}
  @if (session('error'))
    <div id="error-alert" class="p-4 mb-6 rounded-xl bg-rose-500 text-white font-semibold text-sm shadow-md flex items-center justify-between">
      <div class="flex items-center gap-2">
        <i class="ri-error-warning-fill text-lg"></i>
        <span>{{ session('error') }}</span>
      </div>
      <button type="button" onclick="document.getElementById('error-alert').remove()" class="text-white/80 hover:text-white font-bold">✕</button>
    </div>
  @endif

  @if ($errors->any())
    <div class="p-4 mb-6 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium">
      <div class="font-bold flex items-center gap-1.5 mb-1 text-rose-900">
        <i class="ri-error-warning-line"></i> Terjadi kesalahan input:
      </div>
      <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- 2-COLUMN MAIN INTERFACE --}}
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

    {{-- 👈 KOLOM KIRI: DAFTAR ROLE TERSEDIA (4 COLUMNS) --}}
    <div class="lg:col-span-4 space-y-4">
      <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
        <div class="flex items-center justify-between mb-3 px-1">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
            <i class="ri-shield-user-line text-sm"></i> Daftar Role Staf ({{ $roles->count() }})
          </h3>
          <span class="text-[11px] text-slate-400 font-medium">Klik untuk memilih</span>
        </div>

        <div class="space-y-2.5">
          @foreach ($roles as $role)
            @php
              $isActive = $activeRole && $activeRole->id === $role->id;
              $permCount = count($role->permissions ?? []);
              $allPermCount = count(\App\Models\Role::getAllPermissionKeys());
            @endphp
            <div class="group relative rounded-xl border transition-all duration-200 cursor-pointer {{ $isActive ? 'bg-blue-50/70 border-blue-500 ring-2 ring-blue-500/20 shadow-sm' : 'bg-white border-slate-200 hover:border-slate-300 hover:bg-slate-50/60' }}">
              <a href="{{ route('admin.roles.index', ['role_id' => $role->id]) }}" class="block p-3.5">
                <div class="flex items-start justify-between gap-2 mb-1.5">
                  <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs {{ $isActive ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 group-hover:bg-slate-200' }}">
                      {{ strtoupper(substr($role->display_name, 0, 2)) }}
                    </span>
                    <div>
                      <h4 class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors flex items-center gap-1.5">
                        {{ $role->display_name }}
                      </h4>
                      <span class="text-[11px] font-mono text-slate-400">role: {{ $role->name }}</span>
                    </div>
                  </div>

                  @if ($role->is_system)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-900 text-white">
                      System
                    </span>
                  @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-violet-100 text-violet-800 border border-violet-200">
                      Custom
                    </span>
                  @endif
                </div>

                @if ($role->description)
                  <p class="text-xs text-slate-500 line-clamp-2 mt-1 mb-2">{{ $role->description }}</p>
                @endif

                <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs text-slate-500">
                  <span class="inline-flex items-center gap-1 font-medium text-slate-600">
                    <i class="ri-user-line text-slate-400"></i> {{ $role->users_count }} Pengguna
                  </span>
                  <span class="inline-flex items-center gap-1 font-semibold {{ $permCount === $allPermCount ? 'text-emerald-600' : 'text-blue-600' }}">
                    <i class="ri-checkbox-circle-line"></i> {{ $permCount }}/{{ $allPermCount }} Halaman
                  </span>
                </div>
              </a>

              {{-- Tombol Hapus Custom Role jika tidak ada user --}}
              @if (! $role->is_system && $role->users_count === 0)
                <button type="button"
                  onclick="event.stopPropagation(); openDeleteRoleModal('{{ $role->id }}', '{{ $role->display_name }}')"
                  title="Hapus Role Ini"
                  class="absolute top-3 right-3 text-slate-300 hover:text-rose-600 p-1 rounded transition opacity-0 group-hover:opacity-100">
                  <i class="ri-delete-bin-line text-sm"></i>
                </button>
              @endif
            </div>
          @endforeach
        </div>
      </div>

      {{-- Petunjuk / Info Card --}}
      <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/80 text-amber-900 text-xs space-y-1.5">
        <div class="font-bold flex items-center gap-1.5 text-amber-950">
          <i class="ri-lightbulb-line text-amber-600"></i> Cara Kerja Hak Akses
        </div>
        <p class="text-amber-800/90 leading-relaxed">
          Menu dan sub-menu yang tidak dicentang akan otomatis <b>disembunyikan</b> dari navigasi samping pengguna dengan role tersebut, dan akses langsung via URL akan ditolak oleh sistem.
        </p>
      </div>
    </div>

    {{-- 👉 KOLOM KANAN: MATRIKS HAK AKSES ROLE AKTIF (8 COLUMNS) --}}
    <div class="lg:col-span-8">
      @if ($activeRole)
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">

          {{-- Header Detail Role Aktif --}}
          <div class="p-5 sm:p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50/80 to-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3">
              <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
                <i class="ri-shield-keyhole-line"></i>
              </div>
              <div>
                <div class="flex items-center gap-2 flex-wrap">
                  <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">{{ $activeRole->display_name }}</h2>
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                    slug: {{ $activeRole->name }}
                  </span>
                  @if ($activeRole->is_system)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-900 text-white">
                      Role Utama Sistem
                    </span>
                  @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                  {{ $activeRole->description ?: 'Atur izin akses halaman menu dan sub-menu untuk role ini di bawah.' }}
                </p>
              </div>
            </div>

            <div class="flex items-center gap-2 self-end sm:self-center">
              <button type="button" onclick="openEditRoleInfoModal('{{ $activeRole->id }}', '{{ $activeRole->display_name }}', '{{ addslashes($activeRole->description ?? '') }}')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 text-xs font-semibold shadow-2xs transition">
                <i class="ri-edit-line text-sm"></i> Edit Info Role
              </button>
            </div>
          </div>

          {{-- Pesan Khusus Role Admin --}}
          @if ($activeRole->is_system && $activeRole->name === 'admin')
            <div class="m-5 p-4 rounded-xl bg-blue-50/80 border border-blue-200/80 text-blue-950 text-xs flex items-center gap-3">
              <i class="ri-shield-check-fill text-blue-600 text-2xl shrink-0"></i>
              <div>
                <p class="font-bold text-blue-900">Hak Akses Administrator Utama Terkunci Permanen (Full Access)</p>
                <p class="text-blue-800/80 mt-0.5">
                  Untuk keamanan sistem, role Administrator selalu memiliki hak akses tak terbatas ke seluruh menu dan fitur agar sistem tidak terkunci dari luar.
                </p>
              </div>
            </div>
          @endif

          {{-- Formulir Update Hak Akses --}}
          <form id="rolePermissionsForm" action="{{ route('admin.roles.update', $activeRole->id) }}" method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" name="display_name" value="{{ $activeRole->display_name }}">
            <input type="hidden" name="description" value="{{ $activeRole->description }}">

            {{-- Toolbar Cepat: Centang Semua / Hapus Semua --}}
            @if (! ($activeRole->is_system && $activeRole->name === 'admin'))
              <div class="px-6 py-3 bg-slate-50/70 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2 font-medium text-slate-600">
                  <span id="selectedCounter" class="font-bold text-blue-600">0</span> dari <span>{{ count(\App\Models\Role::getAllPermissionKeys()) }}</span> modul diizinkan untuk role ini
                </div>
                <div class="flex items-center gap-2">
                  <button type="button" onclick="setAllCheckboxes(true)"
                    class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold transition text-xs shadow-2xs">
                    <i class="ri-checkbox-multiple-line text-blue-600"></i> Pilih Semua
                  </button>
                  <button type="button" onclick="setAllCheckboxes(false)"
                    class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold transition text-xs shadow-2xs">
                    <i class="ri-checkbox-blank-line text-slate-400"></i> Kosongkan
                  </button>
                </div>
              </div>
            @endif

            {{-- DAFTAR KELOMPOK HAK AKSES --}}
            <div class="p-5 sm:p-6 space-y-6">
              @foreach ($permissionGroups as $groupKey => $group)
                <div class="rounded-xl border border-slate-200/80 overflow-hidden bg-white shadow-2xs">
                  {{-- Header Kategori --}}
                  <div class="px-4 py-3 bg-slate-50/90 border-b border-slate-200/70 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <span class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-700 flex items-center justify-center text-sm shadow-2xs">
                        <i class="{{ $group['icon'] ?? 'ri-folder-line' }}"></i>
                      </span>
                      <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">{{ $group['title'] }}</h4>
                        <p class="text-[11px] text-slate-400">{{ $group['description'] }}</p>
                      </div>
                    </div>

                    @if (! ($activeRole->is_system && $activeRole->name === 'admin'))
                      <button type="button" onclick="toggleGroupCheckboxes('{{ $groupKey }}')"
                        class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 px-2 py-1 rounded hover:bg-blue-50 transition">
                        Toggle Kategori
                      </button>
                    @endif
                  </div>

                  {{-- Grid Items --}}
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-3.5">
                    @foreach ($group['items'] as $itemKey => $item)
                      @php
                        $isChecked = $activeRole->hasPermission($itemKey);
                        $isAdminLocked = $activeRole->is_system && $activeRole->name === 'admin';
                      @endphp
                      <label for="perm_{{ str_replace('.', '_', $itemKey) }}"
                        class="relative flex items-start gap-3 p-3 rounded-xl border transition-all select-none cursor-pointer {{ $isChecked ? 'bg-blue-50/40 border-blue-200/80 ring-1 ring-blue-500/20' : 'bg-white border-slate-200/70 hover:bg-slate-50' }}">
                        <div class="pt-0.5">
                          <input type="checkbox"
                            id="perm_{{ str_replace('.', '_', $itemKey) }}"
                            name="permissions[]"
                            value="{{ $itemKey }}"
                            {{ $isChecked ? 'checked' : '' }}
                            {{ $isAdminLocked ? 'disabled' : '' }}
                            onchange="updateSelectedCounter()"
                            class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300 perm-checkbox perm-group-{{ $groupKey }} cursor-pointer">
                          @if ($isAdminLocked)
                            <input type="hidden" name="permissions[]" value="{{ $itemKey }}">
                          @endif
                        </div>

                        <div class="flex-1 min-w-0">
                          <div class="flex items-center gap-1.5">
                            <i class="{{ $item['icon'] ?? 'ri-pages-line' }} text-slate-400 text-sm"></i>
                            <span class="text-xs font-bold text-slate-900 truncate">{{ $item['name'] }}</span>
                          </div>
                          <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2 leading-relaxed">{{ $item['description'] }}</p>
                          <div class="mt-1.5 flex items-center gap-1.5">
                            <span class="text-[10px] font-mono text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">
                              {{ $item['route'] }}
                            </span>
                          </div>
                        </div>
                      </label>
                    @endforeach
                  </div>
                </div>
              @endforeach
            </div>

            {{-- Sticky Save Bottom Bar --}}
            @if (! ($activeRole->is_system && $activeRole->name === 'admin'))
              <div class="sticky bottom-0 px-6 py-4 bg-white/95 backdrop-blur-md border-t border-slate-200 flex items-center justify-between gap-4 z-20 shadow-lg">
                <div class="text-xs text-slate-500 hidden sm:block">
                  Simpan perubahan hak akses untuk role <b>{{ $activeRole->display_name }}</b>.
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                  <a href="{{ route('admin.roles.index', ['role_id' => $activeRole->id]) }}"
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                    Batal
                  </a>
                  <button type="submit" class="btn-cms-primary">
                    <i class="ri-save-line text-sm"></i> Simpan Hak Akses Role
                  </button>
                </div>
              </div>
            @endif

          </form>

        </div>
      @endif
    </div>

  </div>

  {{-- ➕ MODAL 1: TAMBAH ROLE BARU --}}
  <div id="createRoleModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-2xl w-full max-w-lg shadow-2xl mx-4">
      <div class="flex justify-between items-center mb-4">
        <h4 class="font-bold text-slate-900 text-base flex items-center gap-2">
          <i class="ri-shield-keyhole-line text-blue-600 text-lg"></i> Tambah Role Staf Baru
        </h4>
        <button type="button" onclick="document.getElementById('createRoleModal').style.display='none'"
          class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
      </div>

      <form action="{{ route('admin.roles.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
          <label class="block text-slate-600 text-xs font-bold mb-1">Nama Tampilan Role (Display Name) <span class="text-rose-500">*</span></label>
          <input type="text" name="display_name" class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
            placeholder="Contoh: Operator Multimedia, Petugas Warta..." required>
        </div>

        <div>
          <label class="block text-slate-600 text-xs font-bold mb-1">Slug / Identifier Role (Opsional)</label>
          <input type="text" name="name" class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none font-mono text-xs"
            placeholder="Otomatis dibuat jika kosong (contoh: operator_multimedia)">
          <span class="text-[11px] text-slate-400 mt-0.5 block">Hanya gunakan huruf kecil, angka, garis bawah atau strip.</span>
        </div>

        <div>
          <label class="block text-slate-600 text-xs font-bold mb-1">Deskripsi Tugas / Kewenangan</label>
          <textarea name="description" rows="2" class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
            placeholder="Penjelasan singkat mengenai peran dan tanggung jawab role ini..."></textarea>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
          <button type="button" onclick="document.getElementById('createRoleModal').style.display='none'"
            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-xs font-semibold rounded-xl text-slate-700 transition">
            Batal
          </button>
          <button type="submit" class="btn-cms-primary text-xs">
            <i class="ri-check-line text-sm"></i> Simpan Role
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- ✏️ MODAL 2: EDIT INFO ROLE --}}
  <div id="editRoleInfoModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-2xl w-full max-w-lg shadow-2xl mx-4">
      <div class="flex justify-between items-center mb-4">
        <h4 class="font-bold text-slate-900 text-base flex items-center gap-2">
          <i class="ri-edit-line text-blue-600 text-lg"></i> Edit Informasi Role
        </h4>
        <button type="button" onclick="document.getElementById('editRoleInfoModal').style.display='none'"
          class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
      </div>

      <form id="editRoleInfoForm" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        {{-- Copy permissions saat ini agar tidak terhapus saat edit info --}}
        @if ($activeRole && $activeRole->permissions)
          @foreach ($activeRole->permissions as $p)
            <input type="hidden" name="permissions[]" value="{{ $p }}">
          @endforeach
        @endif

        <div>
          <label class="block text-slate-600 text-xs font-bold mb-1">Nama Tampilan Role <span class="text-rose-500">*</span></label>
          <input type="text" name="display_name" id="edit_role_display_name" class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none" required>
        </div>

        <div>
          <label class="block text-slate-600 text-xs font-bold mb-1">Deskripsi Role</label>
          <textarea name="description" id="edit_role_description" rows="3" class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"></textarea>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
          <button type="button" onclick="document.getElementById('editRoleInfoModal').style.display='none'"
            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-xs font-semibold rounded-xl text-slate-700 transition">
            Batal
          </button>
          <button type="submit" class="btn-cms-primary text-xs">
            <i class="ri-check-line text-sm"></i> Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- 🗑️ MODAL 3: KONFIRMASI HAPUS ROLE --}}
  <div id="deleteRoleModal"
    style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div class="bg-white p-6 rounded-2xl w-full max-w-md shadow-2xl mx-4 text-center">
      <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-3">
        <i class="ri-delete-bin-line"></i>
      </div>
      <h4 class="font-bold text-slate-900 text-base mb-1">Konfirmasi Hapus Role</h4>
      <p class="text-xs text-slate-500 mb-5">Apakah Anda yakin ingin menghapus role <b id="delete_role_name" class="text-slate-800"></b>? Role yang sudah dihapus tidak dapat dipulihkan kembali.</p>

      <form id="deleteRoleForm" method="POST" class="flex justify-center gap-3">
        @csrf
        @method('DELETE')
        <button type="button" onclick="document.getElementById('deleteRoleModal').style.display='none'"
          class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
          Batal
        </button>
        <button type="submit"
          class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition shadow-md">
          Ya, Hapus Role
        </button>
      </form>
    </div>
  </div>

  {{-- SCRIPT INTERAKTIF UI/UX --}}
  <script>
    function updateSelectedCounter() {
      const checkedBoxes = document.querySelectorAll('.perm-checkbox:checked');
      const counterEl = document.getElementById('selectedCounter');
      if (counterEl) {
        counterEl.innerText = checkedBoxes.length;
      }
    }

    function setAllCheckboxes(state) {
      document.querySelectorAll('.perm-checkbox').forEach(cb => {
        if (!cb.disabled) {
          cb.checked = state;
        }
      });
      updateSelectedCounter();
    }

    function toggleGroupCheckboxes(groupKey) {
      const boxes = document.querySelectorAll('.perm-group-' + groupKey);
      if (boxes.length === 0) return;
      const anyUnchecked = Array.from(boxes).some(cb => !cb.checked && !cb.disabled);
      boxes.forEach(cb => {
        if (!cb.disabled) {
          cb.checked = anyUnchecked;
        }
      });
      updateSelectedCounter();
    }

    function openEditRoleInfoModal(id, displayName, description) {
      document.getElementById('edit_role_display_name').value = displayName;
      document.getElementById('edit_role_description').value = description;
      document.getElementById('editRoleInfoForm').action = "{{ url('admin/roles') }}/" + id;
      document.getElementById('editRoleInfoModal').style.display = 'flex';
    }

    function openDeleteRoleModal(id, displayName) {
      document.getElementById('delete_role_name').innerText = displayName;
      document.getElementById('deleteRoleForm').action = "{{ url('admin/roles') }}/" + id;
      document.getElementById('deleteRoleModal').style.display = 'flex';
    }

    document.addEventListener('DOMContentLoaded', updateSelectedCounter);
  </script>
@endsection

