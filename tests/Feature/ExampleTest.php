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
