<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Issue;

class HomeController extends Controller
{
    /**
     * Ana sayfa
     */
    public function index()
    {
        $latestIssue = Issue::orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('created_at')
            ->first();

        $latestArticles = Article::query()
            ->with(['featureImage', 'category', 'author'])
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $popularArticles = Article::query()
            ->with(['featureImage', 'category', 'author'])
            ->where('is_published', true)
            ->when($latestArticles->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $latestArticles->pluck('id')))
            ->orderByDesc('view_count')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        if ($popularArticles->isEmpty()) {
            $popularArticles = $latestArticles;
        }

        $latestIssues = Issue::with('coverImage')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        return view('home', [
            'latestIssue' => $latestIssue,
            'latestArticles' => $latestArticles,
            'popularArticles' => $popularArticles,
            'latestIssues' => $latestIssues,
        ]);
    }
}
