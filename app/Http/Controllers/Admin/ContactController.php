<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        Log::info('Admin: messages contact consultes', ['admin_id' => Auth::id()]);

        $messages = ContactMessage::latest()->paginate(20);

        ContactMessage::where('lu', false)->update(['lu' => true]);

        return view('admin.contact_messages', compact('messages'));
    }

    public function destroy(int $id): RedirectResponse
    {
        ContactMessage::findOrFail($id)->delete();

        Log::info('Admin: message contact supprime', ['admin_id' => Auth::id(), 'message_id' => $id]);

        return back()->with('success', 'Message supprimé.');
    }
}
