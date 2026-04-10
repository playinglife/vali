<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminProductOptionController;
use App\Http\Controllers\Admin\AdminProductVariantController;
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

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

        Route::post('/products/{product}/options', [AdminProductOptionController::class, 'store'])->name('options.store');
        Route::put('/products/{product}/options/{option}', [AdminProductOptionController::class, 'update'])->name('options.update');
        Route::delete('/products/{product}/options/{option}', [AdminProductOptionController::class, 'destroy'])->name('options.destroy');
        Route::post('/products/{product}/options/{option}/values', [AdminProductOptionController::class, 'storeValue'])->name('options.values.store');
        Route::put('/products/{product}/options/{option}/values/{value}', [AdminProductOptionController::class, 'updateValue'])->name('options.values.update');
        Route::delete('/products/{product}/options/{option}/values/{value}', [AdminProductOptionController::class, 'destroyValue'])->name('options.values.destroy');

        Route::post('/products/{product}/variants', [AdminProductVariantController::class, 'store'])->name('variants.store');
        Route::put('/products/{product}/variants/{variant}', [AdminProductVariantController::class, 'update'])->name('variants.update');
        Route::delete('/products/{product}/variants/{variant}', [AdminProductVariantController::class, 'destroy'])->name('variants.destroy');
    });
});
