<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('layouts.management', [
            'pageTitle' => 'Yönetim Paneli',
        ]);
    }
}
