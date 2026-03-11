<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\WhatsappContact;
use Inertia\Inertia;
use Inertia\Response;

class DermaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Public/Derma', [
            'donations' => Donation::active()->orderBy('sort_order')->get(),
            'whatsappContacts' => WhatsappContact::active()
                ->where('category', 'kewangan')
                ->get(),
        ]);
    }
}
