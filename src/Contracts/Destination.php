<?php

declare(strict_types=1);

namespace Sifrious\Trout\Contracts;

use InvalidArgumentException;

final readonly class Destination
{
    public function __construct(
        public string $identity,
        public string $capability,
    ) {
        if ($this->identity === '') {
            throw new InvalidArgumentException('Destination identity must not be empty.');
        }

        if ($this->capability === '') {
            throw new InvalidArgumentException('Destination capability must not be empty.');
        }
    }
}
