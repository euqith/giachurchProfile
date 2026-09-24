<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabang extends Model
{
    use HasFactory;

    protected $table = 'cabangs'; 

    protected $fillable = [
        'nama_cabang',
        'lokasi',
        'createdBy',
        'updatedBy',
        'isActive',
        'isDelete',
    ];

    /**
     * Relasi satu cabang mempunyai banyak event
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'cabang_id');
    }
}