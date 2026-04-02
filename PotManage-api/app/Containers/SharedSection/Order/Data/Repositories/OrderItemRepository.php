<?php

namespace App\Containers\SharedSection\Order\Data\Repositories;

use App\Containers\SharedSection\Order\Models\OrderItem;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of OrderItem
 *
 * @extends ParentRepository<TModel>
 */
final class OrderItemRepository extends ParentRepository
{
    protected $fieldSearchable = [
        // 'id' => '=',
    ];
}
