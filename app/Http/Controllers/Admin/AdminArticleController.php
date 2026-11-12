<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdminArticleController extends Controller
{
    /**
     * Menampilkan daftar artikel di CMS
     */
    public function index(Request $request)
    {
        $query = Article::with(['author', 'likes', 'comments'])
            ->where('isDelete', false);

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        // Filter Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // Statistik Metrik
        $stats = [
            'total' => Article::where('isDelete', false)->count(),
            'published' => Article::where('isDelete', false)->where('status', 'published')->count(),
            'draft' => Article::where('isDelete', false)->where('status', 'draft')->count(),
            'total_views' => Article::where('isDelete', false)->sum('views_count'),
            'total_comments' => ArticleComment::where('isDelete', false)->count(),
        ];

        $kategoriList = ['Renungan', 'Pengajaran', 'Kabar Gereja', 'Kesaksian', 'Komunitas', 'Umum'];

        return view('admin.articles.indexCMS', compact('articles', 'stats', 'kategoriList'));
    }

    /**
     * Form tambah artikel baru
     */
    public function create()
    {
        $kategoriList = ['Renungan', 'Pengajaran', 'Kabar Gereja', 'Kesaksian', 'Komunitas', 'Umum'];

        return view('admin.articles.createCMS', compact('kategoriList'));
    }

    /**
     * Simpan artikel baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:50',
            'ringkasan' => 'nullable|string|max:600',
            'konten' => 'required|string',
            'status' => 'required|in:published,draft',
            'gambar_banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $bannerName = null;
        if ($request->hasFile('gambar_banner')) {
            $file = $request->file('gambar_banner');
            $bannerName = 'article_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $targetDir = public_path('img/articles');
            if (! File::isDirectory($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }
            $file->move($targetDir, $bannerName);
        }

        $slug = Article::generateUniqueSlug($request->judul);
        $user = auth()->user();

        Article::create([
            'judul' => $request->judul,
            'slug' => $slug,
            'kategori' => $request->kategori,
            'ringkasan' => $request->ringkasan ?: Str::limit(strip_tags($request->konten), 180),
            'konten' => $request->konten,
            'gambar_banner' => $bannerName,
            'status' => $request->status,
            'published_at' => $request->status === 'published' ? now() : null,
            'author_id' => $user->id,
            'author_name' => $user->name ?? $user->username ?? 'Admin',
            'isActive' => true,
            'isDelete' => false,
        ]);

        return redirect()->route('admin.article.index')->with('success', 'Artikel berhasil dipublikasikan!');
    }

    /**
     * Form edit artikel
     */
    public function edit($id)
    {
        $article = Article::with(['comments.user'])->where('isDelete', false)->findOrFail($id);
        $kategoriList = ['Renungan', 'Pengajaran', 'Kabar Gereja', 'Kesaksian', 'Komunitas', 'Umum'];

        return view('admin.articles.editCMS', compact('article', 'kategoriList'));
    }

    /**
     * Update artikel
     */
    public function update(Request $request, $id)
    {
        $article = Article::where('isDelete', false)->findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:50',
            'ringkasan' => 'nullable|string|max:600',
            'konten' => 'required|string',
            'status' => 'required|in:published,draft',
            'gambar_banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $bannerName = $article->gambar_banner;
        if ($request->hasFile('gambar_banner')) {
            $file = $request->file('gambar_banner');
            $newBannerName = 'article_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $targetDir = public_path('img/articles');
            if (! File::isDirectory($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }
            $file->move($targetDir, $newBannerName);

            // Hapus banner lama jika ada
            if ($bannerName && File::exists($targetDir.'/'.$bannerName)) {
                File::delete($targetDir.'/'.$bannerName);
            }
            $bannerName = $newBannerName;
        }

        // Perbarui slug jika judul berubah
        $slug = $article->slug;
        if ($article->judul !== $request->judul) {
            $slug = Article::generateUniqueSlug($request->judul, $article->id);
        }

        $publishedAt = $article->published_at;
        if ($request->status === 'published' && ! $publishedAt) {
            $publishedAt = now();
        }

        $article->update([
            'judul' => $request->judul,
            'slug' => $slug,
            'kategori' => $request->kategori,
            'ringkasan' => $request->ringkasan ?: Str::limit(strip_tags($request->konten), 180),
            'konten' => $request->konten,
            'gambar_banner' => $bannerName,
            'status' => $request->status,
            'published_at' => $publishedAt,
        ]);

        return redirect()->route('admin.article.index')->with('success', 'Artikel berhasil diperbarui!');
    }

    /**
     * Hapus artikel (soft delete)
     */
    public function destroy($id)
    {
        $article = Article::where('isDelete', false)->findOrFail($id);
        $article->update([
            'isDelete' => true,
            'isActive' => false,
        ]);

        return redirect()->route('admin.article.index')->with('success', 'Artikel berhasil dihapus.');
    }

    /**
     * Toggle status publikasi (Published / Draft)
     */
    public function toggleStatus($id)
    {
        $article = Article::where('isDelete', false)->findOrFail($id);
        $newStatus = ($article->status === 'published') ? 'draft' : 'published';

        $article->update([
            'status' => $newStatus,
            'published_at' => ($newStatus === 'published' && ! $article->published_at) ? now() : $article->published_at,
        ]);

        return redirect()->back()->with('success', "Status artikel diubah menjadi {$newStatus}.");
    }

    /**
     * Hapus komentar yang tidak pantas (Moderasi Komentar oleh Admin/Fulltimer)
     */
    public function destroyComment($articleId, $commentId)
    {
        $comment = ArticleComment::where('article_id', $articleId)->findOrFail($commentId);
        $comment->update([
            'isDelete' => true,
            'isActive' => false,
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil dihapus.');
    }
}
