<?php

namespace App\Containers\ClientSection\Product\UI\API\Controllers;

use Apiato\Http\Response;
use App\Containers\ClientSection\Product\Actions\ListingProductAction;
use App\Containers\ClientSection\Product\UI\API\Requests\ListingProductRequest;
use App\Containers\ClientSection\Product\UI\API\Transformers\ProductTransformer;
use App\Ship\Parents\Controllers\ApiController;

final class ProductController extends ApiController
{
    public function listing(ListingProductRequest $request)
    {
        $products = app(ListingProductAction::class)->run($request);

        return Response::create($products, ProductTransformer::class);
    }
}
