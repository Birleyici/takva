<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Author;
use Illuminate\View\View;

class AuthorController extends Controller
{
    public function index(): View
    {
        $authors = Author::query()
            ->with('profileImage')
            ->withCount(['articles' => function ($query) {
                $query->where('is_published', true);
            }])
            ->orderByDesc('articles_count')
            ->paginate(12);

        return view('yazarlar.index', [
            'authors' => $authors,
        ]);
    }

}
