<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stream extends Model
{
    use HasFactory;

    protected $table = 'streams';

    protected $fillable = [
        'cabang_id',
        'judul_streaming',
        'youtube_id',
        'channel_id',
        'waktu_tayang',
        'is_live',
        'auto_detect',
        'last_checked_at',
        'createdBy',
        'updatedBy',
        'isActive',
        'isDelete',
    ];

    protected $casts = [
        'is_live' => 'boolean',
        'auto_detect' => 'boolean',
        'last_checked_at' => 'datetime',
        'isActive' => 'boolean',
        'isDelete' => 'boolean',
    ];

    /**
     * Relasi ke Cabang Gereja
     */
    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    /**
     * Ekstrak 11-karakter YouTube Video ID dari berbagai format input (URL watch, live, youtu.be, embed, shorts, atau raw ID).
     */
    public static function extractVideoId(?string $input): ?string
    {
        if (empty($input)) {
            return null;
        }

        $input = trim($input);

        // Jika sudah 11 karakter ID murni
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) {
            return $input;
        }

        // Tangkap video ID dari format URL:
        // - https://www.youtube.com/watch?v=xxxx
        // - https://www.youtube.com/live/xxxx?si=...
        // - https://youtu.be/xxxx?si=...
        // - https://www.youtube.com/embed/xxxx
        // - https://www.youtube.com/shorts/xxxx
        // - https://m.youtube.com/watch?v=xxxx / live/xxxx
        $pattern = '/(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:embed\/|v\/|live\/|shorts\/|(?:.*[?&])?v=))([a-zA-Z0-9_-]{11})/i';
        if (preg_match($pattern, $input, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Mutator agar youtube_id selalu tersimpan bersih sebagai 11-karakter Video ID.
     */
    public function setYoutubeIdAttribute(?string $value): void
    {
        $clean = self::extractVideoId($value);
        $this->attributes['youtube_id'] = $clean ?: trim((string) $value);
    }

    /**
     * Helper untuk mendapatkan embed URL YouTube yang valid dan bebas bug concatenation.
     */
    public function getYoutubeEmbedUrlAttribute(): string
    {
        $id = self::extractVideoId($this->youtube_id);

        if (! $id && ! empty($this->channel_id)) {
            $id = self::extractVideoId($this->channel_id);
        }

        if ($id) {
            return 'https://www.youtube.com/embed/'.$id;
        }

        return '';
    }

    /**
     * Helper untuk mendapatkan ID YouTube bersih.
     */
    public function getCleanYoutubeIdAttribute(): string
    {
        return self::extractVideoId($this->youtube_id)
            ?? self::extractVideoId($this->channel_id)
            ?? '';
    }

    /**
     * Alias accessor judul_stream -> judul_streaming.
     */
    public function getJudulStreamAttribute(): string
    {
        return $this->judul_streaming ?: 'Ibadah Raya Minggu';
    }

    /**
     * Alias accessor jadwal_tayang -> waktu_tayang.
     */
    public function getJadwalTayangAttribute(): string
    {
        return $this->waktu_tayang ?: 'Setiap Minggu';
    }

    /**
     * Helper untuk mendapatkan link tontonan YouTube langsung.
     */
    public function getYoutubeUrlAttribute(): string
    {
        $id = $this->clean_youtube_id;
        if ($id) {
            return 'https://www.youtube.com/watch?v='.$id;
        }

        if (! empty($this->channel_id)) {
            if (str_starts_with($this->channel_id, 'http')) {
                return $this->channel_id;
            }

            return 'https://www.youtube.com/'.$this->channel_id;
        }

        return 'https://www.youtube.com';
    }
}
