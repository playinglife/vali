<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $origin = rtrim((string) config('app.url'), '/');
        $locales = SetLocale::supportedLocaleCodes();
        $paths = [
            '',
            '/products',
            '/aboutus',
            '/custom',
            '/size-chart',
            '/contact',
            '/privacy-policy',
            '/terms',
        ];

        $urls = [];
        foreach ($paths as $path) {
            foreach ($locales as $locale) {
                $urls[] = [
                    'loc' => $origin.'/'.$locale.$path,
                    'lastmod' => null,
                ];
            }
        }

        try {
            $products = Product::query()->active()->orderBy('slug')->get(['slug', 'updated_at']);
        } catch (\Throwable) {
            $products = collect();
        }

        foreach ($products as $product) {
            $lastmod = $product->updated_at?->toAtomString();
            foreach ($locales as $locale) {
                $urls[] = [
                    'loc' => $origin.'/'.$locale.'/products/'.$product->slug,
                    'lastmod' => $lastmod,
                ];
            }
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $origin = rtrim((string) config('app.url'), '/');
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
        ];
        foreach (SetLocale::supportedLocaleCodes() as $locale) {
            foreach (['cart', 'checkout', 'thankyou', 'contact-success'] as $path) {
                $lines[] = 'Disallow: /'.$locale.'/'.$path;
            }
        }
        $lines[] = '';
        $lines[] = 'Sitemap: '.$origin.'/sitemap.xml';

        return response(implode("\n", $lines)."\n", 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
