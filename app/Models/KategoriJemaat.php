<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriJemaat extends Model
{
    use HasFactory;

    protected $table = 'kategori_jemaats';

    protected $fillable = [
        'nama_kategori',
        'min_usia',
        'max_usia',
        'rentang_label',
        'warna_badge',
        'urutan',
        'isActive',
        'isDelete',
    ];

    protected $casts = [
        'min_usia' => 'integer',
        'max_usia' => 'integer',
        'urutan' => 'integer',
        'isActive' => 'boolean',
        'isDelete' => 'boolean',
    ];
}
