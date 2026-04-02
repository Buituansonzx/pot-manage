<?php

namespace App\Containers\ClientSection\Order\Tasks;

use App\Containers\SharedSection\Order\Data\Repositories\OrderItemRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class UpdateOrderItemTask extends ParentTask
{
    public function __construct(
        protected readonly OrderItemRepository $repository
    ) {
    }

    public function run($id, array $data)
    {
        return $this->repository->update($data, $id);
    }
}
