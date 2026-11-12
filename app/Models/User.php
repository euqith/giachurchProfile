<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'avatar', 'google_id', 'telepon', 'bio', 'password', 'role'])]
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
     * Relasi ke artikel yang ditulis oleh user ini
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    /**
     * Relasi ke likes yang diberikan user
     */
    public function likes(): HasMany
    {
        return $this->hasMany(ArticleLike::class, 'user_id');
    }

    /**
     * Relasi ke komentar yang ditulis user
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ArticleComment::class, 'user_id');
    }

    /**
     * Cek apakah user adalah jemaat
     */
    public function isJemaat(): bool
    {
        return strtolower(trim((string) ($this->role ?? ''))) === 'jemaat';
    }

    /**
     * Cek apakah user sudah me-like artikel tertentu
     */
    public function hasLikedArticle(int $articleId): bool
    {
        return $this->likes()->where('article_id', $articleId)->exists();
    }

    /**
     * Accessor Avatar URL
     */
    public function getAvatarUrlAttribute(): string
    {
        if (! empty($this->avatar)) {
            if (str_starts_with($this->avatar, 'http')) {
                return $this->avatar;
            }

            return asset('storage/avatars/'.$this->avatar);
        }

        $name = urlencode($this->name ?: $this->username ?: 'Jemaat');

        return "https://ui-avatars.com/api/?name={$name}&background=8C5E3C&color=fff&rounded=true&bold=true";
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
