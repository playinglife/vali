<?php

test('the application redirects to a localized home', function () {
    $response = $this->get('/');

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toMatch('#/(en|ro)$#');
});

test('indexable pages emit a query-free canonical from APP_URL', function () {
    $origin = rtrim((string) config('app.url'), '/');

    $this->get('/en')->assertOk()->assertSee(
        '<link rel="canonical" href="'.$origin.'/en">',
        false,
    );

    $this->get('/en?utm_source=test')->assertOk()
        ->assertSee('<link rel="canonical" href="'.$origin.'/en">', false)
        ->assertDontSee('<link rel="canonical" href="'.$origin.'/en?', false);
});

test('pages emit open graph and twitter card tags', function () {
    $origin = rtrim((string) config('app.url'), '/');

    $this->get('/en')->assertOk()
        ->assertSee('<meta property="og:type" content="website">', false)
        ->assertSee('<meta property="og:locale" content="en_US">', false)
        ->assertSee('<meta property="og:locale:alternate" content="ro_RO">', false)
        ->assertSee('<meta property="og:image" content="'.$origin.'/images/og-default.jpg">', false)
        ->assertSee('<meta property="og:image:width" content="1200">', false)
        ->assertSee('<meta property="og:image:height" content="630">', false)
        ->assertSee('<meta name="twitter:card" content="summary_large_image">', false);

    $this->get('/ro')->assertOk()
        ->assertSee('<meta property="og:locale" content="ro_RO">', false)
        ->assertSee('<meta property="og:locale:alternate" content="en_US">', false);
});

test('layouts emit favicon and apple-touch-icon links', function () {
    $origin = rtrim((string) config('app.url'), '/');

    $this->get('/en')->assertOk()
        ->assertSee('<link rel="icon" href="'.$origin.'/favicon.ico" sizes="any">', false)
        ->assertSee('<link rel="icon" href="'.$origin.'/favicon.svg" type="image/svg+xml">', false)
        ->assertSee('<link rel="icon" href="'.$origin.'/favicon-32x32.png" type="image/png" sizes="32x32">', false)
        ->assertSee('<link rel="apple-touch-icon" href="'.$origin.'/apple-touch-icon.png" sizes="180x180">', false);
});

test('sitemap lists indexable localized urls and skips private paths', function () {
    $origin = rtrim((string) config('app.url'), '/');

    $this->get('/sitemap.xml')->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee('<loc>'.$origin.'/en</loc>', false)
        ->assertSee('<loc>'.$origin.'/ro/products</loc>', false)
        ->assertSee('<loc>'.$origin.'/en/aboutus</loc>', false)
        ->assertSee('<loc>'.$origin.'/en/custom</loc>', false)
        ->assertSee('<loc>'.$origin.'/en/size-chart</loc>', false)
        ->assertSee('<loc>'.$origin.'/en/contact</loc>', false)
        ->assertSee('<loc>'.$origin.'/en/privacy-policy</loc>', false)
        ->assertSee('<loc>'.$origin.'/ro/terms</loc>', false)
        ->assertDontSee('/en/cart', false)
        ->assertDontSee('/admin', false)
        ->assertDontSee('/en/teachers', false)
        ->assertDontSee('/en/about</loc>', false);
});

test('robots txt points at the sitemap and disallows private urls', function () {
    $origin = rtrim((string) config('app.url'), '/');

    $this->get('/robots.txt')->assertOk()
        ->assertSee("User-agent: *\nDisallow: /admin", false)
        ->assertSee('Disallow: /en/cart', false)
        ->assertSee('Disallow: /ro/checkout', false)
        ->assertSee('Sitemap: '.$origin.'/sitemap.xml', false);
});

test('clef play leftover pages are removed', function () {
    $this->get('/en/about')->assertRedirect('/en/aboutus');
    $this->get('/en/teachers')->assertNotFound();
    $this->get('/en/app')->assertNotFound();
    $this->get('/en/school')->assertNotFound();
    $this->get('/en/book-a-private-conversation')->assertNotFound();
});

test('missing pages return a branded noindex 404 with catalog links', function () {
    $origin = rtrim((string) config('app.url'), '/');

    $this->get('/en/teachers')->assertNotFound()
        ->assertHeader('X-Robots-Tag', 'noindex, follow')
        ->assertSee('<meta name="robots" content="noindex, follow">', false)
        ->assertSee('Page not found', false)
        ->assertSee('href="'.$origin.'/en/products"', false)
        ->assertSee('>Home</span>', false);

    $this->get('/ro/school')->assertNotFound()
        ->assertSee('Pagină inexistentă', false)
        ->assertSee('href="'.$origin.'/ro/products"', false);
});

test('privacy and terms pages are localized and linked from the footer', function () {
    $origin = rtrim((string) config('app.url'), '/');

    $this->get('/en')->assertOk()
        ->assertSee('href="'.$origin.'/en/privacy-policy"', false)
        ->assertSee('href="'.$origin.'/en/terms"', false)
        ->assertSee('href="'.$origin.'/en/products"', false)
        ->assertSee('href="'.$origin.'/en/custom"', false)
        ->assertSee('href="'.$origin.'/en/aboutus"', false)
        ->assertSee('href="'.$origin.'/en/size-chart"', false)
        ->assertSee('href="'.$origin.'/en/contact"', false)
        ->assertSee('Browse shirts', false)
        ->assertDontSee('svg-linkedin', false);

    $this->get('/en/privacy-policy')->assertOk()
        ->assertSee('Privacy Policy', false)
        ->assertSee('Shirt House SRL', false)
        ->assertSee('href="'.$origin.'/en/terms"', false);

    $this->get('/ro/privacy-policy')->assertOk()
        ->assertSee('Politica de confidențialitate', false);

    $this->get('/en/terms')->assertOk()
        ->assertSee('Made to order', false);
});

test('pages emit organization and website json-ld', function () {
    $origin = rtrim((string) config('app.url'), '/');

    $this->get('/en')->assertOk()
        ->assertSee('<script type="application/ld+json">', false)
        ->assertSee('"@type":"Organization"', false)
        ->assertSee('"@type":"WebSite"', false)
        ->assertSee('"url":"'.$origin.'/images/logo.png"', false)
        ->assertSee('"width":512', false)
        ->assertDontSee('SearchAction', false);
});

test('storefront images reserve space and decorative icons are hidden from assistive tech', function () {
    $this->get('/en/custom')->assertOk()
        ->assertSee('width="400"', false)
        ->assertSee('height="400"', false)
        ->assertSee('loading="lazy"', false)
        ->assertDontSee('alt="Clef Play"', false)
        ->assertDontSee('alt="Custom Shirt"', false);

    $this->get('/en')->assertOk()
        ->assertSee('aria-label="'.e(config('app.name')).'"', false)
        ->assertSee('aria-hidden="true"', false);
});

test('money pages preload lcp images and do not load ag-grid', function () {
    $this->get('/en')->assertOk()
        ->assertSee('rel="preload" as="image"', false)
        ->assertSee('/images/home.jpg', false)
        ->assertSee('fetchpriority="high"', false)
        ->assertDontSee('ag-grid-community', false);
});

test('trailing slashes are redirected to the slashless path', function () {
    $request = Illuminate\Http\Request::create('http://localhost/en/products/', 'GET');
    $response = (new App\Http\Middleware\EnsurePreferredOrigin)->handle($request, fn () => response('ok'));

    expect($response->getStatusCode())->toBe(301);
    expect($response->headers->get('Location'))->toEndWith('/en/products');
});

test('production requests are redirected to the APP_URL origin', function () {
    $this->app['env'] = 'production';
    config(['app.url' => 'https://shirthouse.ro']);

    $this->withServerVariables([
        'HTTP_HOST' => 'www.shirthouse.ro',
        'HTTPS' => 'off',
        'SERVER_PORT' => '80',
    ])->get('/en/products')
        ->assertStatus(301)
        ->assertRedirect('https://shirthouse.ro/en/products');
});

test('gtm loads on the storefront only after cookie consent', function () {
    config(['services.gtm.id' => 'GTM-TESTID']);

    $this->get('/en')->assertOk()
        ->assertSee('data-reference="cookie-banner"', false)
        ->assertDontSee("'dataLayer','GTM-TESTID'", false);

    $this->withUnencryptedCookie(\App\Support\Gtm::COOKIE, 'granted')
        ->get('/en')
        ->assertOk()
        ->assertSee("'dataLayer','GTM-TESTID'", false)
        ->assertDontSee('data-reference="cookie-banner"', false);

    $this->withUnencryptedCookie(\App\Support\Gtm::COOKIE, 'denied')
        ->get('/en')
        ->assertOk()
        ->assertDontSee("'dataLayer','GTM-TESTID'", false)
        ->assertDontSee('data-reference="cookie-banner"', false);

    $this->withUnencryptedCookie(\App\Support\Gtm::COOKIE, 'granted')
        ->get('/admin/login')
        ->assertOk()
        ->assertDontSee("'dataLayer','GTM-TESTID'", false)
        ->assertDontSee('data-reference="cookie-banner"', false)
        ->assertDontSee('root-cookie-banner', false);
});

test('privacy policy describes optional analytics cookies', function () {
    $this->get('/en/privacy-policy')->assertOk()
        ->assertSee('Google Tag Manager', false)
        ->assertDontSee('We do not currently use analytics or advertising cookies', false);
});
