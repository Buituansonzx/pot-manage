<?php

/**
 * @apiGroup           Order
 * @apiName            deleteOrderItem
 * @api                {delete} /v1/orders/:id/items/:item_id Delete Order Item
 * @apiDescription     Endpoint to remove an item from an order
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated User
 */

use App\Containers\ClientSection\Order\UI\API\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::delete('orders/{id}/items/{item_id}', [OrderController::class, 'deleteOrderItem'])
    ->middleware(['auth:api']);
