<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const COOKIE_NAME = 'locale';

    /**
     * Apply locale from cookie (persistent), then session, then config default.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('app.supported_locales', []));
        $default = config('app.locale');

        $locale = $request->cookie(self::COOKIE_NAME);
        if (! is_string($locale) || ! in_array($locale, $supported, true)) {
            $locale = $request->session()->get('locale');
        }
        if (! is_string($locale) || ! in_array($locale, $supported, true)) {
            $locale = $default;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
