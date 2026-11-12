<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $table = 'articles';

    protected $fillable = [
        'judul',
        'slug',
        'ringkasan',
        'konten',
        'gambar_banner',
        'kategori',
        'status',
        'views_count',
        'published_at',
        'author_id',
        'author_name',
        'isActive',
        'isDelete',
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'isDelete' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer',
    ];

    /**
     * Penulis artikel
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Like pada artikel
     */
    public function likes(): HasMany
    {
        return $this->hasMany(ArticleLike::class, 'article_id');
    }

    /**
     * Komentar aktif pada artikel
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ArticleComment::class, 'article_id')
            ->where('isActive', true)
            ->where('isDelete', false)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Cek apakah artikel ini di-like oleh user tertentu
     */
    public function isLikedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Accessor URL Banner
     */
    public function getBannerUrlAttribute(): string
    {
        if (! empty($this->gambar_banner)) {
            if (str_starts_with($this->gambar_banner, 'http')) {
                return $this->gambar_banner;
            }

            return asset('img/articles/'.$this->gambar_banner);
        }

        return asset('homepage/assets/images/banner/banner-image1.jpg');
    }

    /**
     * Estimasi waktu baca
     */
    public function getReadingTimeAttribute(): string
    {
        $words = str_word_count(strip_tags($this->konten ?? ''));
        $minutes = max(1, (int) ceil($words / 200));

        return $minutes.' menit baca';
    }

    /**
     * Format tanggal publikasi
     */
    public function getFormattedDateAttribute(): string
    {
        $date = $this->published_at ?: $this->created_at ?: Carbon::now();

        return $date->translatedFormat('d M Y');
    }

    /**
     * Generate unique slug
     */
    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        if (empty($baseSlug)) {
            $baseSlug = 'artikel-'.time();
        }

        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
