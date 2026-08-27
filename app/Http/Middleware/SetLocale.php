<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const COOKIE_NAME = 'locale';

    /**
     * Relative to resources/ — SVG flags for the language switcher (blade-icons set "app", prefix "svg").
     */
    public const FLAGS_RESOURCE_SUBDIR = 'svg/flags';

    /**
     * @return list<string>
     */
    public static function supportedLocaleCodes(): array
    {
        return array_keys(config('app.supported_locales', []));
    }

    public static function isSupportedLocale(?string $locale): bool
    {
        return is_string($locale) && $locale !== '' && in_array($locale, self::supportedLocaleCodes(), true);
    }

    /**
     * Cookie → session → Accept-Language → config default.
     */
    public static function detectLocale(Request $request): string
    {
        $supported = self::supportedLocaleCodes();
        $default = (string) config('app.locale');

        $locale = $request->cookie(self::COOKIE_NAME);
        if (self::isSupportedLocale(is_string($locale) ? $locale : null)) {
            return $locale;
        }

        if ($request->hasSession()) {
            $locale = $request->session()->get('locale');
            if (self::isSupportedLocale(is_string($locale) ? $locale : null)) {
                return $locale;
            }
        }

        $locale = self::localeFromAcceptLanguageHeader($request, $supported);
        if (self::isSupportedLocale($locale)) {
            return $locale;
        }

        return self::isSupportedLocale($default) ? $default : ($supported[0] ?? 'en');
    }

    /**
     * Same path in another locale, e.g. /en/products/foo → /ro/products/foo.
     */
    public static function localizedPath(string $locale, ?string $path = null): string
    {
        $path = trim($path ?? request()->path(), '/');
        $segments = $path === '' || $path === '/' ? [] : explode('/', $path);
        $codes = self::supportedLocaleCodes();

        if ($segments !== [] && in_array($segments[0], $codes, true)) {
            array_shift($segments);
        }

        if (($segments[0] ?? '') === 'admin') {
            return '/'.$locale;
        }

        $rest = implode('/', $segments);

        return '/'.$locale.($rest !== '' ? '/'.$rest : '');
    }

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
     * Apply locale from the URL prefix, then cookie, session, Accept-Language, then config default.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');
        if (! self::isSupportedLocale(is_string($locale) ? $locale : null)) {
            $locale = self::detectLocale($request);
        }

        App::setLocale($locale);
        URL::defaults(['locale' => $locale]);

        if ($request->hasSession() && $request->session()->get('locale') !== $locale) {
            $request->session()->put('locale', $locale);
        }
        if ($request->cookie(self::COOKIE_NAME) !== $locale) {
            Cookie::queue(Cookie::forever(self::COOKIE_NAME, $locale));
        }

        return $next($request);
    }

    /**
     * Parse Accept-Language (e.g. "en" or "en-US,en;q=0.9") and return a supported locale or null.
     *
     * @param  list<string>  $supportedLocales
     */
    public static function localeFromAcceptLanguageHeader(Request $request, array $supportedLocales): ?string
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
