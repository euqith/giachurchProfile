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
     * Helper untuk extract clean YouTube embed ID or full embed URL
     */
    public function getYoutubeEmbedUrlAttribute(): string
    {
        $idOrUrl = trim($this->youtube_id ?? '');
        if (empty($idOrUrl)) {
            return '';
        }

        // Jika URL penuh
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $idOrUrl, $match)) {
            return 'https://www.youtube.com/embed/'.$match[1];
        }

        // Jika sudah 11 karakter ID
        if (strlen($idOrUrl) === 11) {
            return 'https://www.youtube.com/embed/'.$idOrUrl;
        }

        return 'https://www.youtube.com/embed/'.$idOrUrl;
    }
}
