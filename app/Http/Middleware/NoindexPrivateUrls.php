<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoindexPrivateUrls
{
    public function handle(Request $request, Closure $next): Response
    {
        $noindex = $request->is(
            'up',
            'admin',
            'admin/*',
            '*/cart',
            '*/cart/*',
            '*/checkout',
            '*/checkout/*',
            '*/thankyou',
            '*/contact-success',
        );

        if ($noindex) {
            view()->share('seoRobots', 'noindex, nofollow');
        }

        $response = $next($request);

        if ($noindex) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}
