<?php

declare(strict_types=1);

namespace Sifrious\Trout\Contracts;

use InvalidArgumentException;

final readonly class DeliveryResult
{
    public function __construct(
        public string $requestIdentity,
        public DeliveryResultStatus $status,
        public ?string $reason = null,
    ) {
        if ($this->requestIdentity === '') {
            throw new InvalidArgumentException('Delivery result request identity must not be empty.');
        }
    }
}
