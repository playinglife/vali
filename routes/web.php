<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LocaleController;
use App\Features\Admin\Controllers\AuthController;
use App\Features\Admin\Controllers\DashboardController;
use App\Features\Admin\Controllers\ProductController;
use App\Features\Admin\Controllers\ProductOptionController;
use App\Features\Admin\Controllers\ProductVariantController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/', function () {
    return view('pages.home');
});
Route::get('/products', function () {
    return view('pages.products');
});

Route::get('/products/{product:slug}', function (Product $product) {
    abort_unless($product->is_active, 404);

    $product->load([
        'Categories',
        'ProductImages',
        'ShortDescriptionTranslation',
        'DescriptionTranslation',
        'OptionValues.Option',
        'Variants.PriceBrackets',
        'Variants.DescriptionTranslation',
        'Variants.VariantImages',
        'Variants.Values.Option',
        'PriceBrackets',
    ]);

    return view('pages.product', ['product' => $product]);
})->name('products.show');

Route::get('/cart', function () {
    return view('pages.cart');
});

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

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        Route::post('/products/{product}/options', [ProductOptionController::class, 'store'])->name('options.store');
        Route::put('/products/{product}/options/{option}', [ProductOptionController::class, 'update'])->name('options.update');
        Route::delete('/products/{product}/options/{option}', [ProductOptionController::class, 'destroy'])->name('options.destroy');
        Route::post('/products/{product}/options/{option}/values', [ProductOptionController::class, 'storeValue'])->name('options.values.store');
        Route::put('/products/{product}/options/{option}/values/{value}', [ProductOptionController::class, 'updateValue'])->name('options.values.update');
        Route::delete('/products/{product}/options/{option}/values/{value}', [ProductOptionController::class, 'destroyValue'])->name('options.values.destroy');

        Route::get('/product-detail/{product}', [DashboardController::class, 'productDetail'])->name('products.detail');
        
        Route::get('/products/{product}/variants', [ProductVariantController::class, 'index'])->name('variants.index');
        Route::post('/products/{product}/variants/create-all', [ProductVariantController::class, 'createAll'])->name('variants.create-all');
        Route::post('/products/{product}/variants', [ProductVariantController::class, 'store'])->name('variants.store');
        Route::put('/products/{product}/variants/{variant}', [ProductVariantController::class, 'update'])->name('variants.update');
        Route::delete('/products/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])->name('variants.destroy');
    });
});
