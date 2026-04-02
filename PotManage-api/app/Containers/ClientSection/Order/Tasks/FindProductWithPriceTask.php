<?php

namespace App\Containers\ClientSection\Order\Tasks;

use App\Containers\SharedSection\Product\Data\Repositories\ProductRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class FindProductWithPriceTask extends ParentTask
{
    public function __construct(
        protected readonly ProductRepository $repository
    ) {
    }

    public function run($id)
    {
        return $this->repository->with('productPrices')->find($id);
    }
}
