<?php

namespace App\Containers\AdminSection\Product\Tasks;

use App\Containers\SharedSection\Product\Data\Repositories\ProductRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Exception;

class UpdateProductTask extends ParentTask
{
    public function __construct(
        protected ProductRepository $repository
    ) {
    }

    public function run(int $id, array $data)
    {
        try {
            return $this->repository->update($data, $id);
        } catch (Exception $exception) {
            throw new Exception('Could not update Product.');
        }
    }
}
