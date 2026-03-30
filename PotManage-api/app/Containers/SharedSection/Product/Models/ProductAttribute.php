<?php

namespace App\Containers\SharedSection\Product\Models;

use App\Ship\Parents\Models\Model as ParentModel;

final class ProductAttribute extends ParentModel
{
    protected $guarded = [];

    protected $table = 'product_attributes';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
