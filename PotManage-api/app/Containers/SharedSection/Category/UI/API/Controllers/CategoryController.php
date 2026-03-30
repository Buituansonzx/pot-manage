<?php

namespace App\Containers\SharedSection\Category\UI\API\Controllers;

use Apiato\Http\Response;
use App\Containers\SharedSection\Category\Actions\GetAllCategoriesAction;
use App\Containers\SharedSection\Category\UI\API\Requests\GetAllCategoriesRequest;
use App\Containers\SharedSection\Category\UI\API\Transformers\CategoryTransformer;
use App\Ship\Parents\Controllers\ApiController;

final class CategoryController extends ApiController
{
    public function listing(GetAllCategoriesRequest $request)
    {
        $categories = app(GetAllCategoriesAction::class)->run($request);

        return Response::create($categories, CategoryTransformer::class);
    }
}
