<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminRoleController extends Controller
{
    /**
     * Tampilkan halaman utama Role Setting
     */
    public function index(Request $request)
    {
        Role::ensureDefaultRoles();

        $roles = Role::withCount('users')->orderBy('id', 'asc')->get();

        $activeRoleId = $request->query('role_id');
        $activeRole = $activeRoleId ? $roles->firstWhere('id', $activeRoleId) : $roles->first();

        if (! $activeRole && $roles->isNotEmpty()) {
            $activeRole = $roles->first();
        }

        $permissionGroups = Role::getPermissionGroups();

        return view('admin.rolesCMS', compact('roles', 'activeRole', 'permissionGroups'));
    }

    /**
     * Simpan Role Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'display_name' => ['required', 'string', 'max:100'],
            'name' => ['nullable', 'string', 'max:50', 'alpha_dash', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
        ]);

        $slug = $request->filled('name')
            ? Str::slug($request->name, '_')
            : Str::slug($request->display_name, '_');

        // Pastikan slug unik
        $baseSlug = $slug;
        $counter = 1;
        while (Role::where('name', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $role = Role::create([
            'name' => $slug,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'permissions' => $request->input('permissions', []),
            'is_system' => false,
        ]);

        return redirect()->route('admin.roles.index', ['role_id' => $role->id])
            ->with('success', "Role baru '{$role->display_name}' berhasil ditambahkan!");
    }

    /**
     * Update Hak Akses & Detail Role
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'display_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
        ]);

        $permissions = $request->input('permissions', []);

        // Proteksi: Role admin sistem selalu memiliki semua hak akses
        if ($role->is_system && $role->name === 'admin') {
            $permissions = Role::getAllPermissionKeys();
        }

        $role->update([
            'display_name' => $request->display_name,
            'description' => $request->description,
            'permissions' => $permissions,
        ]);

        return redirect()->route('admin.roles.index', ['role_id' => $role->id])
            ->with('success', "Hak akses untuk role '{$role->display_name}' berhasil diperbarui!");
    }

    /**
     * Hapus Custom Role
     */
    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Role sistem bawaan tidak dapat dihapus demi keamanan sistem!');
        }

        $userCount = $role->users()->count();
        if ($userCount > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', "Role '{$role->display_name}' tidak dapat dihapus karena masih digunakan oleh {$userCount} staf/pengguna aktif!");
        }

        $name = $role->display_name;
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$name}' berhasil dihapus!");
    }
}
