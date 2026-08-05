<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warta extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul_edisi',
        'tanggal_rilis',
        'createdBy',
        'updatedBy',
        'isActive',
        'isDelete'
    ];

    /**
     * Relasi ke Slide Gambar Warta (One to Many)
     */
    public function slides()
    {
        return $this->hasMany(WartaSlide::class, 'warta_id')
                    ->where('isDelete', false)
                    ->where('isActive', true)
                    ->orderBy('urutan_halaman', 'asc');
    }
}