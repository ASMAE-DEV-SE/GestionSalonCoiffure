<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /*
    |------------------------------------------------------------------
    | Liste des notifications  GET /client/notifications
    |------------------------------------------------------------------
    */
    public function index(): View
    {
        $notifications = Auth::user()
            ->notifications()
            ->orderBy('cree_le', 'desc')
            ->paginate(20);

        // Marquer toutes comme lues lors de la consultation
        Auth::user()
            ->notificationsNonLues()
            ->update(['lu_le' => now()]);

        return view('client.notifications', compact('notifications'));
    }

    /*
    |------------------------------------------------------------------
    | Marquer une notification comme lue  POST /client/notifications/{id}/lu
    |------------------------------------------------------------------
    */
    public function marquerLu(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->marquerLue();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }

    /*
    |------------------------------------------------------------------
    | Compteur non lus (appelé en AJAX pour la cloche navbar)
    |------------------------------------------------------------------
    */
    public function count(): JsonResponse
    {
        $count = Auth::user()->notificationsNonLues()->count();
        return response()->json(['count' => $count]);
    }
}
