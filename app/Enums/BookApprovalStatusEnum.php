<?php

namespace App\Enums;

enum BookApprovalStatusEnum: int
{
    case APPROVED = 1; // approved by admin
    case PENDING = 2;  // waiting for admin review
    case REJECTED = 3; // rejected by admin

    public function label(): string
    {
        return match ($this) {
            self::APPROVED => 'approved',
            self::PENDING => 'pending',
            self::REJECTED => 'rejected',
        };
    }
}