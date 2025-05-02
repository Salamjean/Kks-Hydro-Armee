<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request; // Assure-toi que Request est importé

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request): ?string // Utilise le type hinting de retour si tu es en PHP 7.4+
    {
        if (! $request->expectsJson()) {
            // Si la route demandée commence par 'admin/'
            if ($request->routeIs('admin.*')) {
                return route('admin.login'); // Redirige vers la page de login admin
            }

            // Si la route demandée commence par 'corps/' (ou est liée au guard corps)
            if ($request->routeIs('corps.*')) {
                 return route('corps.login'); // Redirige vers la page de login corps
            }

            // Optionnel: Une route de login par défaut si aucun préfixe ne correspond
            // return route('login'); // Si tu avais une route nommée 'login' générique
            // Ou redirige vers une page d'accueil publique
             return route('welcome'); // Ou une autre route appropriée

        }
        // Si la requête attend du JSON (API), on ne redirige pas,
        // Laravel renverra une réponse 401 Unauthorized.
        return null;
    }
}