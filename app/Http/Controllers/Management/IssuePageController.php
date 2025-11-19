<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class IssuePageController extends Controller
{
    public function create(): View
    {
        return view('layouts.management', [
            'pageTitle' => 'Yeni Sayı Oluştur',
        ]);
    }

    public function edit(): View
    {
        return view('layouts.management', [
            'pageTitle' => 'Sayı Düzenle',
        ]);
    }
}
