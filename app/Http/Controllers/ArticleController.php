<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ArticleLike;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Halaman Arsip Seluruh Artikel Publik
     */
    public function index(Request $request)
    {
        $query = Article::with(['author', 'likes', 'comments'])
            ->where('isDelete', false)
            ->where('isActive', true)
            ->where('status', 'published');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('ringkasan', 'like', "%{$q}%")
                    ->orWhere('konten', 'like', "%{$q}%");
            });
        }

        $articles = $query->orderBy('published_at', 'desc')->paginate(9)->withQueryString();

        $featuredArticle = null;
        if (! $request->filled('kategori') && ! $request->filled('q') && $request->get('page', 1) == 1) {
            $featuredArticle = $articles->first();
        }

        $kategoriList = Article::where('isDelete', false)
            ->where('status', 'published')
            ->distinct()
            ->pluck('kategori')
            ->filter()
            ->values();

        return view('homepage.articles.index', compact('articles', 'featuredArticle', 'kategoriList'));
    }

    /**
     * Halaman Detail Artikel Publik
     */
    public function show($slug)
    {
        $article = Article::with(['author', 'likes', 'comments.user'])
            ->where('slug', $slug)
            ->where('isDelete', false)
            ->where('isActive', true)
            ->firstOrFail();

        // Increment views
        $article->increment('views_count');

        // Artikel terkait
        $relatedArticles = Article::where('isDelete', false)
            ->where('status', 'published')
            ->where('id', '!=', $article->id)
            ->where('kategori', $article->kategori)
            ->latest('published_at')
            ->take(3)
            ->get();

        if ($relatedArticles->isEmpty()) {
            $relatedArticles = Article::where('isDelete', false)
                ->where('status', 'published')
                ->where('id', '!=', $article->id)
                ->latest('published_at')
                ->take(3)
                ->get();
        }

        $user = auth()->user();
        $isLiked = $user ? $article->isLikedBy($user) : false;

        return view('homepage.articles.show', compact('article', 'relatedArticles', 'isLiked'));
    }

    /**
     * Toggle Like Artikel (Bisa via AJAX)
     */
    public function toggleLike(Request $request, $id)
    {
        if (! auth()->check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'auth_required' => true,
                    'message' => 'Silakan masuk sebagai jemaat untuk menyukai artikel ini.',
                    'redirect_url' => route('jemaat.login', ['redirect' => url()->previous()]),
                ], 401);
            }

            return redirect()->route('jemaat.login', ['redirect' => url()->previous()])
                ->with('warning', 'Silakan masuk sebagai jemaat untuk menyukai artikel ini.');
        }

        $article = Article::where('isDelete', false)->findOrFail($id);
        $userId = auth()->id();

        $existingLike = ArticleLike::where('article_id', $article->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            ArticleLike::create([
                'article_id' => $article->id,
                'user_id' => $userId,
            ]);
            $liked = true;
        }

        $likesCount = $article->likes()->count();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $likesCount,
                'message' => $liked ? 'Terima kasih telah menyukai artikel ini!' : 'Like dibatalkan.',
            ]);
        }

        return redirect()->back();
    }

    /**
     * Kirim Komentar pada Artikel
     */
    public function storeComment(Request $request, $id)
    {
        if (! auth()->check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'auth_required' => true,
                    'message' => 'Silakan masuk sebagai jemaat untuk menulis komentar.',
                    'redirect_url' => route('jemaat.login', ['redirect' => url()->previous()]),
                ], 401);
            }

            return redirect()->route('jemaat.login', ['redirect' => url()->previous()])
                ->with('warning', 'Silakan masuk sebagai jemaat untuk menulis komentar.');
        }

        $request->validate([
            'komentar' => 'required|string|min:3|max:1000',
        ]);

        $article = Article::where('isDelete', false)->findOrFail($id);
        $user = auth()->user();

        $comment = ArticleComment::create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'komentar' => trim($request->komentar),
            'is_approved' => true,
            'isActive' => true,
            'isDelete' => false,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Komentar Anda berhasil dikirim!',
                'comment' => [
                    'id' => $comment->id,
                    'author_name' => $user->name ?: $user->username,
                    'author_avatar' => $user->avatar_url,
                    'content' => nl2br(e($comment->komentar)),
                    'date' => $comment->formatted_date,
                ],
                'comments_count' => $article->comments()->count(),
            ]);
        }

        return redirect()->back()->with('success', 'Komentar Anda berhasil ditambahkan!');
    }
}
