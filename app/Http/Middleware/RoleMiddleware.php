<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware
 *
 * Vérifie que l'utilisateur authentifié possède l'un des rôles
 * autorisés. Utilisé dans routes/web.php :
 *   Route::middleware(['auth', 'role:salon'])
 *   Route::middleware(['auth', 'role:admin'])
 *   Route::middleware(['auth', 'role:admin,salon'])
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // L'utilisateur doit être connecté (auth middleware en amont)
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Accepte plusieurs rôles séparés par virgule : 'role:admin,salon'
        if (! in_array($user->role, $roles)) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}
