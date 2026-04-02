<?php

/**
 * @apiGroup           Product
 * @apiName            UpdateProduct
 *
 * @api                {put} /v1/products/:id Update a Product
 * @apiDescription     Update a product including basic info, prices, attributes, and images.
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated User
 *
 * @apiParam           {String} name Product name
 * @apiParam           {Integer} category_id Category ID
 * @apiParam           {String} description Details of product
 * @apiParam           {Array} [prices] Prices associated to product (floor_price, suggested_retail_price, min_retail_price)
 * @apiParam           {Array} [attributes] Attributes of product (array of {attribute_name, attribute_value})
 * @apiParam           {File[]} [images] Array of images to upload
 *
 * @apiUse             ProductSuccessSingleResponse
 */

use App\Containers\AdminSection\Product\UI\API\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('admin/products/{id}', [ProductController::class, 'update'])
    ->middleware(['auth:api','role:admin']);
