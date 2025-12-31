<?php

namespace App\Http\Middleware;

use Closure;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $availableLanguages = array_keys(config('panel.available_languages', []));
        $primaryLanguage = config('panel.primary_language', 'en');

        if (request('change_language')) {
            $requestedLanguage = request('change_language');
            // Validate against whitelist of available languages
            if (in_array($requestedLanguage, $availableLanguages)) {
                session()->put('language', $requestedLanguage);
                $language = $requestedLanguage;
            } else {
                // Invalid language requested, use primary language
                $language = $primaryLanguage;
            }
        } elseif (session('language')) {
            $sessionLanguage = session('language');
            // Validate session language against whitelist
            if (in_array($sessionLanguage, $availableLanguages)) {
                $language = $sessionLanguage;
            } else {
                $language = $primaryLanguage;
            }
        } elseif ($primaryLanguage) {
            $language = $primaryLanguage;
        }

        if (isset($language)) {
            app()->setLocale($language);
        }

        return $next($request);
    }
}
