<?php

namespace App\Containers\ClientSection\Order\UI\API\Controllers;

use Apiato\Http\Response;
use App\Containers\ClientSection\Order\Actions\CreateOrderAction;
use App\Containers\ClientSection\Order\Actions\AddProductToOrderAction;
use App\Containers\ClientSection\Order\Actions\UpdateOrderItemAction;
use App\Containers\ClientSection\Order\Actions\DeleteOrderItemAction;
use App\Containers\ClientSection\Order\Actions\GetAllOrdersAction;
use App\Containers\ClientSection\Order\UI\API\Requests\CreateOrderRequest;
use App\Containers\ClientSection\Order\UI\API\Requests\AddProductToOrderRequest;
use App\Containers\ClientSection\Order\UI\API\Requests\UpdateOrderItemRequest;
use App\Containers\ClientSection\Order\UI\API\Requests\DeleteOrderItemRequest;
use App\Containers\ClientSection\Order\UI\API\Requests\GetAllOrdersRequest;
use App\Containers\ClientSection\Order\UI\API\Transformers\OrderTransformer;
use App\Ship\Parents\Controllers\ApiController;

final class OrderController extends ApiController
{

    public function create(CreateOrderRequest $request)
    {
        $order = app(CreateOrderAction::class)->run($request);

        return Response::create($order, OrderTransformer::class);
    }

    public function addProduct(AddProductToOrderRequest $request)
    {
        $order = app(AddProductToOrderAction::class)->run($request);

        return Response::create($order, OrderTransformer::class);
    }

    public function updateOrderItem(UpdateOrderItemRequest $request)
    {
        $order = app(UpdateOrderItemAction::class)->run($request);

        return Response::create($order, OrderTransformer::class);
    }

    public function deleteOrderItem(DeleteOrderItemRequest $request)
    {
        $order = app(DeleteOrderItemAction::class)->run($request);

        return Response::create($order, OrderTransformer::class);
    }

    public function getAllOrders(GetAllOrdersRequest $request)
    {
        $orders = app(GetAllOrdersAction::class)->run($request);

        return Response::create($orders, OrderTransformer::class);
    }
}
