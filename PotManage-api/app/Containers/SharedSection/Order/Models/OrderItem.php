<?php

namespace App\Containers\SharedSection\Order\Models;

use App\Containers\SharedSection\Product\Models\Product;
use App\Ship\Parents\Models\Model as ParentModel;

final class OrderItem extends ParentModel
{
    protected $guarded = [];

    protected $table = 'order_items';

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
