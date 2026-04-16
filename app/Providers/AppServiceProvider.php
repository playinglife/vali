<?php

namespace App\Providers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(UrlGenerator $url): void
    {
        if (app()->environment('production') && request()->isSecure()) {
            $url->forceScheme('https');
        }
        // OR better
        /*if (env('FORCE_HTTPS')) {
            $url->forceScheme('https');
        }*/

        RedirectResponse::macro('withNotify', function (string $type, string $message) {
            if ($type === 'information') {
                $type = 'info';
            }
            $allowed = ['success', 'warning', 'error', 'info'];
            if (! in_array($type, $allowed, true)) {
                $type = 'info';
            }

            return $this->with('notify', [
                'type' => $type,
                'message' => $message,
            ]);
        });
    }
}
