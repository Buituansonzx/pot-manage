<?php

namespace App\Containers\AdminSection\Product\Actions;

use App\Containers\AdminSection\Product\UI\API\Requests\UpdateProductRequest;
use App\Containers\AdminSection\Product\Tasks\UpdateProductTask;
use App\Containers\AdminSection\Product\Tasks\UpdateProductImagesTask;
use App\Containers\AdminSection\Product\Tasks\UpdateProductPricesTask;
use App\Containers\AdminSection\Product\Tasks\UpdateProductAttributesTask;
use App\Containers\SharedSection\Product\Data\Repositories\ProductRepository;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;

class UpdateProductAction extends ParentAction
{
    public function __construct(
        private readonly UpdateProductTask $updateProductTask,
        private readonly UpdateProductImagesTask $updateProductImagesTask,
        private readonly UpdateProductPricesTask $updateProductPricesTask,
        private readonly UpdateProductAttributesTask $updateProductAttributesTask,
    ) {
    }

    public function run(UpdateProductRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $productId = $request->id;

            // 1. Cập nhật thông tin cơ bản
            $productData = $request->only([
                'name',
                'category_id',
                'description',
            ]);
            
            if (!empty($productData)) {
                $product = $this->updateProductTask->run($productId, $productData);
            }

            // 2. Cập nhật prices
            if ($request->has('prices')) {
                $this->updateProductPricesTask->run($productId, $request->input('prices'));
            }

            // 3. Cập nhật attributes
            if ($request->has('attributes')) {
                $this->updateProductAttributesTask->run($productId, $request->input('attributes'));
            }

            // 4. Cập nhật images (Upload lên GCS)
            if ($request->hasFile('images')) {
                $this->updateProductImagesTask->run($productId, $request->file('images'));
            }

            // Retrieve updated product
            return app(ProductRepository::class)->find($productId);
        });
    }
}
