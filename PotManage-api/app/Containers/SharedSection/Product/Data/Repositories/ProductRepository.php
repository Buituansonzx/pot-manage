<?php

namespace App\Containers\SharedSection\Product\Data\Repositories;

use App\Containers\SharedSection\Product\Models\Product;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of Product
 *
 * @extends ParentRepository<TModel>
 */
final class ProductRepository extends ParentRepository
{
    protected $fieldSearchable = [
        // 'id' => '=',
    ];

    public function listing(array $data)
    {
        $query = $this->model->query()
            ->join('product_prices', 'products.id', '=', 'product_prices.product_id')
            ->select('products.*')->with('category', 'productAttributes');

        if (!empty($data['category_id'])) {
            $query->where('products.category_id', $data['category_id']);
        }

        if (!empty($data['search'])) {
            $query->where('products.name', 'like', "%{$data['search']}%");
        }

        if (!empty($data['min_price'])) {
            $query->where('product_prices.suggested_retail_price', '>=', $data['min_price']);
        }

        if (!empty($data['max_price'])) {
            $query->where('product_prices.suggested_retail_price', '<=', $data['max_price']);
        }

        if (isset($data['sort_by'])) {
            if ($data['sort_by'] === 'price_asc') {
                $query->orderBy('product_prices.suggested_retail_price', 'asc');
            } elseif ($data['sort_by'] === 'price_desc') {
                $query->orderBy('product_prices.suggested_retail_price', 'desc');
            } else {
                $query->orderBy('products.created_at', 'desc');
            }
        } else {
            $query->orderBy('products.created_at', 'desc');
        }

        return $query->paginate(20);
    }
}
