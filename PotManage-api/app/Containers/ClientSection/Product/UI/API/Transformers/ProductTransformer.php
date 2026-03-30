<?php

namespace App\Containers\ClientSection\Product\UI\API\Transformers;

use App\Containers\SharedSection\Product\Models\Product as ModelsProduct;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;
use App\Containers\AppSection\Authorization\Enums\Role;
use App\Containers\AppSection\User\Models\User;

final class ProductTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [
        'attribute',
        'price',
        'images',
    ];

    protected array $availableIncludes = [];

    public function transform(ModelsProduct $product): array
    {
        return [
            'type' => $product->getResourceKey(),
            'id' => $product->id,
            'name' => $product->name,
            'category_name' => $product->category->name,
            'description' => $product->description,
            'created_at' => $product->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $product->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function includeAttribute(ModelsProduct $product)
    {
        if($product->productAttributes && $product->productAttributes->isNotEmpty()){
            $productAttributesArray = $product->productAttributes->map(function ($productAttribute) {
                return [
                    'attribute_name' => $productAttribute->attribute_name,
                    'attribute_value' => $productAttribute->attribute_value,
                ];
            })->toArray();
            return $this->primitive($productAttributesArray);
        }
        return $this->primitive([]);
    }

    public function includePrice(ModelsProduct $product)
    {
        if($product->productPrices && $product->productPrices->isNotEmpty()){
            /** @var User|null $user */
            $user = auth('api')->user();

            $productPricesArray = $product->productPrices->map(function ($productPrice) use ($user) {
                $price = [
                    "suggested_retail_price" => floor($productPrice->suggested_retail_price),
                ];

                if ($user) {
                    if ($user->hasRole(Role::SALES, 'api') || $user->isSuperAdmin()) {
                        $price["min_retail_price"] = floor($productPrice->min_retail_price);
                    }
                    
                    if ($user->hasRole(Role::COLLABORATOR, 'api') || $user->isSuperAdmin()) {
                        $price["floor_price"] = floor($productPrice->floor_price);
                    }
                }

                return $price;
            })->toArray();
            return $this->primitive($productPricesArray);
        }
        return $this->primitive([]);
    }

    public function includeImages(ModelsProduct $product)
    {
        if($product->productImages && $product->productImages->isNotEmpty()){
            $productImagesArray = $product->productImages->map(function ($productImage) {
                return [
                    'image_url' => $productImage->image_url,
                    'is_primary' => $productImage->is_primary,
                    'sort_order' => $productImage->sort_order,
                ];
            })->toArray();
            return $this->primitive($productImagesArray);
        }
        return $this->primitive([]);
    }
}
