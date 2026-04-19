<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ── Aliases de middleware (utilisés dans routes/web.php) ──
        $middleware->alias([
            'role'         => \App\Http\Middleware\RoleMiddleware::class,
            'salon.valide' => \App\Http\Middleware\SalonValideMiddleware::class,
            'verified'     => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);

        // Railway / reverse proxy: faire confiance aux en-têtes X-Forwarded-*
        // afin que Laravel détecte le vrai scheme https et reconstruise
        // correctement l'URL pour la validation de signature des emails.
        $middleware->trustProxies(at: '*', headers:
            \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Redirection 403 → page d'accueil avec message
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                return redirect()->route('home')
                    ->with('error', 'Accès non autorisé.');
            }
        });

        // Redirection 404 → page d'accueil avec message
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            return redirect()->route('home')
                ->with('error', 'Page introuvable.');
        });
    })
    ->create();
