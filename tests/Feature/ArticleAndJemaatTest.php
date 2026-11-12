<?php

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ArticleLike;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    Role::ensureDefaultRoles();
});

test('homepage displays article section with published articles at the bottom', function () {
    $article = Article::firstOrCreate(
        ['slug' => 'test-artikel-beranda'],
        [
            'judul' => 'Artikel Uji Coba Beranda',
            'kategori' => 'Renungan',
            'ringkasan' => 'Ringkasan artikel uji coba untuk beranda.',
            'konten' => 'Isi lengkap artikel beranda untuk pengujian sistem.',
            'status' => 'published',
            'published_at' => now(),
            'author_name' => 'Admin Penguji',
            'isActive' => true,
            'isDelete' => false,
        ]
    );

    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertSee('Artikel & Renungan Terbaru', false);
    $response->assertSee('Artikel Uji Coba Beranda');
    $response->assertSee(route('articles.show', $article->slug));
});

test('public articles index and show pages work properly', function () {
    $article = Article::firstOrCreate(
        ['slug' => 'artikel-firman-hidup'],
        [
            'judul' => 'Artikel Firman Hidup',
            'kategori' => 'Pengajaran',
            'ringkasan' => 'Ringkasan firman hidup.',
            'konten' => 'Paragraf pertama firman hidup.\n\nParagraf kedua firman hidup.',
            'status' => 'published',
            'published_at' => now(),
            'author_name' => 'Pdt. Penguji',
            'isActive' => true,
            'isDelete' => false,
        ]
    );

    // 1. Index
    $indexResponse = $this->get(route('articles.index'));
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee('Artikel Firman Hidup');

    // 2. Show Detail
    $article->refresh();
    $initialViews = (int) ($article->views_count ?? 0);
    $showResponse = $this->get(route('articles.show', $article->slug));
    $showResponse->assertStatus(200);
    $showResponse->assertSee('Artikel Firman Hidup');
    $showResponse->assertSee('Pdt. Penguji');
    $showResponse->assertSee('Paragraf pertama firman hidup');

    $article->refresh();
    expect((int) $article->views_count)->toBeGreaterThan($initialViews);
});

test('jemaat registration enforces unique username and logs in with simple username and password', function () {
    // 1. Check Username availability endpoint
    $checkAvailable = $this->getJson(route('jemaat.check-username', ['username' => 'jemaat_baru_123']));
    $checkAvailable->assertStatus(200)
        ->assertJson(['available' => true]);

    // 2. Register Jemaat
    $registerResponse = $this->post(route('jemaat.register.submit'), [
        'username' => 'jemaat_baru_123',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ]);

    $registerResponse->assertRedirect(route('jemaat.profil'));
    $this->assertAuthenticated();

    $user = User::where('username', 'jemaat_baru_123')->first();
    expect($user)->not->toBeNull()
        ->and($user->role)->toBe('jemaat')
        ->and($user->isJemaat())->toBeTrue();

    // 3. Registering again with duplicate username fails validation
    auth()->logout();
    $dupResponse = $this->post(route('jemaat.register.submit'), [
        'username' => 'jemaat_baru_123',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ]);

    $dupResponse->assertSessionHasErrors(['username']);
});

test('jemaat can login using username and password and is blocked from cms admin', function () {
    $jemaat = User::create([
        'name' => 'Jemaat Maria',
        'username' => 'maria_santoso',
        'email' => 'maria@jemaat.local',
        'password' => bcrypt('password123'),
        'role' => 'jemaat',
    ]);

    // 1. Login Jemaat
    $loginResponse = $this->post(route('jemaat.login.submit'), [
        'login' => 'maria_santoso',
        'password' => 'password123',
    ]);

    $loginResponse->assertRedirect(route('jemaat.profil'));
    $this->assertAuthenticatedAs($jemaat);

    // 2. Jemaat is blocked from accessing CMS Admin Panel
    $cmsResponse = $this->actingAs($jemaat)->get(route('admin.dashboard'));
    $cmsResponse->assertRedirect('/');
    $cmsResponse->assertSessionHas('error');
});

test('jemaat can like and unlike an article and submit comments', function () {
    $jemaat = User::create([
        'name' => 'Daniel Jemaat',
        'username' => 'daniel_p',
        'email' => 'daniel@jemaat.local',
        'password' => bcrypt('password123'),
        'role' => 'jemaat',
    ]);

    $article = Article::create([
        'judul' => 'Artikel Interaksi Kasih',
        'slug' => 'artikel-interaksi-kasih',
        'kategori' => 'Renungan',
        'ringkasan' => 'Tentang kasih persaudaraan.',
        'konten' => 'Kasih itu sabar dan murah hati.',
        'status' => 'published',
        'published_at' => now(),
        'isActive' => true,
        'isDelete' => false,
    ]);

    // 1. Unauthenticated like returns 401 JSON
    $unauthLike = $this->postJson(route('articles.like', $article->id));
    $unauthLike->assertStatus(401)
        ->assertJson(['auth_required' => true]);

    // 2. Jemaat Likes the article
    $likeResponse = $this->actingAs($jemaat)->postJson(route('articles.like', $article->id));
    $likeResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
            'liked' => true,
            'likes_count' => 1,
        ]);

    expect(ArticleLike::where('article_id', $article->id)->where('user_id', $jemaat->id)->exists())->toBeTrue()
        ->and($jemaat->hasLikedArticle($article->id))->toBeTrue();

    // 3. Jemaat Unlikes the article (toggle)
    $unlikeResponse = $this->actingAs($jemaat)->postJson(route('articles.like', $article->id));
    $unlikeResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
            'liked' => false,
            'likes_count' => 0,
        ]);

    // 4. Jemaat Submits Comment
    $commentResponse = $this->actingAs($jemaat)->postJson(route('articles.comment', $article->id), [
        'komentar' => 'Amin, puji Tuhan firman yang sangat menguatkan!',
    ]);

    $commentResponse->assertStatus(200)
        ->assertJson(['success' => true]);

    $comment = ArticleComment::where('article_id', $article->id)->where('user_id', $jemaat->id)->first();
    expect($comment)->not->toBeNull()
        ->and($comment->komentar)->toBe('Amin, puji Tuhan firman yang sangat menguatkan!');

    // 5. Jemaat Profile reflects the comments
    $profileResponse = $this->actingAs($jemaat)->get(route('jemaat.profil'));
    $profileResponse->assertStatus(200);
    $profileResponse->assertSee('Amin, puji Tuhan firman yang sangat menguatkan!');
});

test('admin can manage articles in cms (create, edit, delete)', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_artikel_test@giasurabaya.org'],
        [
            'name' => 'Admin Artikel Test',
            'username' => 'admin_artikel',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]
    );

    // 1. Index CMS
    $indexResponse = $this->actingAs($admin)->get(route('admin.article.index'));
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee('Kelola Artikel');

    // 2. Create Article
    $storeResponse = $this->actingAs($admin)->post(route('admin.article.store'), [
        'judul' => 'Artikel Baru Dari CMS',
        'kategori' => 'Kabar Gereja',
        'ringkasan' => 'Ringkasan artikel dari CMS.',
        'konten' => 'Ini adalah konten lengkap artikel yang ditulis dari CMS admin.',
        'status' => 'published',
    ]);

    $storeResponse->assertRedirect(route('admin.article.index'));

    $article = Article::where('judul', 'Artikel Baru Dari CMS')->first();
    expect($article)->not->toBeNull()
        ->and($article->slug)->toBe('artikel-baru-dari-cms')
        ->and($article->author_id)->toBe($admin->id);

    // 3. Edit Article
    $updateResponse = $this->actingAs($admin)->put(route('admin.article.update', $article->id), [
        'judul' => 'Artikel Baru Dari CMS (Revisi)',
        'kategori' => 'Renungan',
        'ringkasan' => 'Ringkasan direvisi.',
        'konten' => 'Konten direvisi.',
        'status' => 'published',
    ]);
    $updateResponse->assertRedirect(route('admin.article.index'));

    $article->refresh();
    expect($article->judul)->toBe('Artikel Baru Dari CMS (Revisi)')
        ->and($article->kategori)->toBe('Renungan');

    // 4. Soft Delete Article
    $deleteResponse = $this->actingAs($admin)->delete(route('admin.article.destroy', $article->id));
    $deleteResponse->assertRedirect(route('admin.article.index'));

    $article->refresh();
    expect($article->isDelete)->toBeTrue();
});
