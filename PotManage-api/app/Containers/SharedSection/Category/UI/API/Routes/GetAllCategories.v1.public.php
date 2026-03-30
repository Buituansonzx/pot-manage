<?php

/**
 * @apiGroup           Category
 * @apiName            GetAllCategories
 *
 * @api                {GET} /v1/categories Danh sách Category cơ bản
 * @apiDescription     Lấy danh sách phân trang các mục categories.
 *
 * @apiVersion         1.0.0
 * @apiPermission      none
 *
 */

use App\Containers\SharedSection\Category\UI\API\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('categories', [CategoryController::class, 'listing']);
