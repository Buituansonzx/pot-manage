<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $categoriesData = [
            'Chậu sứ' => [
                'description' => 'Các loại chậu trồng cây bằng gốm sứ nhập khẩu',
                'children' => ['Chậu tử sa', 'Chậu men rạn', 'Chậu mini để bàn', 'Chậu men hoả biến']
            ],
            'Ang cây' => [
                'description' => 'Các loại ang trồng cây hàng nhập',
                'children' => ['Ang tử sa', 'Ang men rạn', 'Ang hoạ tiết cổ', 'Ang chữ nhật']
            ],
            'Tượng' => [
                'description' => 'Các loại tượng trang trí tiểu cảnh',
                'children' => ['Tượng Phật', 'Tượng tiểu đồng', 'Tượng thú phong thuỷ', 'Tượng Tôn Ngộ Không']
            ],
            'Đôn' => [
                'description' => 'Các loại đôn chậu, đôn kê cây cảnh',
                'children' => ['Đôn gỗ', 'Đôn sứ', 'Đôn đá nhân tạo']
            ]
        ];

        foreach ($categoriesData as $parentName => $data) {
            // Thêm danh mục cha và lấy ID
            $parentId = DB::table('categories')->insertGetId([
                'parent_id' => null,
                'name' => $parentName,
                'description' => $data['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Chuẩn bị dữ liệu danh mục con
            $children = [];
            foreach ($data['children'] as $childName) {
                $children[] = [
                    'parent_id' => $parentId,
                    'name' => $childName,
                    'description' => null, // Không cần mô tả quá chi tiết cho DM con lúc này
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Thêm danh sách danh mục con vào bảng
            if (!empty($children)) {
                DB::table('categories')->insert($children);
            }
        }
    }
}
