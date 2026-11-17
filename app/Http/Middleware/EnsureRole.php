<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** Pemakaian: ->middleware(EnsureRole::class.':admin,staff') */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();
        abort_unless($user && in_array($user->role, $roles, true), 403, 'Akses ditolak.');
        return $next($request);
    }
}
