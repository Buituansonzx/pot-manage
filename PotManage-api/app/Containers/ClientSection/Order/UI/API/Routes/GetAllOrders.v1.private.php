<?php

/**
 * @apiGroup           Order
 * @apiName            getAllOrders
 * @api                {get} /v1/orders Get All Orders
 * @apiDescription     Endpoint to get all orders with filters
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated User
 */

use App\Containers\ClientSection\Order\UI\API\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('orders', [OrderController::class, 'getAllOrders'])
    ->middleware(['auth:api']);
