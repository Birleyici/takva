<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $data = $this->performSearch($query);

        return view('search.index', [
            'query' => $query,
            'articles' => $data['articles'],
            'issues' => $data['issues'],
            'authors' => $data['authors'],
            'categories' => $data['categories'],
            'totalResults' => $data['total'],
        ]);
    }

    public function api(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));
        $data = $this->performSearch($query);

        $response = [
            'query' => $query,
            'total' => $data['total'],
            'articles' => $data['articles']->map(function (Article $article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'description' => Str::limit(strip_tags($article->excerpt ?: $article->content), 150),
                    'url' => route('articles.show', $article),
                    'meta' => $article->category?->name,
                    'metaInfo' => optional($article->published_at)->translatedFormat('d F Y'),
                    'image' => $article->featureImage?->url,
                ];
            }),
            'issues' => $data['issues']->map(function (Issue $issue) {
                return [
                    'id' => $issue->id,
                    'title' => $issue->title,
                    'description' => Str::limit(strip_tags($issue->description), 140),
                    'url' => route('issues.show', $issue),
                    'meta' => $issue->month_name.' '.$issue->year,
                    'metaInfo' => null,
                    'image' => $issue->coverImage?->url,
                ];
            }),
            'authors' => $data['authors']->map(function (Author $author) {
                return [
                    'id' => $author->id,
                    'name' => $author->name,
                    'title' => $author->name,
                    'url' => route('articles.author', ['author' => $author->slug]),
                    'image' => $author->profileImage?->url,
                    'description' => ($author->articles_count ?? $author->articles()->where('is_published', true)->count()).' makale',
                    'meta' => null,
                    'metaInfo' => null,
                ];
            }),
            'categories' => $data['categories']->map(function (Category $category) {
                return [
                    'id' => $category->id,
                    'title' => $category->name,
                    'name' => $category->name,
                    'description' => Str::limit(strip_tags((string) $category->description), 150),
                    'url' => route('articles.category', ['category' => $category->slug]),
                    'meta' => null,
                    'metaInfo' => ($category->articles_count ?? 0).' makale',
                    'image' => null,
                ];
            }),
        ];

        return response()->json($response);
    }

    private function performSearch(string $query): array
    {
        if ($query === '') {
            return [
                'articles' => collect(),
                'issues' => collect(),
                'authors' => collect(),
                'categories' => collect(),
                'total' => 0,
            ];
        }

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
            ->withCount(['articles' => fn ($q) => $q->where('is_published', true)])
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

        return [
            'articles' => $articles,
            'issues' => $issues,
            'authors' => $authors,
            'categories' => $categories,
            'total' => $articles->count() + $issues->count() + $authors->count() + $categories->count(),
        ];
    }
}
