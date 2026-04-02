<?php

namespace App\Containers\ClientSection\Order\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class GetAllOrdersRequest extends ParentRequest
{
    public function rules(): array
    {
        return [
            'status' => 'nullable|string',
            'customer_name' => 'nullable|string',
            'customer_phone' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:100',
        ];
    }
}
