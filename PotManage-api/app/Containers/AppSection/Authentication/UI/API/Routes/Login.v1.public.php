<?php

/**
 * @apiGroup           Authentication
 * @apiName            Login
 * @api                {post} /v1/login Login (Alias cho WebClient)
 * @apiDescription     Login Users using their email and password
 *
 * @apiVersion         1.0.0
 * @apiPermission      none
 *
 * @apiBody            {String} email
 * @apiBody            {String} password
 *
 * @apiSuccessExample  {json}       Success-Response:
 * HTTP/1.1 200 OK
 * {
 * "token_type": "Bearer",
 * "expires_in": 315360000,
 * "access_token": "eyJ0eXAiOiJKV1QiLCJhbG...",
 * "refresh_token": "ZFDPA1S7H8Wydjkjl+xt+hPGWTagX..."
 * }
 */

use App\Containers\AppSection\Authentication\UI\API\Controllers\WebClient\IssueTokenController;
use Illuminate\Support\Facades\Route;

Route::post('login', IssueTokenController::class);
