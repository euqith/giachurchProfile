<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisIbadah extends Model
{
    use HasFactory;

    protected $table = 'jenis_ibadahs';

    protected $fillable = [
        'nama_ibadah',
        'deskripsi',
        'createdBy',
        'updatedBy',
        'isActive',
        'isDelete',
    ];

    public function scopeActive($query)
    {
        return $query->where('isDelete', 0);
    }
}