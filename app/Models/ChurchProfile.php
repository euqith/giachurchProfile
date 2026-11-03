<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChurchProfile extends Model
{
    use HasFactory;

    protected $table = 'church_profiles';

    protected $fillable = [
        'nama_gereja',
        'tagline',
        'foto_gereja',
        'tentang_gereja',
        'sejarah_singkat',
        'visi',
        'misi',
        'alamat_pusat',
        'telepon',
        'email',
        'youtube_channel_url',
        'instagram_url',
    ];

    /**
     * Singleton helper to get active church profile record
     */
    public static function getProfile(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'nama_gereja' => 'Gereja Isa Almasih Surabaya',
                'tagline' => 'Bertumbuh dalam Kristus, Berakar dalam Firman, Berbuah dalam Kasih',
                'alamat_pusat' => 'Jl. Raya Darmo Harapan No.PF-1, Surabaya',
                'telepon' => '+62 31 7317208',
                'email' => 'sekretariat@giasurabaya.org',
            ]
        );
    }
}
