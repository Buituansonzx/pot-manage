<?php

namespace App\Containers\SharedSection\Category\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

class GetAllCategoriesRequest extends ParentRequest
{
    public function rules(): array
    {
        return [
            "search" => "nullable|string",
        ];
    }
}
