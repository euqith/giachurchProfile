<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'is_system' => 'boolean',
        ];
    }

    /**
     * Relasi ke Pengguna yang memiliki role ini
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role', 'name');
    }

    /**
     * Cek apakah role memiliki hak akses tertentu
     */
    public function hasPermission(string $key): bool
    {
        if ($this->is_system && $this->name === 'admin') {
            return true;
        }

        $perms = $this->permissions ?? [];

        return in_array($key, $perms, true);
    }

    /**
     * Definisi seluruh modul, halaman, menu dan sub-menu yang dapat di-assign
     */
    public static function getPermissionGroups(): array
    {
        return [
            'navigation' => [
                'title' => 'Navigation',
                'icon' => 'ri-compass-3-line',
                'description' => 'Menu navigasi beranda internal panel CMS.',
                'items' => [
                    'dashboard' => [
                        'name' => 'Dashboard',
                        'description' => 'Melihat ringkasan metrik statistik, jemaat, dan aktivitas utama.',
                        'route' => 'admin.dashboard',
                        'icon' => 'ri-dashboard-line',
                    ],
                ],
            ],
            'apps' => [
                'title' => 'Apps & Publikasi',
                'icon' => 'ri-apps-2-line',
                'description' => 'Pengelolaan konten yang tampil pada situs web gereja.',
                'items' => [
                    'event' => [
                        'name' => 'Kelola Event',
                        'description' => 'Membuat, memperbarui, dan mempublikasikan agenda atau event.',
                        'route' => 'admin.event.*',
                        'icon' => 'ri-calendar-event-line',
                    ],
                    'warta' => [
                        'name' => 'Kelola Warta',
                        'description' => 'Mengunggah dan mengelola buletin warta jemaat digital mingguan.',
                        'route' => 'admin.warta.*',
                        'icon' => 'ri-article-line',
                    ],
                    'stream' => [
                        'name' => 'Kelola Streaming',
                        'description' => 'Mengatur siaran langsung YouTube Live ibadah tiap wilayah.',
                        'route' => 'admin.stream.*',
                        'icon' => 'ri-live-line',
                    ],
                ],
            ],
            'attendance' => [
                'title' => 'Modul Absensi',
                'icon' => 'ri-checkbox-circle-line',
                'description' => 'Rangkaian pencatatan kehadiran dan persiapan ibadah gereja.',
                'items' => [
                    'attendance.persiapan' => [
                        'name' => 'Persiapan Ibadah',
                        'description' => 'Menjadwalkan pelayan/petugas, liturgi, dan pustaka lagu.',
                        'route' => 'admin.attendance.persiapan*',
                        'icon' => 'ri-book-open-line',
                    ],
                    'attendance.input' => [
                        'name' => 'Input Kehadiran',
                        'description' => 'Melakukan pencatatan kehadiran jemaat saat sesi ibadah berlangsung.',
                        'route' => 'admin.attendance.input*',
                        'icon' => 'ri-clipboard-line',
                    ],
                    'attendance.history' => [
                        'name' => 'Riwayat Sesi',
                        'description' => 'Meninjau dan mengoreksi data sesi kehadiran ibadah yang telah lalu.',
                        'route' => 'admin.attendance.history*',
                        'icon' => 'ri-history-line',
                    ],
                    'attendance.report' => [
                        'name' => 'Laporan Absensi',
                        'description' => 'Melihat grafik statistik kehadiran dan rekapitulasi data kunjungan.',
                        'route' => 'admin.attendance.report*',
                        'icon' => 'ri-bar-chart-2-line',
                    ],
                ],
            ],
            'masterdata' => [
                'title' => 'Master Data',
                'icon' => 'ri-database-2-line',
                'description' => 'Pengaturan data induk, profil, jemaat, wilayah, dan akun.',
                'items' => [
                    'church' => [
                        'name' => 'Profil Gereja',
                        'description' => 'Mengatur banner beranda, sambutan, profil, dan kontak gereja.',
                        'route' => 'admin.church.*',
                        'icon' => 'ri-building-line',
                    ],
                    'cabang' => [
                        'name' => 'Kelola Wilayah',
                        'description' => 'Mengatur daftar cabang/wilayah gereja, embed peta, dan jadwal.',
                        'route' => 'admin.cabang.*',
                        'icon' => 'ri-map-pin-line',
                    ],
                    'ibadah' => [
                        'name' => 'Kelola Ibadah',
                        'description' => 'Menentukan kategori ibadah (Ibadah Raya, Doa, Pemuda, dll).',
                        'route' => 'admin.ibadah.*',
                        'icon' => 'ri-service-line',
                    ],
                    'sesi' => [
                        'name' => 'Kelola Sesi',
                        'description' => 'Menentukan jam sesi pelaksanaan ibadah tiap wilayah.',
                        'route' => 'admin.sesi.*',
                        'icon' => 'ri-time-line',
                    ],
                    'jemaat' => [
                        'name' => 'Kelola Jemaat',
                        'description' => 'Mengelola data jemaat, keluarga, kategori usia, dan import/export.',
                        'route' => 'admin.jemaat.*',
                        'icon' => 'ri-user-shared-line',
                    ],
                    'users' => [
                        'name' => 'Kelola Staf / Pengguna',
                        'description' => 'Mendaftarkan akun staf pelayan dan menetapkan role pengguna.',
                        'route' => 'admin.users.*',
                        'icon' => 'ri-shield-user-line',
                    ],
                    'roles' => [
                        'name' => 'Role Setting',
                        'description' => 'Mengatur hak akses modul, halaman, dan sub-menu untuk tiap role.',
                        'route' => 'admin.roles.*',
                        'icon' => 'ri-shield-keyhole-line',
                    ],
                ],
            ],
        ];
    }

    /**
     * Dapatkan semua key hak akses yang tersedia
     *
     * @return array<string>
     */
    public static function getAllPermissionKeys(): array
    {
        $keys = [];
        foreach (self::getPermissionGroups() as $group) {
            foreach (array_keys($group['items']) as $key) {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    /**
     * Pastikan role bawaan (Admin, Fulltimer, Pendeta) terdaftar di sistem
     */
    public static function ensureDefaultRoles(): void
    {
        $allKeys = self::getAllPermissionKeys();

        // 1. Role Admin (Full Access Permanen)
        self::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator Utama',
                'description' => 'Hak akses penuh tanpa batasan ke seluruh menu, sub-menu, dan konfigurasi sistem.',
                'permissions' => $allKeys,
                'is_system' => true,
            ]
        );

        // 2. Role Fulltimer
        self::firstOrCreate(
            ['name' => 'fulltimer'],
            [
                'display_name' => 'Staff Fulltimer',
                'description' => 'Staf operasional gereja yang mengelola event, warta, streaming, absensi, dan data jemaat.',
                'permissions' => [
                    'dashboard',
                    'event',
                    'warta',
                    'stream',
                    'attendance.persiapan',
                    'attendance.input',
                    'attendance.history',
                    'attendance.report',
                    'jemaat',
                ],
                'is_system' => false,
            ]
        );

        // 3. Role Pendeta / Gembala
        self::firstOrCreate(
            ['name' => 'pendeta'],
            [
                'display_name' => 'Pendeta / Gembala',
                'description' => 'Pelayan pastoral dengan akses monitoring laporan kehadiran, data jemaat, dan warta.',
                'permissions' => [
                    'dashboard',
                    'warta',
                    'attendance.report',
                    'jemaat',
                ],
                'is_system' => false,
            ]
        );
    }
}
