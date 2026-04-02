<?php

namespace App\Containers\ClientSection\Order\Actions;

use App\Containers\ClientSection\Order\Tasks\CreateOrderItemTask;
use App\Containers\ClientSection\Order\Tasks\CreateOrderTask;
use App\Containers\ClientSection\Order\Tasks\FindProductWithPriceTask;
use App\Containers\ClientSection\Order\Tasks\UpdateOrderTotalTask;
use App\Containers\ClientSection\Order\UI\API\Requests\CreateOrderRequest;
use App\Containers\SharedSection\Order\Models\Order;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CreateOrderAction extends ParentAction
{
    public function run(CreateOrderRequest $request): Order
    {
        return DB::transaction(function () use ($request) {
            //Tìm sản phẩm
            $product = app(FindProductWithPriceTask::class)->run($request->product_id);
            $productPrice = $product->productPrices->last();

            if (!$productPrice) {
                throw ValidationException::withMessages(['product_id' => 'Sản phẩm chưa được cấu hình giá']);
            }

            $price = $request->price;
            if ($price !== null) {
                if ($productPrice->min_retail_price !== null && $price < $productPrice->min_retail_price) {
                    throw ValidationException::withMessages(['price' => 'Giá không được thấp hơn giá tối thiểu quy định']);
                }
            } else {
                $price = $productPrice->suggested_retail_price;
            }
            //Tạo đơn hàng
            $order = app(CreateOrderTask::class)->run([
                'user_id' => $request->user()->id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'status' => 'pending',
                'total' => 0,
            ]);

            //Thêm sản phẩm vào đơn hàng
            app(CreateOrderItemTask::class)->run([
                'order_id' => $order->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $price,
                'subtotal' => $price * $request->quantity,
                'note' => $request->note ?? null,
            ]);

            //Update tổng tiền đơn hàng
            return app(UpdateOrderTotalTask::class)->run($order->id);
        });
    }
}
