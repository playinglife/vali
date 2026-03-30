<?php

namespace App\Livewire;

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public bool $open = false;

    public ?string $label = null;

    public function mount(?string $label = null): void
    {
        $this->label = $label;
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function selectLocale(string $locale): mixed
    {
        $supported = array_keys(config('app.supported_locales', []));
        if (! in_array($locale, $supported, true)) {
            return null;
        }

        session()->put('locale', $locale);
        Cookie::queue(Cookie::forever(SetLocale::COOKIE_NAME, $locale));
        app()->setLocale($locale);
        $this->open = false;

        // During Livewire requests, `request()->fullUrl()` is the `/livewire-…/update`
        // endpoint, so redirecting there triggers a GET and "Method Not Allowed".
        return $this->redirect($this->redirectTargetAfterLocaleChange());
    }

    /**
     * Prefer the page the user was on (Referer / session), never the Livewire update URL.
     */
    private function redirectTargetAfterLocaleChange(): string
    {
        $target = url()->previous();
        $path = parse_url($target, PHP_URL_PATH) ?? '';

        if ($path !== '' && str_contains($path, '/livewire-') && str_ends_with(rtrim($path, '/'), '/update')) {
            return url('/');
        }

        return $target;
    }

    public function render()
    {
        $locales = SetLocale::supportedLocalesWithFlags();
        $current = app()->getLocale();

        return view('livewire.language-switcher', [
            'locales' => $locales,
            'current' => $current,
            'currentLocale' => Collection::make($locales)->firstWhere('code', $current),
        ]);
    }
}
