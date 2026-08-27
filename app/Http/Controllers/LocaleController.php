<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $supported = SetLocale::supportedLocaleCodes();

        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in($supported)],
        ]);

        $locale = $validated['locale'];

        $request->session()->put('locale', $locale);

        $refererPath = parse_url((string) $request->headers->get('referer'), PHP_URL_PATH);
        $target = SetLocale::localizedPath($locale, is_string($refererPath) ? $refererPath : '/');

        return redirect($target)->withCookie(Cookie::forever(SetLocale::COOKIE_NAME, $locale));
    }
}
