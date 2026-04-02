<?php

/**
 * @apiGroup           Order
 * @apiName            updateOrderItem
 * @api                {patch} /v1/orders/:id/items/:item_id Update Order Item
 * @apiDescription     Endpoint to update an existing order item
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated User
 */

use App\Containers\ClientSection\Order\UI\API\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::patch('orders/{id}/items/{item_id}', [OrderController::class, 'updateOrderItem'])
    ->middleware(['auth:api']);
