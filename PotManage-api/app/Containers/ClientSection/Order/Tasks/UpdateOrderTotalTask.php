<?php

namespace App\Containers\ClientSection\Order\Tasks;

use App\Containers\SharedSection\Order\Data\Repositories\OrderRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Support\Facades\DB;

final class UpdateOrderTotalTask extends ParentTask
{
    public function __construct(
        protected readonly OrderRepository $repository
    ) {
    }

    public function run($orderId)
    {
        $order = $this->repository->find($orderId);
        $total = $order->items()->sum('subtotal');
        return $this->repository->update(['total' => $total], $orderId);
    }
}
