<?php

declare(strict_types=1);

namespace Drivkraft\Valkyrie\Http\Middleware;

use Closure;
use Drivkraft\Valkyrie\Facades\Valkyrie;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Passes when the authenticated user holds any of the given roles through a global
 * assignment. A trailing `any` parameter (`role:admin,any`) also accepts assignments
 * in any scope.
 */
final class EnsureHasRole
{
    /**
     * @param  Closure(Request): Response  $next
     *
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user instanceof Model) {
            throw new AuthenticationException;
        }

        $anyScope = end($roles) === self::AnyScope;

        if ($anyScope) {
            array_pop($roles);
        }

        foreach ($roles as $role) {
            if ($anyScope ? Valkyrie::hasRole($user, $role) : Valkyrie::hasGlobalRole($user, $role)) {
                return $next($request);
            }
        }

        throw new AuthorizationException;
    }

    public const string AnyScope = 'any';
}
