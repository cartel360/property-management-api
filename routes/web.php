<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-auth', function () {
    $user = \App\Models\User::first();
    $token = $user->createToken('telescope-test')->plainTextToken;

    return [
        'token' => $token,
        'user_id' => $user->id
    ];
});
