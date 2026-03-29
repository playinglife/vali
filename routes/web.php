<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::get('/', function () {
    return view('pages.welcome');
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
