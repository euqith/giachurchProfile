<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WartaSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'warta_id',
        'file_gambar',
        'urutan_halaman',
        'createdBy',
        'updatedBy',
        'isActive',
        'isDelete'
    ];

    public function warta()
    {
        return $this->belongsTo(Warta::class, 'warta_id');
    }
}