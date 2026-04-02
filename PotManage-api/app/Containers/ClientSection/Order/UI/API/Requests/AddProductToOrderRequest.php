<?php

namespace App\Containers\ClientSection\Order\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class AddProductToOrderRequest extends ParentRequest
{
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Mã sản phẩm là bắt buộc',
            'product_id.exists' => 'Sản phẩm không tồn tại',
            'quantity.required' => 'Số lượng là bắt buộc',
            'quantity.integer' => 'Số lượng phải là số nguyên',
            'quantity.min' => 'Số lượng phải lớn hơn 0',
            'price.required' => 'Giá là bắt buộc',
            'price.numeric' => 'Giá phải là số',
            'price.min' => 'Giá phải lớn hơn 0',
        ];
    }
}
