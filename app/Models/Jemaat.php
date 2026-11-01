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
        'kategori',
        'fokus_ibadah',
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
        'fokus_ibadah' => 'array',
    ];

    protected $appends = [
        'kategori_usia',
        'usia',
    ];

    /**
     * Hitung usia berdasarkan tanggal lahir
     */
    public function getUsiaAttribute(): ?int
    {
        return $this->tgl_lahir ? $this->tgl_lahir->age : null;
    }

    /**
     * Ambil Kategori Usia Jemaat (Anak, Remaja, Pemuda, Dewasa, Lansia, atau Tamu)
     */
    public function getKategoriUsiaAttribute(): string
    {
        if (strtolower($this->status ?? '') === 'tamu' || strtolower($this->kategori ?? '') === 'tamu') {
            return 'Tamu';
        }

        if ($this->kategori && in_array($this->kategori, ['Anak', 'Remaja', 'Pemuda', 'Dewasa', 'Lansia'])) {
            return $this->kategori;
        }

        if ($this->tgl_lahir) {
            $age = $this->tgl_lahir->age;
            if ($age <= 12) {
                return 'Anak';
            }
            if ($age <= 20) {
                return 'Remaja';
            }
            if ($age <= 30) {
                return 'Pemuda';
            }
            if ($age <= 59) {
                return 'Dewasa';
            }

            return 'Lansia';
        }

        return $this->kategori ?: 'Dewasa';
    }

    /**
     * Relasi ke Master Data Cabang (Wilayah Ibadah)
     */
    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    /**
     * Helper Method: Cek apakah jemaat perlu kunjungan
     * (Tidak pernah hadir 3 minggu / 21 hari berturut-turut di salah satu fokus ibadahnya)
     */
    public function perluKunjungan(): bool
    {
        if (empty($this->fokus_ibadah) || ! is_array($this->fokus_ibadah)) {
            return false;
        }

        // Contoh integrasi ke tabel presensi/absensi Anda:
        /*
        foreach ($this->fokus_ibadah as $jenisIbadah) {
            $hadir = \App\Models\AbsensiJemaat::where('jemaat_id', $this->id)
                ->where('kategori_ibadah', $jenisIbadah)
                ->where('created_at', '>=', now()->subDays(21))
                ->exists();

            if (!$hadir) {
                return true; // Perlu kunjungan karena absen 3 minggu di fokus ibadah ini
            }
        }
        */

        return false;
    }
}
