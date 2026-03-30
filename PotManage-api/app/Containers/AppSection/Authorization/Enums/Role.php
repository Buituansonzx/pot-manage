<?php

namespace App\Containers\AppSection\Authorization\Enums;

enum Role: string
{
    case SUPER_ADMIN = 'admin';
    case SALES = 'nhân viên bán hàng';
    case COLLABORATOR = 'cộng tác viên';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::SALES => 'Nhân viên bán hàng',
            self::COLLABORATOR => 'Cộng tác viên',
        };
    }

    public function code(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'admin',
            self::SALES => 'sales',
            self::COLLABORATOR => 'collaborator',
        };
    }
}
