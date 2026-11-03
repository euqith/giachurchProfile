<?php

use App\Models\Cabang;
use App\Models\ChurchProfile;
use App\Models\Event;
use App\Models\Stream;
use App\Models\User;
use App\Models\Warta;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

test('landing page loads with soft earth tone layout, church profile, and sections', function () {
    $profile = ChurchProfile::getProfile();

    $response = $this->get(route('home'));
    $response->assertStatus(200);
    $response->assertSee($profile->nama_gereja);
    $response->assertSee('Tentang Gereja');
    $response->assertSee('Visi Pelayanan');
    $response->assertSee('Misi Pelayanan');
    $response->assertSee('Jadwal Ibadah 5 Wilayah');
    $response->assertSee('Ibadah Online Gereja');

    // Menu "Jadwal Ibadah (Liturgi)" is removed from the navbar
    $response->assertDontSee('LIHAT JADWAL & LITURGI');
});

test('landing page displays ongoing and future events on banner 2 and beyond', function () {
    $cabang = Cabang::create([
        'nama_cabang' => 'Darmo Pusat',
        'lokasi' => 'Surabaya',
        'isActive' => 1,
        'isDelete' => 0,
    ]);

    $futureEvent = Event::create([
        'nama_acara' => 'Kebaktian Kebangunan Rohani 2026',
        'cabang_id' => $cabang->id,
        'waktu_mulai' => now()->addDays(5),
        'waktu_selesai' => now()->addDays(5)->addHours(3),
        'lokasi_spesifik' => 'Auditorium Darmo',
        'deskripsi' => 'KKR tahunan jemaat.',
        'isActive' => 1,
        'isDelete' => 0,
    ]);

    $response = $this->get(route('home'));
    $response->assertStatus(200);
    $response->assertSee('Kebaktian Kebangunan Rohani 2026');
});

test('landing page detects active live streams and offline state correctly', function () {
    $cabang = Cabang::firstOrCreate(
        ['nama_cabang' => 'Darmo Pusat Test'],
        ['lokasi' => 'Surabaya', 'isActive' => 1, 'isDelete' => 0]
    );

    $stream = Stream::firstOrCreate(
        ['cabang_id' => $cabang->id],
        [
            'judul_streaming' => 'Ibadah Raya Test',
            'youtube_id' => 'dQw4w9WgXcQ',
            'waktu_tayang' => 'Minggu 08.00 WIB',
            'is_live' => false,
            'isActive' => true,
            'isDelete' => false,
        ]
    );

    // 1. When all streams are offline
    Stream::query()->update(['is_live' => false]);

    $responseOffline = $this->get(route('home'));
    $responseOffline->assertStatus(200);
    $responseOffline->assertSee('Saat Ini Tidak Ada Siaran Langsung (Offline)');

    // 2. When a stream is set to live
    $stream->update(['is_live' => true]);

    $responseLive = $this->get(route('home'));
    $responseLive->assertStatus(200);
    $responseLive->assertSee('SEDANG BERLANGSUNG (LIVE)');
});

test('warta jemaat page loads published bulletins and archives', function () {
    $warta = Warta::create([
        'judul_edisi' => 'Warta Jemaat Edisi Uji Coba',
        'tanggal_rilis' => now()->format('Y-m-d'),
        'isActive' => 1,
        'isDelete' => 0,
    ]);

    $response = $this->get(route('warta.index'));
    $response->assertStatus(200);
    $response->assertSee('Warta Jemaat Digital');
    $response->assertSee('Warta Jemaat Edisi Uji Coba');
});

test('event page loads and filters events properly', function () {
    $response = $this->get(route('event.index'));
    $response->assertStatus(200);
    $response->assertSee('Kalender Kegiatan');
    $response->assertSee('Event');
});

test('admin can manage church profile and upload photo', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('admin.church.index'));
    $response->assertStatus(200);
    $response->assertSee('Pengaturan Profil Gereja');

    $file = UploadedFile::fake()->image('gereja_baru.jpg');

    $postResponse = $this->actingAs($admin)->post(route('admin.church.update'), [
        'nama_gereja' => 'GIA Surabaya Update',
        'tagline' => 'Gereja yang memuliakan Tuhan',
        'tentang_gereja' => 'Deskripsi tentang gereja kami.',
        'visi' => 'Menjadi garam dan terang dunia.',
        'misi' => "Misi 1\nMisi 2",
        'instagram_url' => 'https://www.instagram.com/giasurabayaupdate',
        'foto_gereja' => $file,
    ]);

    $postResponse->assertRedirect();
    $this->assertDatabaseHas('church_profiles', [
        'nama_gereja' => 'GIA Surabaya Update',
        'tagline' => 'Gereja yang memuliakan Tuhan',
        'instagram_url' => 'https://www.instagram.com/giasurabayaupdate',
    ]);

    // Clean up uploaded test file
    $profile = ChurchProfile::first();
    if ($profile && $profile->foto_gereja) {
        $path = public_path('img/admin/church/'.$profile->foto_gereja);
        if (File::exists($path)) {
            File::delete($path);
        }
    }
});

test('admin can manage streams and toggle live status', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $cabang = Cabang::firstOrCreate(
        ['nama_cabang' => 'Darmo Admin Test'],
        ['lokasi' => 'Surabaya', 'isActive' => 1, 'isDelete' => 0]
    );

    $stream = Stream::firstOrCreate(
        ['cabang_id' => $cabang->id],
        [
            'judul_streaming' => 'Ibadah Raya Admin Test',
            'youtube_id' => 'dQw4w9WgXcQ',
            'waktu_tayang' => 'Minggu 08.00 WIB',
            'is_live' => false,
            'isActive' => true,
            'isDelete' => false,
        ]
    );

    $response = $this->actingAs($admin)->get(route('admin.stream.index'));
    $response->assertStatus(200);
    $response->assertSee('Kelola Live Streaming');

    // Toggle status
    $currentStatus = $stream->is_live;
    $toggleResponse = $this->actingAs($admin)->post(route('admin.stream.toggle', $stream->id));
    $toggleResponse->assertRedirect();

    $stream->refresh();
    expect($stream->is_live)->toBe(! $currentStatus);

    // Save YouTube API Key
    $apiKeyResponse = $this->actingAs($admin)->post(route('admin.stream.api-key'), [
        'youtube_api_key' => 'AIzaSyTestApiKey12345',
    ]);
    $apiKeyResponse->assertRedirect();
    $this->assertDatabaseHas('church_profiles', [
        'youtube_api_key' => 'AIzaSyTestApiKey12345',
    ]);

    // Trigger auto-detect all
    $autoDetectResponse = $this->actingAs($admin)->post(route('admin.stream.auto-detect'));
    $autoDetectResponse->assertRedirect();
});

test('admin can manage cabang maps and schedule and homepage reflects it', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_cabang_test@giasurabaya.org'],
        [
            'name' => 'Admin Cabang Test',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]
    );

    $cabang = Cabang::create([
        'nama_cabang' => 'Darmo Pusat Maps Test',
        'lokasi' => 'Jl. Raya Darmo Harapan No.PF-1, Surabaya',
        'maps_url' => 'https://maps.app.goo.gl/sampleDarmoLink',
        'maps_embed' => 'https://www.google.com/maps/embed?pb=sampleDarmoEmbed',
        'jadwal_ibadah' => "Ibadah 1: Minggu 06.00 WIB\nIbadah 2: Minggu 08.00 WIB",
        'isActive' => 1,
        'isDelete' => 0,
    ]);

    // 1. Admin Index Cabang sees maps
    $indexResponse = $this->actingAs($admin)->get(route('admin.cabang.index'));
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee('Darmo Pusat Maps Test');
    $indexResponse->assertSee('Buka Maps');

    // 2. Admin Update Cabang
    $updateResponse = $this->actingAs($admin)->put(route('admin.cabang.update', $cabang->id), [
        'nama_cabang' => 'Darmo Pusat Maps Updated',
        'lokasi' => 'Jl. Raya Darmo Harapan No.PF-1 Baru',
        'maps_url' => 'https://maps.google.com/?q=DarmoUpdated',
        'maps_embed' => '<iframe src="https://www.google.com/maps/embed?pb=updatedEmbed"></iframe>',
        'jadwal_ibadah' => "Ibadah Pagi: 07.00 WIB\nDoa Malam: 19.00 WIB",
        'isActive' => 1,
    ]);
    $updateResponse->assertRedirect();

    $cabang->refresh();
    expect($cabang->nama_cabang)->toBe('Darmo Pusat Maps Updated')
        ->and($cabang->maps_url)->toBe('https://maps.google.com/?q=DarmoUpdated')
        ->and($cabang->google_maps_embed_url)->toBe('https://www.google.com/maps/embed?pb=updatedEmbed')
        ->and($cabang->jadwal_lines)->toContain('Ibadah Pagi: 07.00 WIB');

    // 3. Homepage reflects the updated branch, maps, and schedules
    $homeResponse = $this->get(route('home'));
    $homeResponse->assertStatus(200);
    $homeResponse->assertSee('Darmo Pusat Maps Updated');
    $homeResponse->assertSee('https://maps.google.com/?q=DarmoUpdated');
    $homeResponse->assertSee('Ibadah Pagi: 07.00 WIB');
});

test('admin login screen renders with dark luxury theme and cms brand colors', function () {
    $response = $this->get(route('admin.login'));

    $response->assertStatus(200);
    $response->assertSee('Log In Admin CMS');
    $response->assertSee('Gereja Isa Almasih Surabaya');
    $response->assertSee('Masuk Dashboard');
    $response->assertSee('giaslogo-darkbg.png');
});
