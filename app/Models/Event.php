<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events'; 

    protected $fillable = [
        'cabang_id',
        'nama_acara',
        'deskripsi_acara',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi_spesifik',
        'gambar_banner',
        'createdBy',
        'updatedBy',
        'isActive',
        'isDelete'
    ];

    /**
     * ⛪ BARIS BARU: Hubungan balik ke model Cabang
     */
    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }
}