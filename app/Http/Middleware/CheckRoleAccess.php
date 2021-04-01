<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckRoleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string[] $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        /** @var  \App\Models\User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $next($request); // Admin has full access
        }

        if (!in_array($user->role, $roles)) {
            throw new AccessDeniedHttpException('Access denied.');
        }

        return $next($request);
    }
}
