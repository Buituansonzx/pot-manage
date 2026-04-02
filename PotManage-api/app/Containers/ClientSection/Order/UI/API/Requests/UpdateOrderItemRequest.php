<?php

namespace App\Containers\ClientSection\Order\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class UpdateOrderItemRequest extends ParentRequest
{
    public function rules(): array
    {
        return [
            'quantity' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ];
    }
}
