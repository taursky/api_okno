<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\OrderController;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::prefix('v1')
    ->middleware([
        'auth:sanctum',
        'api.client.active',
    ])
    ->group(function () {
        Route::get('/ping', function (Request $request) {
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'ok',
                    'client' => [
                        'id' => $request->user()->id,
                        'name' => $request->user()->name,
                        'description' => $request->user()->description,
                    ],
                ],
            ]);
        });

        Route::middleware([
            'abilities:orders:read',
        ])->group(function () {

            Route::get(
                '/orders',
                [OrderController::class, 'index']
            )->name('api.v1.orders.index');

            Route::get(
                '/orders/{id}',
                [OrderController::class, 'show']
            )
                ->whereNumber('id')
                ->name('api.v1.orders.show');
        });
    });
