<?php

use App\Http\Controllers\AuthSso\RegisteredLoginController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Http\Request;

Route::get('/sso/login1/{isauto?}', function ($isauto = false) {
    return view('authsso.login1', array('isauto' => $isauto));
})->name('sso.login1');

Route::get('/sso/login2/{isauto?}', function ($isauto = false) {
    return view('authsso.login2', array('isauto' => $isauto));
})->name('sso.login2');

Route::get('/sso/is-register/{id_sso}', function ($id_sso) {
    $user = User::where('id_sso', $id_sso)->first();
    return response()->json([
        'status'    => ($user != null) ? true : false,
    ]);
})->name('sso.register');

Route::post('/sso/register', [RegisteredLoginController::class, 'store'])
    ->middleware('guest')
    ->name('sso.register');
