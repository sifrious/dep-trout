<?php

declare(strict_types=1);

namespace Sifrious\Trout\Contracts;

enum DeliveryResultStatus: string
{
    case Accepted = 'accepted';
    case Refused = 'refused';
    case Failed = 'failed';
    case Canceled = 'canceled';
}
