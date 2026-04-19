<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        $messages = ContactMessage::latest()->paginate(20);

        // Marquer tous les non-lus comme lus
        ContactMessage::where('lu', false)->update(['lu' => true]);

        return view('admin.contact_messages', compact('messages'));
    }

    public function destroy(int $id): RedirectResponse
    {
        ContactMessage::findOrFail($id)->delete();

        return back()->with('success', 'Message supprimé.');
    }
}
