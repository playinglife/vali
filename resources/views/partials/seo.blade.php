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

    $seoOgLocaleMap = [
        'en' => 'en_US',
        'ro' => 'ro_RO',
    ];
    $seoOgLocale = $seoOgLocaleMap[app()->getLocale()] ?? 'en_US';
    if ($seoOgImage === '') {
        $seoOgImage = $seoOrigin.'/images/og-default.jpg';
    } elseif (! str_starts_with($seoOgImage, 'http://') && ! str_starts_with($seoOgImage, 'https://')) {
        $seoOgImage = $seoOrigin.'/'.ltrim($seoOgImage, '/');
    } else {
        $seoOgImagePath = parse_url($seoOgImage, PHP_URL_PATH) ?: '';
        $seoOgImageQuery = parse_url($seoOgImage, PHP_URL_QUERY);
        $seoOgImage = $seoOrigin.$seoOgImagePath.($seoOgImageQuery ? '?'.$seoOgImageQuery : '');
    }

    $seoJsonLd = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => $seoOrigin.'/#organization',
                'name' => $seoSiteName,
                'url' => $seoOrigin,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $seoOrigin.'/images/logo.png',
                    'width' => 512,
                    'height' => 512,
                ],
            ],
            [
                '@type' => 'WebSite',
                '@id' => $seoOrigin.'/#website',
                'name' => $seoSiteName,
                'url' => $seoOrigin,
                'publisher' => [
                    '@id' => $seoOrigin.'/#organization',
                ],
            ],
        ],
    ];
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
        <meta property="og:type" content="{{ $seoOgType }}">
        <meta property="og:site_name" content="{{ $seoSiteName }}">
        <meta property="og:title" content="{{ $seoFullTitle }}">
        <meta property="og:locale" content="{{ $seoOgLocale }}">
@foreach ($seoOgLocaleMap as $seoOgLocaleCode => $seoOgLocaleValue)
@if ($seoOgLocaleValue !== $seoOgLocale)
        <meta property="og:locale:alternate" content="{{ $seoOgLocaleValue }}">
@endif
@endforeach
@if ($seoDescription !== '')
        <meta property="og:description" content="{{ $seoDescription }}">
@endif
@if ($seoCanonical !== '')
        <meta property="og:url" content="{{ $seoCanonical }}">
@endif
        <meta property="og:image" content="{{ $seoOgImage }}">
@if (str_ends_with($seoOgImage, '/images/og-default.jpg'))
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
@endif
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seoFullTitle }}">
@if ($seoDescription !== '')
        <meta name="twitter:description" content="{{ $seoDescription }}">
@endif
        <meta name="twitter:image" content="{{ $seoOgImage }}">
        <script type="application/ld+json">{!! json_encode($seoJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@stack('jsonld')
