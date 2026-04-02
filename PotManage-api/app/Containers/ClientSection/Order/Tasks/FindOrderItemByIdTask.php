<?php

namespace App\Containers\ClientSection\Order\Tasks;

use App\Containers\SharedSection\Order\Data\Repositories\OrderItemRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class FindOrderItemByIdTask extends ParentTask
{
    public function __construct(
        protected readonly OrderItemRepository $repository
    ) {
    }

    public function run($id)
    {
        return $this->repository->find($id);
    }
}
