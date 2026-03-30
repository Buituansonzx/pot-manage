<?php

namespace App\Containers\ClientSection\Product\Tasks;

use App\Containers\SharedSection\Product\Data\Repositories\ProductRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class ListingProductTask extends ParentTask
{
    public function __construct(private readonly ProductRepository $productRepository)
    {
    }

    public function run(array $data)
    {
        return $this->productRepository->listing($data);
    }
}
