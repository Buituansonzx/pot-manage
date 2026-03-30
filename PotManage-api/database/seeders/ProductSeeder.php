<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        
        // Lấy tất cả danh mục
        $categories = DB::table('categories')->get();
        // Lọc lấy danh mục con (hoặc lấy tất nếu không có danh mục con)
        $childCategories = $categories->whereNotNull('parent_id');
        if ($childCategories->isEmpty()) {
            $childCategories = $categories;
        }

        if ($childCategories->isEmpty()) {
            $catId = DB::table('categories')->insertGetId([
                'parent_id' => null,
                'name' => 'Danh mục chung',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $childCategories = collect([ (object)['id' => $catId, 'name' => 'Danh mục chung'] ]);
        }

        // Tạo 30 sản phẩm từ các danh mục được chọn ngẫu nhiên
        for ($i = 1; $i <= 30; $i++) {
            $category = $childCategories->random();
            $catName = mb_strtolower($category->name);

            // Sinh dữ liệu phù hợp theo đặc thù của danh mục
            if (str_contains($catName, 'chậu')) {
                $names = ['Chậu bonsai cao cấp', 'Chậu cây cảnh mini', 'Chậu trồng cây dáng đứng', 'Chậu chữ nhật đắp nổi', 'Chậu tròn phong thủy', 'Chậu hoa lan', 'Chậu lục giác men rạn'];
                $units = ['cái', 'bộ'];
                $materials = ['Tử sa nguyên khoáng', 'Gốm men rạn', 'Sứ cảnh đức trấn', 'Đất nung cao cấp'];
            } elseif (str_contains($catName, 'ang')) {
                $names = ['Ang nước tiểu cảnh', 'Ang bonsai mini', 'Ang sen đá', 'Ang chữ nhật trồng cây', 'Ang bầu dục mỏng', 'Ang vuông 4 chân', 'Ang thả hoa súng'];
                $units = ['ang', 'cái'];
                $materials = ['Gốm tử sa', 'Đất nung', 'Sứ men ngọc', 'Sứ men xanh'];
            } elseif (str_contains($catName, 'tượng')) {
                $names = ['Tượng Phật Di Lặc', 'Tượng Tôn Ngộ Không ngồi thiền', 'Tượng tiểu đồng', 'Tượng nghê phong thủy', 'Tượng hươu trang trí', 'Tượng Đạt Ma sư tổ', 'Tượng cá chép'];
                $units = ['bức', 'con'];
                $materials = ['Gốm sứ', 'Bột đá', 'Composite', 'Sứ trắng'];
            } elseif (str_contains($catName, 'đôn')) {
                $names = ['Đôn kỷ trúc', 'Đôn sứ kê chậu', 'Đôn đá điêu khắc', 'Đôn lục giác', 'Đôn voi gốm', 'Đôn trống', 'Đôn giả cổ'];
                $units = ['chiếc', 'cái'];
                $materials = ['Gỗ hương', 'Sứ Giang Tây', 'Bột đá nhân tạo'];
            } else {
                $names = ['Sản phẩm trang trí', 'Phụ kiện tiểu cảnh', 'Bộ dụng cụ làm vườn'];
                $units = ['cái', 'bộ'];
                $materials = ['Tổng hợp'];
            }

            $colors = ['Xanh ngọc', 'Nâu đỏ tía', 'Trắng sữa', 'Xanh lam', 'Đen xám mờ', 'Màu gốm mộc'];
            $sizes = ['15x10cm', '20x15cm', '30x20cm', '40x25cm', '50x30cm', 'D15 x H10cm', 'D30 x H25cm'];

            $productName = $names[array_rand($names)] . ' ' . rand(100, 999);
            $slug = Str::slug($productName) . '-' . Str::random(5);
            $unit = $units[array_rand($units)];
            $material = $materials[array_rand($materials)];
            $color = $colors[array_rand($colors)];
            $size = $sizes[array_rand($sizes)];

            $description = "Sản phẩm {$productName} với chất liệu {$material} bền bỉ, tính thẩm mỹ cao. " .
                "Hàng nhập khẩu tuyển chọn nguyên kiện từ Trung Quốc, phù hợp trang trí không gian sân vườn, tiểu cảnh nội ngoại thất.";

            // 1. Insert Products
            $productId = DB::table('products')->insertGetId([
                'category_id' => $category->id,
                'sku' => 'TQ-' . strtoupper(Str::random(3)) . '-' . rand(1000, 9999) . '-' . $i,
                'name' => $productName,
                'slug' => $slug,
                'thumbnail' => "https://picsum.photos/seed/" . rand(1, 1000) . "/400/400",
                'description' => $description,
                'unit' => $unit,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // 2. Insert Product Prices
            $costVND = rand(50, 500) * 1000; 
            $floorPrice = $costVND * 1.3; 
            $suggestedRetail = $floorPrice * (rand(15, 25) / 10); 
            $minRetail = $suggestedRetail * 0.9; 

            DB::table('product_prices')->insert([
                'product_id' => $productId,
                'floor_price' => $floorPrice,
                'suggested_retail_price' => $suggestedRetail,
                'min_retail_price' => $minRetail,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // 3. Insert Product Attributes
            $attributesToInsert = [
                ['Chất liệu', $material],
                ['Màu sắc', $color],
                ['Kích thước', $size],
            ];
            
            // Đối với tượng có thể thêm "Ý nghĩa"
            if (str_contains($catName, 'tượng')) {
                $meanings = ['Mang lại bình an', 'Chiêu tài lộc', 'Trấn trạch', 'May mắn trong công việc'];
                $attributesToInsert[] = ['Ý nghĩa phong thủy', $meanings[array_rand($meanings)]];
            }

            foreach ($attributesToInsert as $attr) {
                DB::table('product_attributes')->insert([
                    'product_id' => $productId,
                    'attribute_name' => $attr[0],
                    'attribute_value' => $attr[1],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // 4. Insert Product Batches
            $quantityImported = rand(20, 150);
            $unitCostYuan = $costVND / 3500; 
            DB::table('product_batches')->insert([
                'product_id' => $productId,
                'name' => 'LOHANG-HQ-' . date('Ym') . '-' . strtoupper(Str::random(3)),
                'quantity_imported' => $quantityImported,
                'quantity_remaining' => rand(5, $quantityImported),
                'unit_cost_yuan' => round($unitCostYuan, 2),
                'unit_cost_vnd' => $costVND,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
