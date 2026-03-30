<?php

namespace App\Containers\AdminSection\Product\Tasks;

use App\Containers\SharedSection\Product\Models\ProductPrice;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Exception;

class UpdateProductPricesTask extends ParentTask
{
    public function run(int $productId, array $prices)
    {
        try {
            $productPrice = ProductPrice::where('product_id', $productId)->first();

            if ($productPrice) {
                if (array_key_exists('floor_price', $prices)) {
                    $productPrice->floor_price = $prices['floor_price'];
                }
                if (array_key_exists('suggested_retail_price', $prices)) {
                    $productPrice->suggested_retail_price = $prices['suggested_retail_price'];
                }
                if (array_key_exists('min_retail_price', $prices)) {
                    $productPrice->min_retail_price = $prices['min_retail_price'];
                }
                
                $productPrice->save();
                return $productPrice;
            }

            return ProductPrice::create([
                'product_id' => $productId,
                'floor_price' => $prices['floor_price'] ?? 0,
                'suggested_retail_price' => $prices['suggested_retail_price'] ?? 0,
                'min_retail_price' => $prices['min_retail_price'] ?? null,
            ]);

        } catch (Exception $exception) {
            throw new Exception('Could not update Product Prices.');
        }
    }
}
