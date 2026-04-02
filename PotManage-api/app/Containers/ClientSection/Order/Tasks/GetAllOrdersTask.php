<?php

namespace App\Containers\ClientSection\Order\Tasks;

use App\Containers\SharedSection\Order\Data\Repositories\OrderRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class GetAllOrdersTask extends ParentTask
{
    public function __construct(
        protected readonly OrderRepository $repository
    ) {
    }

    public function run(array $data)
    {
        return $this->repository->listing($data);
    }
}
