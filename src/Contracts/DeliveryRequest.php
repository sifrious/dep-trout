<?php

declare(strict_types=1);

namespace Sifrious\Trout\Contracts;

use InvalidArgumentException;

final readonly class DeliveryRequest
{
    public function __construct(
        public string $identity,
        public Edition $edition,
        public Destination $destination,
        public string $correlationReference,
        public string $idempotencyKey,
    ) {
        if ($this->identity === '') {
            throw new InvalidArgumentException('Delivery request identity must not be empty.');
        }

        if ($this->correlationReference === '') {
            throw new InvalidArgumentException('Delivery request correlation reference must not be empty.');
        }

        if ($this->idempotencyKey === '') {
            throw new InvalidArgumentException('Delivery request idempotency key must not be empty.');
        }
    }
}
