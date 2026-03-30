<?php

namespace App\Containers\SharedSection\Category\Data\Repositories;

use App\Containers\SharedSection\Category\Models\Category;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of Category
 *
 * @extends ParentRepository<TModel>
 */
final class CategoryRepository extends ParentRepository
{
    protected $fieldSearchable = [
        'id' => '=',
        'name' => 'like',
    ];

    /**
     * Filter categories by name.
     */
    public function filterByName(string $search)
    {
        $this->scopeQuery(function ($query) use ($search) {
            return $query->where('name', 'like', "%{$search}%");
        });

        return $this;
    }
}
