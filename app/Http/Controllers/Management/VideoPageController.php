<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class VideoPageController extends Controller
{
    public function create(): View
    {
        return view('layouts.management', [
            'pageTitle' => 'Yeni Video Ekle',
        ]);
    }

    public function edit(): View
    {
        return view('layouts.management', [
            'pageTitle' => 'Video Düzenle',
        ]);
    }
}
