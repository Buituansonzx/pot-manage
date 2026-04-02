<?php

namespace App\Containers\SharedSection\Order\Data\Repositories;

use App\Containers\SharedSection\Order\Models\Order;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of Order
 *
 * @extends ParentRepository<TModel>
 */
final class OrderRepository extends ParentRepository
{
    protected $fieldSearchable = [
        // 'id' => '=',
    ];
    public function listing(array $data)
    {
        $query = $this->model->query()->with('items.product');

        if (!empty($data['user_id'])) {
            $query = $query->where('user_id', $data['user_id']);
        }

        if (!empty($data['status'])) {
            $query = $query->where('status', $data['status']);
        }

        if (!empty($data['customer_name'])) {
            $query = $query->where('customer_name', 'like', "%{$data['customer_name']}%");
        }

        if (!empty($data['customer_phone'])) {
            $query = $query->where('customer_phone', 'like', "%{$data['customer_phone']}%");
        }

        $query = $query->orderBy('created_at', 'desc');

        return $query->paginate($data['limit'] ?? 20);
    }
}
