<?php

namespace App\Containers\SharedSection\Category\UI\API\Transformers;

use App\Containers\SharedSection\Category\Models\Category;
use App\Ship\Parents\Transformers\Transformer as ParentTransformer;

class CategoryTransformer extends ParentTransformer
{
    protected array $defaultIncludes = [

    ];

    protected array $availableIncludes = [

    ];

    public function transform(Category $category): array
    {
        return [
            'object' => $category->getResourceKey(),
            'id' => $category->id,
            'parent_id' => $category->parent_id,
            'name' => $category->name,
            'description' => $category->description,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
        ];
    }
}
