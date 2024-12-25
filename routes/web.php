<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/admin');
    }
    return redirect('/admin/login'); // Ganti '/admin/login' sesuai URL login Filament Anda
});
