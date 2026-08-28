<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePreferredOrigin
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->getPathInfo();
        $normalizedPath = ($path !== '/' && str_ends_with($path, '/'))
            ? (rtrim($path, '/') ?: '/')
            : $path;

        $preferred = parse_url((string) config('app.url'));
        $preferredHost = $preferred['host'] ?? null;
        $preferredScheme = $preferred['scheme'] ?? null;
        $preferredPort = isset($preferred['port']) ? (int) $preferred['port'] : null;

        $forceOrigin = app()->environment('production')
            && is_string($preferredHost)
            && $preferredHost !== ''
            && is_string($preferredScheme)
            && $preferredScheme !== '';

        $slashDiff = $normalizedPath !== $path;
        $schemeDiff = $forceOrigin && $request->getScheme() !== $preferredScheme;
        $hostDiff = $forceOrigin && strcasecmp($request->getHost(), $preferredHost) !== 0;

        if (! $slashDiff && ! $schemeDiff && ! $hostDiff) {
            return $next($request);
        }

        if ($forceOrigin) {
            $portPart = $preferredPort !== null ? ':'.$preferredPort : '';
            $target = $preferredScheme.'://'.$preferredHost.$portPart.$normalizedPath;
        } else {
            $target = $request->getSchemeAndHttpHost().$normalizedPath;
        }

        if ($query = $request->getQueryString()) {
            $target .= '?'.$query;
        }

        $status = in_array($request->method(), ['GET', 'HEAD'], true) ? 301 : 308;

        return redirect($target, $status);
    }
}
