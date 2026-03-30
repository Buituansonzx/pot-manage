<?php

namespace App\Containers\SharedSection\Product\Models;

use App\Containers\SharedSection\Category\Models\Category;
use App\Ship\Parents\Models\Model as ParentModel;

final class Product extends ParentModel
{
    protected $table = 'products';

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }
}
