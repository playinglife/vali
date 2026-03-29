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
        $supported = array_keys(config('app.supported_locales', []));

        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in($supported)],
        ]);

        $locale = $validated['locale'];

        $request->session()->put('locale', $locale);

        $response = redirect()->back();

        // Persist across browser sessions (optional; session alone resets when session expires)
        return $response->withCookie(Cookie::forever(SetLocale::COOKIE_NAME, $locale));
    }
}
