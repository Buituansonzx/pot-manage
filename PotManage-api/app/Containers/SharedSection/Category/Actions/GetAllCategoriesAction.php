<?php

namespace App\Containers\SharedSection\Category\Actions;

use App\Containers\SharedSection\Category\Tasks\GetAllCategoriesTask;
use App\Containers\SharedSection\Category\UI\API\Requests\GetAllCategoriesRequest;
use App\Ship\Parents\Actions\Action as ParentAction;

class GetAllCategoriesAction extends ParentAction
{
    public function run(GetAllCategoriesRequest $request): mixed
    {
        $search = $request->input('search');
        
        return app(GetAllCategoriesTask::class)->run($search);
    }
}
