<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\MenuPage;
use App\Models\SiteSetting;
use App\Repositories\ArticleRepository;
use App\Repositories\AuthorRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\IssueRepository;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Repositories\Interfaces\AuthorRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\IssueRepositoryInterface;
use App\Repositories\Interfaces\MediaAssetRepositoryInterface;
use App\Repositories\Interfaces\MenuPageRepositoryInterface;
use App\Repositories\Interfaces\SiteSettingRepositoryInterface;
use App\Repositories\MediaAssetRepository;
use App\Repositories\MenuPageRepository;
use App\Repositories\SiteSettingRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(ArticleRepositoryInterface::class, ArticleRepository::class);
        $this->app->bind(AuthorRepositoryInterface::class, AuthorRepository::class);
        $this->app->bind(MediaAssetRepositoryInterface::class, MediaAssetRepository::class);
        $this->app->bind(IssueRepositoryInterface::class, IssueRepository::class);
        $this->app->bind(MenuPageRepositoryInterface::class, MenuPageRepository::class);
        $this->app->bind(SiteSettingRepositoryInterface::class, SiteSettingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (function_exists('ini_set')) {
            @ini_set('upload_max_filesize', '50M');
            @ini_set('post_max_size', '60M');
        }

        $locale = config('app.locale', 'tr');
        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);
        setlocale(LC_TIME, 'tr_TR.UTF-8', 'tr_TR', 'turkish');

        Vite::prefetch(concurrency: 3);

        View::composer(['components.layout.navigation', 'components.layout.footer'], function ($view) {
            $categories = Category::query()
                ->where('is_active', true)
                ->withCount(['articles' => function ($query) {
                    $query->where('is_published', true);
                }])
                ->orderBy('name')
                ->get();

            $menuPages = MenuPage::query()
                ->where('is_active', true)
                ->orderBy('position')
                ->orderBy('title')
                ->get();

            $siteSettings = SiteSetting::query()->first();

            $view->with('navCategories', $categories)
                ->with('navMenuPages', $menuPages)
                ->with('siteSettings', $siteSettings);
        });
    }
}
