<?php

namespace App\Containers\AppSection\Authorization\Data\Seeders;

use App\Containers\AppSection\Authorization\Enums\Role;
use App\Containers\AppSection\Authorization\Tasks\CreateRoleTask;
use App\Ship\Parents\Seeders\Seeder as ParentSeeder;

final class AuthorizationSeeder_1 extends ParentSeeder
{
    public function run(CreateRoleTask $task): void
    {
        foreach (Role::cases() as $role) {
            $task->run(
                $role->value,
                $role->code(),
                $role->label(),
                $role->label(),
                'api',
            );
        }
    }
}
