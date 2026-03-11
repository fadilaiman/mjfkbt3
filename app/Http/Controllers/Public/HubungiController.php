<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\WhatsappContact;
use Inertia\Inertia;
use Inertia\Response;

class HubungiController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Public/Hubungi', [
            'whatsappContacts' => WhatsappContact::active()->get(),
            'mosque' => config('mjfkbt3.mosque'),
        ]);
    }
}
