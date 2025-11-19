<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ArticlePageController extends Controller
{
    public function create(): View
    {
        return view('layouts.management', [
            'pageTitle' => 'Yeni Makale Oluştur',
        ]);
    }

    public function edit(): View
    {
        return view('layouts.management', [
            'pageTitle' => 'Makale Düzenle',
        ]);
    }
}
