<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorshipSchedule extends Model
{
    use HasFactory;

    protected $table = 'worship_schedules';

    protected $fillable = [
        'tanggal',
        'jenis_ibadah_id',
        'nama_ibadah',
        'sesi_id',
        'nama_sesi',
        'cabang_id',
        'nama_cabang',
        'tema',
        'ayat',
        'bahan_khotbah',
        'pengkhotbah',
        'pemimpin_pujian',
        'catatan_singkat',
        'petugas_tambahan',
        'bahan_media',
        'createdBy',
        'updatedBy',
        'isActive',
        'isDelete',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'petugas_tambahan' => 'array',
        'bahan_media' => 'array',
        'isActive' => 'boolean',
        'isDelete' => 'boolean',
    ];

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function jenisIbadah(): BelongsTo
    {
        return $this->belongsTo(JenisIbadah::class, 'jenis_ibadah_id');
    }

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(Sesi::class, 'sesi_id');
    }

    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'worship_schedule_songs')
            ->withPivot(['id', 'bagian_liturgi', 'nada_dasar', 'urutan'])
            ->orderByPivot('urutan', 'asc')
            ->withTimestamps();
    }

    public function scheduleSongs(): HasMany
    {
        return $this->hasMany(WorshipScheduleSong::class, 'worship_schedule_id')->orderBy('urutan', 'asc');
    }
}
