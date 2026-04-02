<?php

namespace App\Containers\ClientSection\Order\Actions;

use App\Containers\ClientSection\Order\Tasks\GetAllOrdersTask;
use App\Containers\ClientSection\Order\UI\API\Requests\GetAllOrdersRequest;
use App\Ship\Parents\Actions\Action as ParentAction;

final class GetAllOrdersAction extends ParentAction
{
    public function run(GetAllOrdersRequest $request)
    {
        $data = $request->all();
        $user = $request->user();

        // Admin sees all, otherwise only their own orders
        if (!$user->hasRole('admin')) {
            $data['user_id'] = $user->id;
        }

        return app(GetAllOrdersTask::class)->run($data);
    }
}
