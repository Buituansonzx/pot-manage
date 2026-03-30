<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Containers\AppSection\User\Models\User;
use App\Containers\AppSection\Authorization\Enums\Role;

class AssignRoleToUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salesEmails = [
            'nguyenvana@gmail.com',
            'tranthib@gmail.com',
            'levanc@gmail.com',
            'phamthid@gmail.com',
            'hoangvane@gmail.com',
        ];

        $users = User::with(['roles', 'permissions'])->get();

        foreach ($users as $user) {
            if ($user->email === 'tuanson@gmail.com') {
                $user->assignRole(Role::SUPER_ADMIN->value);
            } elseif (in_array($user->email, $salesEmails, true)) {
                $user->assignRole(Role::SALES->value);
            } else {
                if ($user->email !== 'admin@admin.com') {
                    $user->assignRole(Role::COLLABORATOR->value);
                }
            }
        }
    }
}
