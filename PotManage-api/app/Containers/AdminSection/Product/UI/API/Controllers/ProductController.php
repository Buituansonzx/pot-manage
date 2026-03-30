<?php

namespace App\Containers\AdminSection\Product\UI\API\Controllers;

use Apiato\Http\Response;
use App\Ship\Parents\Controllers\ApiController;

use App\Containers\AdminSection\Product\Actions\UpdateProductAction;
use App\Containers\AdminSection\Product\UI\API\Requests\UpdateProductRequest;
use App\Containers\ClientSection\Product\UI\API\Transformers\ProductTransformer;

final class ProductController extends ApiController
{
    /**
     * @param UpdateProductRequest $request
     * @param UpdateProductAction $action
     * @return array
     */
    public function update(UpdateProductRequest $request, UpdateProductAction $action)
    {
        $product = $action->run($request);
        return Response::create($product, ProductTransformer::class);
    }
}
