<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class StaticPageController extends Controller
{
    public function dasarPrivasi(): Response
    {
        return Inertia::render('Public/DasarPrivasi');
    }

    public function termaPenggunaan(): Response
    {
        return Inertia::render('Public/TermaPenggunaan');
    }
}
