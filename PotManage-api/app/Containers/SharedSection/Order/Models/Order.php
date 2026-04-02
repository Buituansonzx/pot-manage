<?php

namespace App\Containers\SharedSection\Order\Models;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Models\Model as ParentModel;

final class Order extends ParentModel
{
    protected $guarded = [];

    protected $table = 'orders';

    public function createBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
