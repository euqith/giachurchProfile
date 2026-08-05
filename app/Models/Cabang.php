<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabang extends Model
{
    use HasFactory;

    // Pastikan nama tabelnya sesuai dengan database kamu (biasanya 'cabangs' atau 'cabang')
    protected $table = 'cabangs'; 

    protected $fillable = [
        'nama_cabang',
        'lokasi_cabang',
        // tambahkan kolom lain milik tabel cabangmu di sini jika ada
    ];

    /**
     * Relasi satu cabang mempunyai banyak event
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'cabang_id');
    }
}