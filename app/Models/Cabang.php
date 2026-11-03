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
        'maps_url',
        'maps_embed',
        'jadwal_ibadah',
        'createdBy',
        'updatedBy',
        'isActive',
        'isDelete',
    ];

    /**
     * URL navigasi Google Maps untuk jemaat
     */
    public function getGoogleMapsLinkAttribute(): string
    {
        if (! empty($this->maps_url)) {
            return $this->maps_url;
        }

        $query = 'Gereja Isa Almasih '.$this->nama_cabang.' '.($this->lokasi ?? 'Surabaya');

        return 'https://www.google.com/maps/search/?api=1&query='.urlencode($query);
    }

    /**
     * URL embed Google Maps untuk iframe peta interaktif
     */
    public function getGoogleMapsEmbedUrlAttribute(): string
    {
        if (! empty($this->maps_embed)) {
            // Jika admin memasukkan tag <iframe src="...">
            if (preg_match('/src="([^"]+)"/', $this->maps_embed, $matches)) {
                return $matches[1];
            }
            // Jika admin memasukkan URL langsung
            if (filter_var($this->maps_embed, FILTER_VALIDATE_URL)) {
                return $this->maps_embed;
            }
        }

        // Fallback embed query peta otomatis berdasarkan nama dan lokasi cabang
        $query = 'Gereja Isa Almasih '.$this->nama_cabang.' '.($this->lokasi ?? 'Surabaya');

        return 'https://maps.google.com/maps?q='.urlencode($query).'&t=&z=16&ie=UTF8&iwloc=&output=embed';
    }

    /**
     * Mengambil daftar baris jadwal ibadah
     */
    public function getJadwalLinesAttribute(): array
    {
        if (! empty($this->jadwal_ibadah)) {
            return array_filter(array_map('trim', explode("\n", str_replace("\r", '', $this->jadwal_ibadah))));
        }

        return [
            'Ibadah Raya 1: Minggu 06.00 WIB',
            'Ibadah Raya 2: Minggu 08.00 WIB',
            'Ibadah Raya 3: Minggu 10.00 WIB',
            'Doa Tengah Minggu: Rabu 18.30 WIB',
        ];
    }

    /**
     * Relasi satu cabang mempunyai banyak event
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'cabang_id');
    }
}
