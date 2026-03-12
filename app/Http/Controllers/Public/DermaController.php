<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\WhatsappContact;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;

class DermaController extends Controller
{
    public function index(): Response
    {
        $donations = Donation::active()->orderBy('sort_order')->get();

        $queryUrl = config('mjfkbt3.toyyibpay.query_url');
        if ($queryUrl && $donations->isNotEmpty()) {
            try {
                $response = Http::timeout(5)->get($queryUrl);
                if ($response->successful()) {
                    $amount = $response->json('billpaymentAmountNett');
                    if ($amount !== null) {
                        $donations[0]->collected_amount = $amount;
                    }
                }
            } catch (\Exception $e) {
                // fall back to DB value silently
            }
        }

        return Inertia::render('Public/Derma', [
            'donations' => $donations,
            'whatsappContacts' => WhatsappContact::active()
                ->where('category', 'kewangan')
                ->get(),
        ]);
    }
}
