<?php

namespace App\Http\Middleware;

use App\Domain\IdentityAndAccess\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $userRole = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        if ($userRole === UserRole::Admin->value) {
            return $next($request);
        }

        if (!in_array($userRole, $roles, true)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
