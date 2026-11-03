<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminUserController extends Controller
{
    // Tampilkan Daftar Staf Admin & Pengguna
    public function index()
    {
        Role::ensureDefaultRoles();

        $users = User::orderBy('name', 'asc')->get();
        $roles = Role::orderBy('id', 'asc')->get();

        return view('admin.usersCMS', compact('users', 'roles'));
    }

    // Proses Simpan Staf Baru ke Database
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Staf baru berhasil didaftarkan!');
    }

    // 🔄 FUNGSI BARU: Proses Update Data Staf via Modal JS
    public function update(Request $request, $user)
    {
        $user = User::findOrFail($user);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', Rules\Password::defaults()], // Password opsional saat edit
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')->with('success', 'Data staf berhasil diperbarui!');
    }

    // 🗑️ FUNGSI BARU: Hapus Staf Secara Permanen
    public function destroy($user)
    {
        $user = User::findOrFail($user);

        // Mencegah admin bunuh diri (menghapus akun sendiri secara tidak sengaja)
        if (auth()->id() == $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak bisa menghapus akun Anda sendiri yang sedang aktif!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun staf berhasil dihapus secara permanen!');
    }
}
