<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorshipScheduleSong extends Model
{
    use HasFactory;

    protected $table = 'worship_schedule_songs';

    protected $fillable = [
        'worship_schedule_id',
        'song_id',
        'bagian_liturgi',
        'nada_dasar',
        'urutan',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(WorshipSchedule::class, 'worship_schedule_id');
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class, 'song_id');
    }
}
