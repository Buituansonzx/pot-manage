<?php

namespace App\Containers\ClientSection\Product\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class ListingProductRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'category_id' => 'nullable|exists:categories,id',
            'search' => 'nullable|string',
            'min_price' => 'nullable|numeric',
            'max_price' => 'nullable|numeric',
            'sort_by' => 'nullable|string',
        ];
    }
}
