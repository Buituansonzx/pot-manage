<?php

namespace App\Containers\AdminSection\Product\Tasks;

use App\Containers\SharedSection\Product\Models\ProductImage;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Support\Facades\Storage;
use Exception;

class UpdateProductImagesTask extends ParentTask
{
    public function run(int $productId, array $images)
    {
        try {
            // Tuỳ vào logic, có thể xoá ảnh cũ trên GCS rồi thay ảnh mới,
            // hoặc giữ lại ảnh cũ. Ở đây theo chuẩn update mảng mới hoàn toàn: xoá cũ, thay mới.
            $oldImages = ProductImage::where('product_id', $productId)->get();
            foreach ($oldImages as $oldImage) {
                // Xoá trên GCS (tuỳ chọn)
                // if ($oldImage->image_url) {
                //    $path = parse_url($oldImage->image_url, PHP_URL_PATH);
                //    Storage::disk('gcs')->delete($path);
                // }
                $oldImage->delete();
            }
            
            $createdImages = [];
            foreach ($images as $index => $imageFile) {
                $url = upload_to_gcs($imageFile, 'products');
                if (!$url) continue;

                // Mặc định ảnh đầu tiên là primary
                $isPrimary = ($index === 0);

                $createdImages[] = ProductImage::create([
                    'product_id' => $productId,
                    'image_url' => $url,
                    'is_primary' => $isPrimary,
                    'sort_order' => $index,
                ]);
            }

            return $createdImages;

        } catch (Exception $exception) {
            throw new Exception('Could not upload and update Product Images.' . $exception->getMessage());
        }
    }
}
