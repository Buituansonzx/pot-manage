<?php

namespace App\Containers\ClientSection\Order\Actions;

use App\Containers\ClientSection\Order\Tasks\FindOrderByIdTask;
use App\Containers\ClientSection\Order\Tasks\FindOrderItemByIdTask;
use App\Containers\ClientSection\Order\Tasks\FindProductWithPriceTask;
use App\Containers\ClientSection\Order\Tasks\UpdateOrderItemTask;
use App\Containers\ClientSection\Order\Tasks\UpdateOrderTotalTask;
use App\Containers\ClientSection\Order\UI\API\Requests\UpdateOrderItemRequest;
use App\Containers\SharedSection\Order\Models\Order;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class UpdateOrderItemAction extends ParentAction
{
    public function run(UpdateOrderItemRequest $request): Order
    {
        return DB::transaction(function () use ($request) {
            $order = app(FindOrderByIdTask::class)->run($request->id);

            $user = $request->user();
            if ($order->user_id !== $user->id && !$user->hasRole('admin')) {
                throw new AccessDeniedHttpException('Bạn không có quyền thao tác trên đơn hàng này');
            }

            $orderItem = app(FindOrderItemByIdTask::class)->run($request->item_id);

            $price = $request->has('price') ? $request->price : $orderItem->price;
            $quantity = $request->has('quantity') ? $request->quantity : $orderItem->quantity;

            if ($request->has('price')) {
                $product = app(FindProductWithPriceTask::class)->run($orderItem->product_id);
                $productPrice = $product->productPrices->last();
                if ($productPrice && $productPrice->min_retail_price !== null && $price < $productPrice->min_retail_price) {
                    throw ValidationException::withMessages(['price' => 'Giá không được thấp hơn giá tối thiểu quy định']);
                }
            }

            $updateData = [];
            if ($request->has('quantity')) {
                $updateData['quantity'] = $quantity;
            }
            if ($request->has('price')) {
                $updateData['price'] = $price;
            }
            if ($request->has('note')) {
                $updateData['note'] = $request->note;
            }

            $updateData['subtotal'] = $price * $quantity;

            app(UpdateOrderItemTask::class)->run($orderItem->id, $updateData);

            return app(UpdateOrderTotalTask::class)->run($order->id);
        });
    }
}
