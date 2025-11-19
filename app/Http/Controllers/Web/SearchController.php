<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));

        $articles = collect();
        $issues = collect();
        $authors = collect();
        $categories = collect();
        $totalResults = 0;

        if ($query !== '') {
            $articles = Article::query()
                ->with(['author', 'featureImage', 'category'])
                ->where('is_published', true)
                ->where(function ($builder) use ($query) {
                    $builder->where('title', 'like', "%{$query}%")
                        ->orWhere('excerpt', 'like', "%{$query}%")
                        ->orWhere('content', 'like', "%{$query}%");
                })
                ->latest('published_at')
                ->latest('created_at')
                ->take(6)
                ->get();

            $issues = Issue::query()
                ->with('coverImage')
                ->where(function ($builder) use ($query) {
                    $builder->where('title', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('slug', 'like', "%{$query}%");
                })
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->take(6)
                ->get();

            $authors = Author::query()
                ->with('profileImage')
                ->where('name', 'like', "%{$query}%")
                ->orderBy('name')
                ->take(6)
                ->get();

            $categories = Category::query()
                ->where('is_active', true)
                ->where(function ($builder) use ($query) {
                    $builder->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                ->withCount(['articles' => fn ($builder) => $builder->where('is_published', true)])
                ->orderByDesc('articles_count')
                ->take(6)
                ->get();

            $totalResults = $articles->count() + $issues->count() + $authors->count() + $categories->count();
        }

        return view('search.index', [
            'query' => $query,
            'articles' => $articles,
            'issues' => $issues,
            'authors' => $authors,
            'categories' => $categories,
            'totalResults' => $totalResults,
        ]);
    }
}
