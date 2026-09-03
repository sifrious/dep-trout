<?php

declare(strict_types=1);

namespace Sifrious\Trout\Contracts;

use InvalidArgumentException;

final readonly class PayloadReference
{
    public function __construct(
        public string $kind,
        public string $uri,
        public ?string $checksum = null,
    ) {
        if ($this->kind === '') {
            throw new InvalidArgumentException('Payload reference kind must not be empty.');
        }

        if ($this->uri === '') {
            throw new InvalidArgumentException('Payload reference URI must not be empty.');
        }
    }

    /**
     * @param array{kind: string, uri: string, checksum?: string|null} $input
     */
    public static function fromArray(array $input): self
    {
        return new self(
            kind: $input['kind'],
            uri: $input['uri'],
            checksum: $input['checksum'] ?? null,
        );
    }
}
