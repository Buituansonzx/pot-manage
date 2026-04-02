<?php
/**
 * @apiGroup           Order
 * @apiName            addProductToOrder
 * @api                {post} /v1/orders/:id/products Add Product to Order
 * @apiDescription     Endpoint to add a new product to an existing order
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated User
 */
use App\Containers\ClientSection\Order\UI\API\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('orders/{id}/products', [OrderController::class, 'addProduct'])
    ->middleware(['auth:api']);
