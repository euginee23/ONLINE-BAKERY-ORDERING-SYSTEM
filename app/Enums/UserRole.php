<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Customer = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Customer => 'Customer',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Admin => 'amber',
            self::Customer => 'zinc',
        };
    }
}
