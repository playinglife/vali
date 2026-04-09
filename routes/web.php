<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LocaleController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

Route::get('/', function () {
    return view('pages.home');
});
Route::get('/service', function () {
    return view('pages.service');
});

Route::get('/products/{product:slug}', function (Product $product) {
    abort_unless($product->is_active, 404);

    $product->load([
        'Categories',
        'ShortDescriptionTranslation',
        'DescriptionTranslation',
        'OptionValues.Option',
        'Variants.PriceBrackets',
        'Variants.DescriptionTranslation',
        'Variants.Values.Option',
        'PriceBrackets',
    ]);

    return view('pages.product', ['product' => $product]);
})->name('products.show');

Route::get('/cart', function () {
    return view('pages.cart');
})->name('cart.index');

Route::get('/teachers', function () {
    return view('pages.teachers');
});
Route::get('/app', function () {
    return view('pages.app');
});
Route::get('/about', function () {
    return view('pages.about');
});
Route::get('/school', function () {
    return view('pages.school');
});
Route::get('/contact', function () {
    return view('pages.contact');
});
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::get('/contact-success', function () {
    return view('pages.contact-success');
})->name('contact.success');
Route::get('/book-a-private-conversation', function () {
    return view('pages.bookacall');
});
Route::post('/book-a-private-conversation', function () {
    return redirect()->back();
})->name('bookacall.submit');
