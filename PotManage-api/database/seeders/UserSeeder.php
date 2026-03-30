<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Containers\AppSection\User\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Tuấn Sơn',
                'email' => 'tuanson@gmail.com',
                'password' => '123456',
            ],
            [
                'name' => 'Nguyễn Văn A',
                'email' => 'nguyenvana@gmail.com',
                'password' => '123456',
            ],
            [
                'name' => 'Trần Thị B',
                'email' => 'tranthib@gmail.com',
                'password' => '123456',
            ],
            [
                'name' => 'Lê Văn C',
                'email' => 'levanc@gmail.com',
                'password' => '123456',
            ],
            [
                'name' => 'Phạm Thị D',
                'email' => 'phamthid@gmail.com',
                'password' => '123456',
            ],
            [
                'name' => 'Hoàng Văn E',
                'email' => 'hoangvane@gmail.com',
                'password' => '123456',
            ],
            [
                'name' => 'Vũ Thị F',
                'email' => 'vuthif@gmail.com',
                'password' => '123456',
            ],
            [
                'name' => 'Đặng Văn G',
                'email' => 'dangvang@gmail.com',
                'password' => '123456',
            ],
            [
                'name' => 'Bùi Thị H',
                'email' => 'buithih@gmail.com',
                'password' => '123456',
            ],
            [
                'name' => 'Đỗ Văn I',
                'email' => 'dovani@gmail.com',
                'password' => '123456',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                [
                    'email' => $userData['email'],
                    "email_verified_at" => now(),
                    "password" => Hash::make($userData['password'])
                ],
                $userData
            );
        }
    }
}
