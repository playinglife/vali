@php
    $seoTitle = trim((string) ($seoTitle ?? $__env->yieldContent('title')));
    $seoDescription = trim((string) ($seoDescription ?? $__env->yieldContent('description')));
    $seoCanonical = trim((string) ($seoCanonical ?? $__env->yieldContent('canonical')));
    $seoRobots = trim((string) ($seoRobots ?? $__env->yieldContent('robots')));
    $seoOgImage = trim((string) ($seoOgImage ?? $__env->yieldContent('og_image')));
    $seoOgType = trim((string) ($seoOgType ?? $__env->yieldContent('og_type')));
    $seoSiteName = config('app.name');
    $seoTitleFull = trim((string) ($seoTitleFull ?? $__env->yieldContent('title_full')));
    if ($seoTitleFull !== '') {
        $seoFullTitle = $seoTitleFull;
    } elseif ($seoTitle !== '') {
        $seoFullTitle = $seoTitle.' | '.$seoSiteName;
    } else {
        $seoFullTitle = $seoSiteName;
    }

    if ($seoRobots === '') {
        $seoRobots = 'index, follow';
    }
    if ($seoOgType === '') {
        $seoOgType = 'website';
    }

    $seoOrigin = rtrim((string) config('app.url'), '/');
    $seoHreflang = [];
    $seoPath = trim(request()->path(), '/');
    $seoSegments = $seoPath === '' ? [] : explode('/', $seoPath);
    $seoLocales = \App\Http\Middleware\SetLocale::supportedLocaleCodes();
    if ($seoSegments !== [] && in_array($seoSegments[0], $seoLocales, true)) {
        $seoRest = implode('/', array_slice($seoSegments, 1));
        foreach ($seoLocales as $seoLocale) {
            $seoHreflang[$seoLocale] = $seoOrigin.'/'.$seoLocale.($seoRest !== '' ? '/'.$seoRest : '');
        }
        $seoDefaultLocale = (string) config('app.locale');
        if (isset($seoHreflang[$seoDefaultLocale])) {
            $seoHreflang['x-default'] = $seoHreflang[$seoDefaultLocale];
        }
    }
    if ($seoCanonical === '') {
        $seoCanonical = $seoPath === '' ? $seoOrigin : $seoOrigin.'/'.$seoPath;
    }

    $seoHasOptional = $seoDescription !== '' || $seoCanonical !== '' || $seoOgImage !== '';
@endphp
        <title>{{ $seoFullTitle }}</title>
        <meta name="robots" content="{{ $seoRobots }}">
@foreach ($seoHreflang as $seoHreflangCode => $seoHreflangHref)
        <link rel="alternate" hreflang="{{ $seoHreflangCode }}" href="{{ $seoHreflangHref }}">
@endforeach
@if ($seoDescription !== '')
        <meta name="description" content="{{ $seoDescription }}">
@endif
@if ($seoCanonical !== '')
        <link rel="canonical" href="{{ $seoCanonical }}">
@endif
@if ($seoHasOptional)
        <meta property="og:type" content="{{ $seoOgType }}">
        <meta property="og:site_name" content="{{ $seoSiteName }}">
        <meta property="og:title" content="{{ $seoFullTitle }}">
@if ($seoDescription !== '')
        <meta property="og:description" content="{{ $seoDescription }}">
@endif
@if ($seoCanonical !== '')
        <meta property="og:url" content="{{ $seoCanonical }}">
@endif
@if ($seoOgImage !== '')
        <meta property="og:image" content="{{ $seoOgImage }}">
@endif
        <meta name="twitter:card" content="{{ $seoOgImage !== '' ? 'summary_large_image' : 'summary' }}">
        <meta name="twitter:title" content="{{ $seoFullTitle }}">
@if ($seoDescription !== '')
        <meta name="twitter:description" content="{{ $seoDescription }}">
@endif
@if ($seoOgImage !== '')
        <meta name="twitter:image" content="{{ $seoOgImage }}">
@endif
@endif
@stack('jsonld')
