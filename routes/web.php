<?php

use App\Http\Controllers\Management\ArticleController;
use App\Http\Controllers\Management\ArticlePageController;
use App\Http\Controllers\Management\AuthorController;
use App\Http\Controllers\Management\CategoryController;
use App\Http\Controllers\Management\DashboardController;
use App\Http\Controllers\Management\ContactMessageController as ManagementContactMessageController;
use App\Http\Controllers\Management\IssueController as ManagementIssueApiController;
use App\Http\Controllers\Management\IssuePageController;
use App\Http\Controllers\Management\MenuPageController as ManagementMenuPageController;
use App\Http\Controllers\Management\SiteSettingController as ManagementSiteSettingController;
use App\Http\Controllers\Management\MediaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\ArticleController as WebArticleController;
use App\Http\Controllers\Web\AuthorController as WebAuthorController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\IssueController as WebIssueController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\ContactMessageController as WebContactMessageController;
use App\Http\Controllers\Web\SearchController;
use App\Http\Controllers\Web\MenuPageController as WebMenuPageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('tr/yazilar')->group(function () {
    Route::get('/', [WebArticleController::class, 'index'])->name('articles.index');
    Route::get('/yazar/{author:slug}', [WebArticleController::class, 'index'])->name('articles.author');
    Route::get('/{category:slug}/{author:slug?}', [WebArticleController::class, 'index'])
        ->name('articles.category')
        ->withoutScopedBindings();
});

Route::get('/yazilar/{article:slug}', [WebArticleController::class, 'show'])->name('articles.show');

Route::get('/yazarlar', [WebAuthorController::class, 'index'])->name('authors.index');

Route::get('/sayilar', [WebIssueController::class, 'index'])->name('issues.index');
Route::get('/sayilar/{issue:slug}', [WebIssueController::class, 'show'])->name('issues.show');

Route::get('/tr/menu/{menuPage:slug}', [WebMenuPageController::class, 'show'])->name('menu.show');
Route::get('/tr/iletisim', ContactController::class)->name('contact.show');
Route::post('/tr/iletisim', [WebContactMessageController::class, 'store'])->name('contact.store');
Route::get('/ara', [SearchController::class, 'index'])->name('search.index');
Route::get('/api/search', [SearchController::class, 'api'])->name('search.api');

Route::get('/dashboard', function () {
    return redirect()->route('management.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])
    ->prefix('management')
    ->name('management.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/articles/create', [ArticlePageController::class, 'create'])->name('articles.create');
        Route::get('/articles/{article}/edit', [ArticlePageController::class, 'edit'])->name('articles.edit');
        Route::get('/issues/create', [IssuePageController::class, 'create'])->name('issues.create');
        Route::get('/issues/{issue}/edit', [IssuePageController::class, 'edit'])->name('issues.edit');

        Route::prefix('api')
            ->name('api.')
            ->group(function () {
                Route::apiResource('categories', CategoryController::class)->except(['create', 'edit']);
                Route::apiResource('authors', AuthorController::class)->except(['create', 'edit']);
                Route::apiResource('articles', ArticleController::class)->except(['create', 'edit']);
                Route::apiResource('issues', ManagementIssueApiController::class)->except(['create', 'edit']);
                Route::apiResource('menu-pages', ManagementMenuPageController::class)->except(['create', 'edit']);
                Route::get('media', [MediaController::class, 'index'])->name('media.index');
                Route::post('media', [MediaController::class, 'store'])->name('media.store');
                Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
                Route::get('site-settings', [ManagementSiteSettingController::class, 'show'])->name('site-settings.show');
                Route::put('site-settings', [ManagementSiteSettingController::class, 'update'])->name('site-settings.update');
                Route::apiResource('contact-messages', ManagementContactMessageController::class)->only(['index', 'show', 'update', 'destroy']);
            });

        Route::get('{any}', DashboardController::class)
            ->where('any', '^(?!api).*')
            ->name('spa');
    });

require __DIR__.'/auth.php';
