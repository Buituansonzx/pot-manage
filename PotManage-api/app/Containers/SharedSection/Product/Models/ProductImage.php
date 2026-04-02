<?php

namespace App\Containers\SharedSection\Product\Models;

use App\Ship\Parents\Models\Model as ParentModel;

class ProductImage extends ParentModel
{
    protected $table = 'product_images';

    protected $fillable = [
        'product_id',
        'image_url',
        'is_primary',
        'sort_order',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
