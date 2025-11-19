<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MenuPage;
use Illuminate\View\View;

class MenuPageController extends Controller
{
    public function show(MenuPage $menuPage): View
    {
        abort_unless($menuPage->is_active, 404);

        return view('menu.show', [
            'page' => $menuPage,
        ]);
    }
}
