<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsCMSAccessible
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sudah login
        if (! Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        $roleName = strtolower(trim((string) ($user->role ?? '')));

        // 2. Admin selalu lolos langsung tanpa query tambahan
        if ($roleName === 'admin') {
            return $next($request);
        }

        // 3. Cek apakah role terdaftar sebagai pengelola CMS
        try {
            $validRoles = Role::pluck('name')->map(fn ($r) => strtolower(trim((string) $r)))->toArray();
        } catch (\Throwable $e) {
            $validRoles = [];
        }

        if (empty($validRoles)) {
            $validRoles = ['admin', 'fulltimer', 'pendeta'];
        }

        if (! in_array($roleName, $validRoles, true)) {
            return redirect('/')->with('error', 'Akses ditolak! Anda tidak memiliki izin untuk mengakses CMS.');
        }

        // 4. Cek permission spesifik untuk rute aktif
        $routeName = $request->route()?->getName();
        if ($routeName) {
            $permissionRequired = $this->resolvePermissionKey($routeName);
            if ($permissionRequired && ! $user->canAccess($permissionRequired)) {
                return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak! Role Anda ('.$user->getRoleDisplayName().') tidak memiliki izin ke halaman tersebut.');
            }
        }

        return $next($request);
    }

    /**
     * Map nama rute ke kunci hak akses modul
     */
    protected function resolvePermissionKey(string $routeName): ?string
    {
        if (str_starts_with($routeName, 'admin.event.')) {
            return 'event';
        }
        if (str_starts_with($routeName, 'admin.warta.')) {
            return 'warta';
        }
        if (str_starts_with($routeName, 'admin.stream.')) {
            return 'stream';
        }
        if (
            str_starts_with($routeName, 'admin.attendance.persiapan') ||
            str_starts_with($routeName, 'admin.attendance.createSchedule') ||
            str_starts_with($routeName, 'admin.attendance.storeSchedule') ||
            str_starts_with($routeName, 'admin.attendance.showSchedule') ||
            str_starts_with($routeName, 'admin.attendance.editSchedule') ||
            str_starts_with($routeName, 'admin.attendance.updateSchedule') ||
            str_starts_with($routeName, 'admin.attendance.destroySchedule') ||
            str_starts_with($routeName, 'admin.attendance.uploadMedia') ||
            str_starts_with($routeName, 'admin.attendance.storeSong') ||
            str_starts_with($routeName, 'admin.attendance.updateSong') ||
            str_starts_with($routeName, 'admin.attendance.destroySong')
        ) {
            return 'attendance.persiapan';
        }
        if (
            str_starts_with($routeName, 'admin.attendance.input') ||
            str_starts_with($routeName, 'admin.attendance.store') ||
            str_starts_with($routeName, 'admin.attendance.guest') ||
            str_starts_with($routeName, 'admin.attendance.get-draft') ||
            str_starts_with($routeName, 'admin.attendance.jemaatDetail')
        ) {
            return 'attendance.input';
        }
        if (
            str_starts_with($routeName, 'admin.attendance.history') ||
            str_starts_with($routeName, 'admin.attendance.show') ||
            str_starts_with($routeName, 'admin.attendance.update') ||
            str_starts_with($routeName, 'admin.attendance.destroy')
        ) {
            return 'attendance.history';
        }
        if (
            str_starts_with($routeName, 'admin.attendance.report') ||
            str_starts_with($routeName, 'admin.attendance.exportKunjungan')
        ) {
            return 'attendance.report';
        }
        if (str_starts_with($routeName, 'admin.church.')) {
            return 'church';
        }
        if (str_starts_with($routeName, 'admin.cabang.')) {
            return 'cabang';
        }
        if (str_starts_with($routeName, 'admin.ibadah.')) {
            return 'ibadah';
        }
        if (str_starts_with($routeName, 'admin.sesi.')) {
            return 'sesi';
        }
        if (str_starts_with($routeName, 'admin.jemaat.')) {
            return 'jemaat';
        }
        if (str_starts_with($routeName, 'admin.users.')) {
            return 'users';
        }
        if (str_starts_with($routeName, 'admin.roles.')) {
            return 'roles';
        }
        if ($routeName === 'admin.dashboard') {
            return 'dashboard';
        }

        return null;
    }
}
