<?php

namespace App\Containers\SharedSection\Category\Tasks;

use App\Containers\SharedSection\Category\Data\Repositories\CategoryRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

class GetAllCategoriesTask extends ParentTask
{
    public function __construct(
        protected CategoryRepository $repository
    ) {
    }

    public function run(?string $search = null): mixed
    {
        if ($search) {
            $this->repository->filterByName($search);
        }
        
        return $this->repository->all();
    }
}
