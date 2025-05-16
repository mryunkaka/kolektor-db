<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('filament.admin.pages.dashboard');
    }

    return redirect()->route('filament.admin.auth.login');
});
