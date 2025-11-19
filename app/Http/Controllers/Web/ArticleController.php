<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request, ?Category $category = null, ?Author $author = null): View
    {
        $articlesQuery = Article::query()
            ->with(['author.profileImage', 'category', 'featureImage'])
            ->where('is_published', true)
            ->latest('published_at')
            ->latest('created_at');

        $selectedCategory = $category;
        if (!$selectedCategory && $categorySlug = $request->query('category')) {
            $selectedCategory = Category::query()
                ->where('slug', $categorySlug)
                ->where('is_active', true)
                ->firstOrFail();
        }

        if ($selectedCategory) {
            $articlesQuery->where('category_id', $selectedCategory->id);
        }

        $selectedAuthor = $author?->loadMissing('profileImage');
        if (!$selectedAuthor && $authorParam = $request->query('author')) {
            $selectedAuthor = Author::query()
                ->with('profileImage')
                ->when(
                    is_numeric($authorParam),
                    fn ($query) => $query->where('id', (int) $authorParam),
                    fn ($query) => $query->where('slug', $authorParam),
                )
                ->firstOrFail();
        }

        if ($selectedAuthor) {
            $articlesQuery->where('author_id', $selectedAuthor->id);
        }

        $articles = $articlesQuery->paginate(9)->withQueryString();

        $popularArticles = Article::query()
            ->with(['featureImage'])
            ->where('is_published', true)
            ->orderByDesc('view_count')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->take(4)
            ->get();

        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['articles' => function ($query) {
                $query->where('is_published', true);
            }])
            ->orderBy('name')
            ->get();

        $featuredAuthors = Author::query()
            ->with('profileImage')
            ->withCount(['articles' => function ($query) {
                $query->where('is_published', true);
            }])
            ->orderByDesc('articles_count')
            ->take(6)
            ->get();

        $heroTitle = 'Tüm Makale Listesi';
        $heroDescription = 'Dergimizde yayınlanan tüm makaleleri görüntüleyebilir, konu ve yazarlara göre filtreleyebilirsiniz.';

        if ($selectedCategory && $selectedAuthor) {
            $heroTitle = "{$selectedCategory->name} · {$selectedAuthor->name}";
            $heroDescription = "{$selectedAuthor->name} tarafından kaleme alınan {$selectedCategory->name} konulu makaleleri inceleyin.";
        } elseif ($selectedCategory) {
            $heroTitle = "{$selectedCategory->name} Konusunda Yazılar";
            $heroDescription = $selectedCategory->description
                ? strip_tags($selectedCategory->description)
                : "{$selectedCategory->name} kategorisinde yazılmış makaleleri görüntüleyebilir yazarlara göre filtreleyebilirsiniz.";
        } elseif ($selectedAuthor) {
            $heroTitle = "{$selectedAuthor->name} Kaleminden Yazılar";
            $heroDescription = "{$selectedAuthor->name} tarafından kaleme alınan makaleleri bir arada görün ve yazarın tüm içeriklerine hızlıca ulaşın.";
        }

        return view('makaleler.index', [
            'articles' => $articles,
            'popularArticles' => $popularArticles,
            'categories' => $categories,
            'featuredAuthors' => $featuredAuthors,
            'selectedCategory' => $selectedCategory,
            'selectedAuthor' => $selectedAuthor,
            'heroTitle' => $heroTitle,
            'heroDescription' => $heroDescription,
        ]);
    }

    public function show(Article $article): View
    {
        abort_unless($article->is_published, 404);
        $article->increment('view_count');
        $article->loadMissing(['author.profileImage', 'category', 'featureImage']);

        $relatedArticles = Article::query()
            ->with('featureImage')
            ->where('is_published', true)
            ->where('id', '!=', $article->id)
            ->when($article->category_id, function ($query) use ($article) {
                $query->where('category_id', $article->category_id);
            })
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        return view('makaleler.show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}
