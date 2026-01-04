<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\HeroSlide;
use App\Models\Issue;
use App\Models\SiteSetting;
use App\Models\Video;

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
            ->leftJoin('issues', 'articles.issue_id', '=', 'issues.id')
            ->where('articles.is_published', true)
            ->orderByDesc('issues.number')
            ->orderByDesc('issues.year')
            ->orderByDesc('issues.month')
            ->orderByDesc('articles.published_at')
            ->orderByDesc('articles.created_at')
            ->select('articles.*')
            ->take(6)
            ->get();

        $popularArticles = Article::query()
            ->with(['featureImage', 'category', 'author'])
            ->leftJoin('issues', 'articles.issue_id', '=', 'issues.id')
            ->where('articles.is_published', true)
            ->when($latestArticles->isNotEmpty(), fn ($query) => $query->whereNotIn('articles.id', $latestArticles->pluck('id')))
            ->orderByDesc('view_count')
            ->orderByDesc('issues.number')
            ->orderByDesc('issues.year')
            ->orderByDesc('issues.month')
            ->orderByDesc('articles.published_at')
            ->orderByDesc('articles.created_at')
            ->select('articles.*')
            ->take(3)
            ->get();

        if ($popularArticles->isEmpty()) {
            $popularArticles = $latestArticles;
        }

        $latestIssues = Issue::with('coverImage')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        $latestVideos = Video::query()
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $heroSlides = HeroSlide::query()
            ->with('image')
            ->where('is_active', true)
            ->whereNotNull('image_id')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get()
            ->filter(fn (HeroSlide $slide) => (bool) $slide->image_url)
            ->map(function (HeroSlide $slide) {
                return [
                    'id' => $slide->id,
                    'image_url' => $slide->image_url,
                    'date_label' => $slide->display_date_label,
                    'link_url' => $slide->link_url,
                ];
            })
            ->values();

        $siteSettings = SiteSetting::query()->first();

        return view('home', [
            'latestIssue' => $latestIssue,
            'latestArticles' => $latestArticles,
            'popularArticles' => $popularArticles,
            'latestIssues' => $latestIssues,
            'latestVideos' => $latestVideos,
            'heroSlides' => $heroSlides,
            'navMode' => $heroSlides->isNotEmpty() ? 'solid' : 'overlay',
            'siteSettings' => $siteSettings,
        ]);
    }
}
