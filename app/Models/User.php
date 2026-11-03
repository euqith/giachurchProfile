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
        return Role::where('name', $this->role)->first();
    }

    /**
     * Dapatkan nama tampilan role
     */
    public function getRoleDisplayName(): string
    {
        $roleObj = $this->roleModel();

        return $roleObj ? $roleObj->display_name : ucfirst($this->role ?? 'User');
    }

    /**
     * Cek apakah pengguna memiliki hak akses ke modul tertentu
     */
    public function canAccess(string $permissionKey): bool
    {
        // Role 'admin' selalu memiliki hak akses penuh
        if ($this->role === 'admin') {
            return true;
        }

        $roleObj = $this->roleModel();
        if (! $roleObj) {
            // Default fallback
            return $this->role === 'fulltimer';
        }

        return $roleObj->hasPermission($permissionKey);
    }

    /**
     * Cek apakah pengguna dapat mengakses minimal salah satu modul dalam kelompok
     *
     * @param  array<string>  $permissionKeys
     */
    public function canAccessGroup(array $permissionKeys): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        foreach ($permissionKeys as $key) {
            if ($this->canAccess($key)) {
                return true;
            }
        }

        return false;
    }
}
