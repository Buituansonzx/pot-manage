<?php

namespace App\Containers\ClientSection\Order\Actions;

use App\Containers\ClientSection\Order\Tasks\CreateOrderItemTask;
use App\Containers\ClientSection\Order\Tasks\UpdateOrderItemTask;
use App\Containers\ClientSection\Order\Tasks\FindOrderByIdTask;
use App\Containers\ClientSection\Order\Tasks\FindProductWithPriceTask;
use App\Containers\ClientSection\Order\Tasks\UpdateOrderTotalTask;
use App\Containers\ClientSection\Order\UI\API\Requests\AddProductToOrderRequest;
use App\Containers\SharedSection\Order\Models\Order;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class AddProductToOrderAction extends ParentAction
{
    public function run(AddProductToOrderRequest $request): Order
    {
        return DB::transaction(function () use ($request) {
            //Tìm đơn hàng
            $order = app(FindOrderByIdTask::class)->run($request->id);

            $user = $request->user();
            if ($order->user_id !== $user->id && !$user->hasRole('admin')) {
                throw new AccessDeniedHttpException('Bạn không có quyền thao tác trên đơn hàng này');
            }

            //Tìm sản phẩm
            $product = app(FindProductWithPriceTask::class)->run($request->product_id);
            $productPrice = $product->productPrices->last();

            if (!$productPrice) {
                throw ValidationException::withMessages(['product_id' => 'Sản phẩm chưa được cấu hình giá']);
            }
            //Kiểm tra sản phẩm đã có trong đơn hàng chưa
            $existingItem = $order->items()->where('product_id', $request->product_id)->first();

            $price = $request->price;
            if ($price !== null) {
                if ($productPrice->min_retail_price !== null && $price < $productPrice->min_retail_price) {
                    throw ValidationException::withMessages(['price' => 'Giá không được thấp hơn giá tối thiểu quy định']);
                }
            } else {
                //Nếu không có giá thì lấy giá của sản phẩm
                $price = $existingItem ? $existingItem->price : $productPrice->suggested_retail_price;
            }

            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $request->quantity;
                app(UpdateOrderItemTask::class)->run($existingItem->id, [
                    'quantity' => $newQuantity,
                    'price' => $price,
                    'subtotal' => $price * $newQuantity,
                    'note' => $request->has('note') ? $request->note : $existingItem->note,
                ]);
            } else {
                //Thêm sản phẩm vào đơn hàng
                app(CreateOrderItemTask::class)->run([
                    'order_id' => $order->id,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                    'price' => $price,
                    'subtotal' => $price * $request->quantity,
                    'note' => $request->note ?? null,
                ]);
            }

            //Update tổng tiền đơn hàng
            return app(UpdateOrderTotalTask::class)->run($order->id);
        });
    }
}
