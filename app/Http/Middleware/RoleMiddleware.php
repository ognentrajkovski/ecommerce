<?php

namespace App\Http\Middleware;

use App\Domain\IdentityAndAccess\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $userRole = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        if ($userRole !== $role) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
