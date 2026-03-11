<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WhatsappContactController extends Controller
{
    use LogsAdminAction;

    public function index(): Response
    {
        $contacts = WhatsappContact::orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/WhatsappKenalan/Index', [
            'contacts' => $contacts,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $contact = WhatsappContact::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'role' => ['required', 'string', 'max:200'],
            'wa_number' => ['nullable', 'string', 'max:50'],
            'wa_qr_id' => ['nullable', 'string', 'max:200'],
            'category' => ['required', 'in:kewangan,am,pendidikan,kebajikan'],
            'sort_order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $contact->update($validated);

        $this->logAction('updated_whatsapp_contact', 'WhatsappContact', $contact->id, "Kemaskini kenalan WhatsApp: {$contact->name}");

        return back()->with('success', 'Kenalan WhatsApp berjaya dikemaskini.');
    }
}
