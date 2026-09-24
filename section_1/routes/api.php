<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function (Request $request) {
    return response()->json(['message' => 'test']);
});
Route::get('/test-fail', function (Request $request) {
    return response()->json([
        'message' => 'hello'
    ], 400);
});
