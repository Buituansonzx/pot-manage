<?php

namespace App\Containers\ClientSection\Order\UI\API\Transformers;

use App\Containers\SharedSection\Order\Models\Order;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

final class OrderTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [
        'items'
    ];

    protected array $availableIncludes = [];

    public function transform(Order $order): array
    {
        return [
            'type' => $order->getResourceKey(),
            'id' => $order->id,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'total' => (float) $order->total,
            'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $order->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function includeItems(Order $order)
    {
        $order->loadMissing(['items.product.productImages', 'items.product.category']);

        if($order->items && $order->items->isNotEmpty()) {
            $itemArrays = $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product ? $item->product->name : null,
                    'category_name' => $item->product && $item->product->category ? $item->product->category->name : null,
                    'quantity' => $item->quantity,
                    'price' => (float) $item->price,
                    'subtotal' => (float) $item->subtotal,
                    'note' => $item->note,
                    'images' => $item->product && $item->product->productImages ? $item->product->productImages->map(function($img) {
                        return ['image_url' => $img->image_url];
                    })->toArray() : [],
                ];
            })->toArray();
            return $this->primitive($itemArrays);
        }
        return $this->primitive([]);

    }
}
