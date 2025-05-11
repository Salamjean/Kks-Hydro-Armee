<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('active_class_by_pattern')) {
    function active_class_by_pattern(array|string $patterns, string $className = 'active'): string
    {
        $patterns = (array) $patterns;

        foreach ($patterns as $pattern) {
            if (request()->is($pattern)) {
                return $className;
            }
        }

        return '';
    }
}

if (!function_exists('is_active_route')) {
    function is_active_route($route)
    {
        return Route::currentRouteName() === $route ? 'active' : '';
    }
}

if (!function_exists('active_class')) {
    function active_class($routes = [])
    {
        return in_array(Route::currentRouteName(), $routes) ? 'active' : '';
    }
}

if (!function_exists('show_class')) {
    function show_class($routes = [])
    {
        return in_array(Route::currentRouteName(), $routes) ? 'show' : '';
    }
}
