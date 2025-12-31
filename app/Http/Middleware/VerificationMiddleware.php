<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Gate;
use App\Models\Role;
use Closure;

class VerificationMiddleware
{
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            if (! auth()->user()->verified) {
                auth()->logout();

                return redirect()->route('login')->with('message', trans('global.verifyYourEmail'));
            }
        }

        return $next($request);
    }
}
