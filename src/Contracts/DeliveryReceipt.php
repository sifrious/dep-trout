<?php

declare(strict_types=1);

namespace Sifrious\Trout\Contracts;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class DeliveryReceipt
{
    /**
     * @param array<string, scalar|null> $metadata
     */
    public function __construct(
        public string $requestIdentity,
        public string $acknowledgementIdentity,
        public DateTimeImmutable $acknowledgedAt,
        public ?string $traceReference = null,
        public array $metadata = [],
    ) {
        if ($this->requestIdentity === '') {
            throw new InvalidArgumentException('Delivery receipt request identity must not be empty.');
        }

        if ($this->acknowledgementIdentity === '') {
            throw new InvalidArgumentException('Delivery receipt acknowledgement identity must not be empty.');
        }
    }
}
