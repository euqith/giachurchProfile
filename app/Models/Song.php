<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Song extends Model
{
    use HasFactory;

    protected $table = 'songs';

    protected $fillable = [
        'judul_lagu',
        'artis_sumber',
        'nada_dasar',
        'bagian_terakhir',
        'youtube_url',
        'spotify_url',
        'chord_url',
        'lirik_url',
        'lirik_teks',
        'createdBy',
        'updatedBy',
        'isActive',
        'isDelete',
    ];

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(WorshipSchedule::class, 'worship_schedule_songs')
            ->withPivot(['bagian_liturgi', 'nada_dasar', 'urutan'])
            ->withTimestamps();
    }

    public function scheduleSongs(): HasMany
    {
        return $this->hasMany(WorshipScheduleSong::class, 'song_id');
    }
}
