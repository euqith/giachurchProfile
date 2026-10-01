<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSession extends Model
{
    protected $fillable = [
        'tanggal',
        'jenis_ibadah_id',
        'nama_ibadah',
        'nama_sesi',
        'cabang_id',
        'nama_cabang',
        'nama_petugas',
        'total_anggota',
        'total_tamu',
        'total_hadir',
        'isDelete',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(AttendanceDetail::class, 'attendance_session_id');
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function jenisIbadah(): BelongsTo
    {
        return $this->belongsTo(JenisIbadah::class, 'jenis_ibadah_id');
    }
}