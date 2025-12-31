<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class AuthGates
{
    /**
     * Cache key for permissions array.
     */
    const CACHE_KEY = 'auth_gates_permissions';

    /**
     * Cache duration in seconds (24 hours).
     */
    const CACHE_DURATION = 86400;

    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (! $user) {
            return $next($request);
        }

        // Cache permissions to avoid loading on every request
        $permissionsArray = Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            $roles = Role::with('permissions')->get();
            $permissionsArray = [];

            foreach ($roles as $role) {
                foreach ($role->permissions as $permissions) {
                    $permissionsArray[$permissions->title][] = $role->id;
                }
            }

            return $permissionsArray;
        });

        foreach ($permissionsArray as $title => $roles) {
            Gate::define($title, function ($user) use ($roles) {
                return count(array_intersect($user->roles->pluck('id')->toArray(), $roles)) > 0;
            });
        }

        return $next($request);
    }
}
