<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        if (in_array($routeName, ['auth.blocked', 'auth.logout'], true)) {
            return $next($request);
        }

        $user = $request->user();

        if ($user && ! $user->is_active) {
            return redirect()->route('auth.blocked');
        }

        return $next($request);
    }
}
