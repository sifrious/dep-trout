<?php

declare(strict_types=1);

namespace Sifrious\Trout\Contracts;

use InvalidArgumentException;

final readonly class RetryPolicy
{
    /**
     * @param list<string> $priorAttemptIdentities
     */
    public function __construct(
        public int $maxAttempts,
        public array $priorAttemptIdentities = [],
    ) {
        if ($this->maxAttempts < 1) {
            throw new InvalidArgumentException('Retry policy max attempts must be at least 1.');
        }

        foreach ($this->priorAttemptIdentities as $attemptIdentity) {
            if ($attemptIdentity === '') {
                throw new InvalidArgumentException('Retry policy attempt identities must not be empty.');
            }
        }
    }

    public function canRetry(DeliveryState $state): bool
    {
        if ($state !== DeliveryState::Failed) {
            return false;
        }

        return count($this->priorAttemptIdentities) < $this->maxAttempts;
    }

    public function withRecordedAttempt(string $attemptIdentity): self
    {
        if ($attemptIdentity === '') {
            throw new InvalidArgumentException('Retry policy attempt identity must not be empty.');
        }

        $updatedAttempts = $this->priorAttemptIdentities;
        $updatedAttempts[] = $attemptIdentity;

        return new self(
            maxAttempts: $this->maxAttempts,
            priorAttemptIdentities: $updatedAttempts,
        );
    }
}
