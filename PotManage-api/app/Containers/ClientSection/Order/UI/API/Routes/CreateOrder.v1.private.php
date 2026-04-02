<?php

/**
 * @apiGroup           Order
 * @apiName            createOrder
 * @api                {post} /v1/orders Create Order
 * @apiDescription     Endpoint to create a new order
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated User
 */

use App\Containers\ClientSection\Order\UI\API\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('orders', [OrderController::class, 'create'])
    ->middleware(['auth:api']);
