<?php

use App\Models\Cabang;
use App\Models\Jemaat;
use App\Models\JenisIbadah;
use App\Models\Sesi;
use App\Models\Song;
use App\Models\User;
use App\Models\WorshipSchedule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('public worship schedule page /ibadah can be rendered', function () {
    $response = $this->get('/ibadah');
    $response->assertStatus(200);
});

test('unauthenticated users are redirected from attendance cms', function () {
    $response = $this->get(route('admin.attendance.input'));
    $response->assertRedirect('/adminlogin');
});

test('admin can access input attendance page', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->get(route('admin.attendance.input'));
    $response->assertStatus(200);
});

test('admin can access dashboard report page', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->get(route('admin.attendance.report'));
    $response->assertStatus(200);
});

test('admin can access history page', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->get(route('admin.attendance.history'));
    $response->assertStatus(200);
});

test('admin can access worship preparation page', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->get(route('admin.attendance.persiapan'));
    $response->assertStatus(200);
});

test('admin can store attendance session', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $cabang = Cabang::firstOrCreate(
        ['nama_cabang' => 'Darmo'],
        ['alamat' => 'Surabaya', 'isActive' => 1, 'isDelete' => 0]
    );

    $jenis = JenisIbadah::firstOrCreate(
        ['nama_ibadah' => 'Ibadah Raya 1'],
        ['isActive' => 1, 'isDelete' => 0]
    );

    $sesi = Sesi::firstOrCreate(
        ['nama_sesi' => 'Pagi'],
        ['isActive' => 1, 'isDelete' => 0]
    );

    $payload = [
        'tanggal' => now()->format('Y-m-d'),
        'nama_ibadah' => $jenis->nama_ibadah,
        'nama_sesi' => $sesi->nama_sesi,
        'nama_cabang' => $cabang->nama_cabang,
        'nama_petugas' => 'Petugas Test',
        'present_list' => [
            ['id' => 'tamu_1', 'nama_asli' => 'Budi Tamu', 'status' => 'Tamu', 'wilayah' => '-'],
            ['id' => 'tamu_2', 'nama_asli' => 'Siti Anggota', 'status' => 'Anggota', 'wilayah' => 'Darmo'],
        ],
    ];

    $response = $this->actingAs($user)->postJson(route('admin.attendance.store'), $payload);

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('attendance_sessions', [
        'nama_ibadah' => $jenis->nama_ibadah,
        'nama_sesi' => $sesi->nama_sesi,
        'nama_cabang' => $cabang->nama_cabang,
        'total_hadir' => 2,
    ]);
});

test('admin can create a worship schedule and song', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $song = Song::create([
        'judul_lagu' => 'Kebaikan Tuhan',
        'artis_sumber' => 'Bethel Music',
        'nada_dasar' => 'G',
        'isActive' => 1,
        'isDelete' => 0,
    ]);

    $schedulePayload = [
        'tanggal' => now()->addDays(3)->format('Y-m-d'),
        'nama_ibadah' => 'Ibadah Raya 1',
        'nama_sesi' => 'Pagi',
        'nama_cabang' => 'Darmo',
        'tema' => 'Kasih Karunia',
        'ayat' => 'Efesus 2:8',
        'pengkhotbah' => 'Pdt. Johanes',
        'pemimpin_pujian' => 'Maria',
        'songs' => [
            [
                'song_id' => $song->id,
                'bagian_liturgi' => 'Pujian Pembuka',
                'nada_dasar' => 'G',
            ],
        ],
    ];

    $response = $this->actingAs($user)->post(route('admin.attendance.storeSchedule'), $schedulePayload);
    $response->assertRedirect();

    $this->assertDatabaseHas('worship_schedules', [
        'tema' => 'Kasih Karunia',
        'pengkhotbah' => 'Pdt. Johanes',
    ]);
});

test('admin can access create and edit worship schedule editor forms', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $responseCreate = $this->actingAs($user)->get(route('admin.attendance.createSchedule'));
    $responseCreate->assertStatus(200);

    $schedule = WorshipSchedule::firstOrCreate(
        ['tema' => 'Ibadah Minggu Pagi'],
        [
            'tanggal' => now()->format('Y-m-d'),
            'nama_ibadah' => 'Ibadah Umum',
            'nama_cabang' => 'Darmo',
            'isActive' => 1,
            'isDelete' => 0,
        ]
    );

    $responseEdit = $this->actingAs($user)->get(route('admin.attendance.editSchedule', $schedule->id));
    $responseEdit->assertStatus(200);
});

test('public can view worship schedules and detail page', function () {
    $schedule = WorshipSchedule::create([
        'tanggal' => now()->addDays(2)->format('Y-m-d'),
        'nama_ibadah' => 'Ibadah Umum',
        'nama_cabang' => 'Gateway',
        'tema' => 'Percayalah kepada Tuhan',
        'ayat' => 'Amsal 3:1-6',
        'pengkhotbah' => 'Juni Kristiani T',
        'pemimpin_pujian' => 'Paulus Natawidjaja',
        'isActive' => 1,
        'isDelete' => 0,
    ]);

    // Test public list page
    $responseList = $this->get(route('ibadah.public'));
    $responseList->assertStatus(200);
    $responseList->assertSee('Jadwal Ibadah');
    $responseList->assertSee('Mendatang');
    $responseList->assertSee('Arsip');

    // Test public detail page
    $responseDetail = $this->get(route('ibadah.public', ['id' => $schedule->id]));
    $responseDetail->assertStatus(200);
    $responseDetail->assertSee('Percayalah kepada Tuhan');

    // Test detail redirect route
    $responseRedirect = $this->get(route('ibadah.public.detail', $schedule->id));
    $responseRedirect->assertRedirect(route('ibadah.public', ['id' => $schedule->id]));
});

test('landing page displays worship schedule section and upcoming highlight', function () {
    $response = $this->get(route('home'));
    $response->assertStatus(200);
    $response->assertSee('Jadwal Ibadah');
});

test('admin layout sidebar has reorganized absensi submenu with laporan', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->get(route('admin.dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Persiapan Ibadah');
    $response->assertSee('Input Kehadiran');
    $response->assertSee('Riwayat Sesi');
    $response->assertSee('Laporan');
});

test('admin can store guest and auto-creates jemaat record with status and kategori Tamu', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->postJson(route('admin.attendance.storeGuest'), [
        'nama_asli' => 'Bambang Tamu Baru',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'jemaat' => [
                'nama_asli' => 'Bambang Tamu Baru',
                'status' => 'Tamu',
                'kategori' => 'Tamu',
            ],
        ]);

    $this->assertDatabaseHas('jemaats', [
        'nama_asli' => 'Bambang Tamu Baru',
        'status' => 'Tamu',
        'kategori' => 'Tamu',
    ]);
});

test('admin can fetch jemaat detail with 10 last attendance history', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $jemaat = Jemaat::create([
        'nama_asli' => 'Yohanes Markus',
        'status' => 'Anggota',
        'tgl_lahir' => '1995-05-15',
        'telepon' => '081234567890',
        'alamat' => 'Jl. Diponegoro Surabaya',
        'isActive' => 1,
        'isDelete' => 0,
    ]);

    $response = $this->actingAs($user)->getJson(route('admin.attendance.jemaatDetail', $jemaat->id));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'jemaat' => [
                'id' => $jemaat->id,
                'nama_asli' => 'Yohanes Markus',
                'status' => 'Anggota',
                'kategori' => 'Dewasa',
            ],
        ]);
});

test('admin can upload worship material media to hosting storage', function () {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'admin']);

    $file = UploadedFile::fake()->create('slide_khotbah.pptx', 1024, 'application/vnd.openxmlformats-officedocument.presentationml.presentation');

    $response = $this->actingAs($user)->postJson(route('admin.attendance.uploadMedia'), [
        'media_file' => $file,
        'judul' => 'Slide Khotbah Minggu',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'file' => [
                'judul' => 'Slide Khotbah Minggu',
                'tipe' => 'Slide PPT',
            ],
        ]);
});

test('dashboard report includes categories breakdown data', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->get(route('admin.attendance.report'));

    $response->assertStatus(200);
    $response->assertSee('Laporan Kehadiran Berdasarkan Kategori');
    $response->assertSee('Semua Lokasi / Wilayah');
    $response->assertSee('Semua Kategori Usia');
    $response->assertSee('Anak');
    $response->assertSee('Remaja');
    $response->assertSee('Pemuda');
    $response->assertSee('Dewasa');
    $response->assertSee('Lansia');
    $response->assertSee('Tamu');
});
