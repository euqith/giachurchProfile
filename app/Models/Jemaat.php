<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jemaat extends Model
{
    use HasFactory;

    protected $table = 'jemaats';

    protected $fillable = [
        'nama_asli',
        'status',
        'badge_tag',
        'alias_1',
        'alias_2',
        'keluarga',
        'cabang_id',
        'alamat',
        'tempat_lahir',
        'tgl_lahir',
        'telepon',
        'createdBy',
        'updatedBy',
        'isActive',
        'isDelete',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    /**
     * Relasi ke Master Data Cabang (Wilayah Ibadah)
     */
    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }
}