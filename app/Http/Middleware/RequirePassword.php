<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RequirePassword
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var  \App\Models\User $user */
        $user = Auth::user();

        if ($user === null || $request->input('password') === null) {
            throw new AccessDeniedHttpException('Missing password.');
        }

        if (!Hash::check($request->input('password'), $user->password)) {
            throw new AccessDeniedHttpException('Bad password.');
        }

        return $next($request);
    }
}
