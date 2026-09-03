<?php

declare(strict_types=1);

namespace Sifrious\Trout\Contracts;

enum ApprovalState: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function allowsDispatch(): bool
    {
        return $this === self::Approved;
    }
}
