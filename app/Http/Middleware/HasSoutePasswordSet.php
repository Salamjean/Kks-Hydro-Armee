<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Ajout de cet import
use Symfony\Component\HttpFoundation\Response;


class HasSoutePasswordSet
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifie si l'utilisateur est connecté via le guard 'personnel_soute'
        if (Auth::guard('personnel_soute')->check()) {
            $personnel = Auth::guard('personnel_soute')->user();

            // Si le mot de passe est null et que l'utilisateur n'est pas déjà
            // sur la page pour définir son mot de passe
            if ($personnel->password === null &&
                !$request->routeIs('soute.dashboard.set.password') &&
                !$request->routeIs('soute.dashboard.handleSet.password')) {
                return redirect()->route('soute.dashboard.set.password');
            }
        }
        // Si l'utilisateur n'est pas connecté via ce guard ou si son mot de passe est déjà défini,
        // on ne fait rien de spécial ici, le middleware 'auth:personnel_soute' gérera
        // la redirection vers la page de login si non authentifié.
        return $next($request);
    }
}