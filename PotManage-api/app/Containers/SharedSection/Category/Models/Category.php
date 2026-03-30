<?php

namespace App\Containers\SharedSection\Category\Models;

use App\Containers\SharedSection\Product\Models\Product;
use App\Ship\Parents\Models\Model as ParentModel;

final class Category extends ParentModel
{
    protected $guarded = [];

    protected $table = 'categories';

    public function product()
    {
        return $this->hasMany(Product::class);
    }
}
