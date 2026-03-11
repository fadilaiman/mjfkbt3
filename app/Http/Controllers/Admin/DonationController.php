<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateDonationRequest;
use App\Models\Donation;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DonationController extends Controller
{
    use LogsAdminAction;

    public function index(): Response
    {
        $donations = Donation::orderByDesc('is_active')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/Derma/Index', [
            'donations' => $donations,
        ]);
    }

    public function edit(int $id): Response
    {
        $donation = Donation::findOrFail($id);

        return Inertia::render('Admin/Derma/Edit', [
            'donation' => $donation,
        ]);
    }

    public function update(UpdateDonationRequest $request, int $id): RedirectResponse
    {
        $donation = Donation::findOrFail($id);

        $donation->update($request->validated());

        $this->logAction('updated_donation', 'Donation', $donation->id, "Kemaskini derma: {$donation->name}");

        return redirect()->route('admin.derma.index')
            ->with('success', 'Tabung derma berjaya dikemaskini.');
    }
}
