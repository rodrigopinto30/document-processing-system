<?php

use App\Http\Controllers\ProcessController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([
    'prefix' => 'process'
], function () {
    Route::post('start', [ProcessController::class, "start"]);

    Route::post('stop/{process_id}', [ProcessController::class, "stop"]);

    Route::get('status/{process_id}', [ProcessController::class, "status"]);

    Route::get('list', [ProcessController::class, "list"]);

    Route::get('results/{process_id}', [ProcessController::class, "results"]);
});
