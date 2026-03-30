<?php

namespace App\Containers\AdminSection\Product\Tasks;

use App\Containers\SharedSection\Product\Models\ProductAttribute;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Exception;

class UpdateProductAttributesTask extends ParentTask
{
    public function run(int $productId, array $attributes)
    {
        try {
            $createdAttributes = [];
            foreach ($attributes as $attribute) {
                if (!empty($attribute['attribute_name']) && isset($attribute['attribute_value'])) {
                    $createdAttributes[] = ProductAttribute::updateOrCreate(
                        [
                            'product_id' => $productId,
                            'attribute_name' => $attribute['attribute_name']
                        ],
                        [
                            'attribute_value' => $attribute['attribute_value']
                        ]
                    );
                }
            }

            return $createdAttributes;
        } catch (Exception $exception) {
            throw new Exception('Could not update Product Attributes.');
        }
    }
}
