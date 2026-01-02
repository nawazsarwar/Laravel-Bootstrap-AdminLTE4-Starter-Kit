<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Closure;

class TwoFactorMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->two_factor_code) {
            try {
                $expiresAt = Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $user->two_factor_expires_at);
            } catch (\Exception $e) {
                // Fallback to Carbon::parse() if format doesn't match exactly
                try {
                    $expiresAt = Carbon::parse($user->two_factor_expires_at);
                } catch (\Exception $e2) {
                    // If parsing fails completely, treat as expired
                    $user->resetTwoFactorCode();
                    auth()->logout();
                    return redirect()->route('login')->with('message', __('global.two_factor.expired'));
                }
            }
            
            if ($expiresAt->lt(now())) {
                $user->resetTwoFactorCode();
                auth()->logout();

                return redirect()->route('login')->with('message', __('global.two_factor.expired'));
            }

            if (! $request->is('two-factor*')) {
                return redirect()->route('twoFactor.show');
            }
        }

        return $next($request);
    }
}
