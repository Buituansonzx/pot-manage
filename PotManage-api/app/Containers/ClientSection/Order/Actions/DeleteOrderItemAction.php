<?php

namespace App\Containers\ClientSection\Order\Actions;

use App\Containers\ClientSection\Order\Tasks\DeleteOrderItemTask;
use App\Containers\ClientSection\Order\Tasks\FindOrderByIdTask;
use App\Containers\ClientSection\Order\Tasks\UpdateOrderTotalTask;
use App\Containers\ClientSection\Order\UI\API\Requests\DeleteOrderItemRequest;
use App\Containers\SharedSection\Order\Models\Order;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class DeleteOrderItemAction extends ParentAction
{
    public function run(DeleteOrderItemRequest $request): Order
    {
        return DB::transaction(function () use ($request) {
            $order = app(FindOrderByIdTask::class)->run($request->id);

            $user = $request->user();
            if ($order->user_id !== $user->id && !$user->hasRole('admin')) {
                throw new AccessDeniedHttpException('Bạn không có quyền thao tác trên đơn hàng này');
            }

            app(DeleteOrderItemTask::class)->run($request->item_id);

            return app(UpdateOrderTotalTask::class)->run($order->id);
        });
    }
}
