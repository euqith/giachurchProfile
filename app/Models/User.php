<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 🛡️ SUNTIKKAN 'role' KE DALAM ATTRIBUTE FILLABLE DI SINI ✅
#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke data Role jika ada
     */
    public function roleModel(): ?Role
    {
        try {
            return Role::where('name', $this->role)->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Dapatkan nama tampilan role
     */
    public function getRoleDisplayName(): string
    {
        try {
            $roleObj = $this->roleModel();

            return $roleObj ? $roleObj->display_name : ucfirst((string) ($this->role ?? 'User'));
        } catch (\Throwable $e) {
            return ucfirst((string) ($this->role ?? 'User'));
        }
    }

    /**
     * Cek apakah pengguna memiliki hak akses ke modul tertentu
     */
    public function canAccess(string $permissionKey): bool
    {
        $roleName = strtolower(trim((string) ($this->role ?? '')));

        // Role 'admin' selalu memiliki hak akses penuh
        if ($roleName === 'admin') {
            return true;
        }

        try {
            $roleObj = $this->roleModel();
            if (! $roleObj) {
                // Default fallback jika role belum terkonfigurasi di tabel
                return in_array($roleName, ['fulltimer', 'pendeta'], true);
            }

            return $roleObj->hasPermission($permissionKey);
        } catch (\Throwable $e) {
            return true;
        }
    }

    /**
     * Cek apakah pengguna dapat mengakses minimal salah satu modul dalam kelompok
     *
     * @param  array<string>  $permissionKeys
     */
    public function canAccessGroup(array $permissionKeys): bool
    {
        if (strtolower(trim((string) ($this->role ?? ''))) === 'admin') {
            return true;
        }

        try {
            foreach ($permissionKeys as $key) {
                if ($this->canAccess($key)) {
                    return true;
                }
            }

            return false;
        } catch (\Throwable $e) {
            return true;
        }
    }
}
