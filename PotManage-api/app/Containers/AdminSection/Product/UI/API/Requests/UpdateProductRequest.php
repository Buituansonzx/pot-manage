<?php

namespace App\Containers\AdminSection\Product\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

class UpdateProductRequest extends ParentRequest
{

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            
            // Prices
            'prices' => 'nullable|array',
            'prices.floor_price' => 'nullable|numeric|min:0',
            'prices.suggested_retail_price' => 'nullable|numeric|min:0',
            'prices.min_retail_price' => 'nullable|numeric|min:0',
            
            // Attributes
            'attributes' => 'nullable|array',
            'attributes.*.attribute_name' => 'required_with:attributes|string',
            'attributes.*.attribute_value' => 'required_with:attributes|string',
            
            // Images
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120', // 5MB max
        ];
    }
}
