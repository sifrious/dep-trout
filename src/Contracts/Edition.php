<?php

declare(strict_types=1);

namespace Sifrious\Trout\Contracts;

use InvalidArgumentException;

final readonly class Edition
{
    /**
     * @param list<PayloadReference> $payloadReferences
     */
    public function __construct(
        public string $identity,
        public string $version,
        public string $provenance,
        public array $payloadReferences,
    ) {
        if ($this->identity === '') {
            throw new InvalidArgumentException('Edition identity must not be empty.');
        }

        if ($this->version === '') {
            throw new InvalidArgumentException('Edition version must not be empty.');
        }

        if ($this->provenance === '') {
            throw new InvalidArgumentException('Edition provenance must not be empty.');
        }

        if ($this->payloadReferences === []) {
            throw new InvalidArgumentException('Edition must include at least one payload reference.');
        }

        foreach ($this->payloadReferences as $payloadReference) {
            if (!$payloadReference instanceof PayloadReference) {
                throw new InvalidArgumentException('Edition payload references must be PayloadReference objects.');
            }
        }
    }

    /**
     * @param array{
     *   identity: string,
     *   version: string,
     *   provenance: string,
     *   payloadReferences: list<array{kind: string, uri: string, checksum?: string|null}>
     * } $input
     */
    public static function fromArray(array $input): self
    {
        $payloadReferences = [];

        foreach ($input['payloadReferences'] as $payloadReference) {
            $payloadReferences[] = PayloadReference::fromArray($payloadReference);
        }

        return new self(
            identity: $input['identity'],
            version: $input['version'],
            provenance: $input['provenance'],
            payloadReferences: $payloadReferences,
        );
    }
}
