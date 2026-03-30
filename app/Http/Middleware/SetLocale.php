<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const COOKIE_NAME = 'locale';

    /**
     * Relative to resources/ — SVG flags for the language switcher (blade-icons set "app", prefix "svg").
     */
    public const FLAGS_RESOURCE_SUBDIR = 'svg/flags';

    /**
     * Blade Icons name for {@see self::FLAGS_RESOURCE_SUBDIR}/{locale}.svg (e.g. svg-flags-en), or null if the file is missing.
     */
    public static function flagIconName(string $locale): ?string
    {
        $locale = strtolower($locale);
        if ($locale === '' || ! preg_match('/^[\w.-]+$/', $locale)) {
            return null;
        }

        $path = resource_path(self::FLAGS_RESOURCE_SUBDIR.DIRECTORY_SEPARATOR.$locale.'.svg');
        if (! is_file($path)) {
            return null;
        }

        return 'svg-flags.'.$locale;
    }

    /**
     * Supported locales from config with optional flag icon names for inline SVG (e.g. x-icon).
     *
     * @return list<array{code: string, name: string, icon: string|null}>
     */
    public static function supportedLocalesWithFlags(): array
    {
        $locales = config('app.supported_locales', []);
        $out = [];
        foreach ($locales as $code => $name) {
            $code = (string) $code;
            $out[] = [
                'code' => $code,
                'name' => (string) $name,
                'icon' => self::flagIconName($code),
            ];
        }

        return $out;
    }

    /**
     * Apply locale from cookie (user preference), then session, then Accept-Language,
     * then config default. Supported keys must match config('app.supported_locales').
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
            $locale = $this->localeFromAcceptLanguageHeader($request, $supported);
        }
        if (! is_string($locale) || ! in_array($locale, $supported, true)) {
            $locale = $default;
        }

        App::setLocale($locale);

        return $next($request);
    }

    /**
     * Parse Accept-Language (e.g. "en" or "en-US,en;q=0.9") and return a supported locale or null.
     */
    private function localeFromAcceptLanguageHeader(Request $request, array $supportedLocales): ?string
    {
        $locale = $request->header('Accept-Language', config('app.locale'));

        $locale = explode(',', $locale)[0] ?? $locale;
        $locale = explode('-', trim($locale))[0] ?? $locale;
        $locale = strtolower(trim($locale));

        if (in_array($locale, $supportedLocales, true)) {
            return $locale;
        }

        return null;
    }
}
