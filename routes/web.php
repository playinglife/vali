<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/teachers', function() {
    return view('teachers');
});
Route::get('/app', function() {
    return view('app');
});
Route::get('/about', function() {
    return view('about');
});
Route::get('/school', function() {
    return view('school');
});
Route::get('/contact', function() {
    return view('contact');
});
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::get('/contact-success', function() {
    return view('contact-success');
})->name('contact.success');
Route::get('/book-a-private-conversation', function() {
    return view('bookacall');
});
Route::post('/book-a-private-conversation', function () {
    return redirect()->back();
})->name('bookacall.submit');