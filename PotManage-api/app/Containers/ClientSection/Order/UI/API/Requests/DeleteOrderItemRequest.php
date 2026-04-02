<?php

namespace App\Containers\ClientSection\Order\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class DeleteOrderItemRequest extends ParentRequest
{
    public function rules(): array
    {
        return [];
    }
}
