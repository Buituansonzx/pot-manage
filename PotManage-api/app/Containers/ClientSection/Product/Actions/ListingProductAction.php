<?php

namespace App\Containers\ClientSection\Product\Actions;

use App\Containers\ClientSection\Product\Tasks\ListingProductTask;
use App\Containers\ClientSection\Product\UI\API\Requests\ListingProductRequest;
use App\Ship\Parents\Actions\Action as ParentAction;

final class ListingProductAction extends ParentAction
{
    public function run(ListingProductRequest $listingProductRequest)
    {
        $data = $listingProductRequest->validated();
        return  app(ListingProductTask::class)->run($data);
    }
}
