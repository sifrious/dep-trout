<?php

declare(strict_types=1);

namespace Sifrious\Trout\Contracts;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class DeliveryTransition
{
    public function __construct(
        public DeliveryState $fromState,
        public DeliveryState $toState,
        public string $actorIdentity,
        public string $actorType = 'system',
        public ?string $reason = null,
        public DateTimeImmutable $transitionedAt = new DateTimeImmutable(),
    ) {
        if ($this->actorIdentity === '') {
            throw new InvalidArgumentException('Delivery transition actor identity must not be empty.');
        }

        if ($this->actorType === '') {
            throw new InvalidArgumentException('Delivery transition actor type must not be empty.');
        }

        if (!$this->fromState->canTransitionTo($this->toState)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid delivery state transition from "%s" to "%s".',
                    $this->fromState->value,
                    $this->toState->value,
                ),
            );
        }
    }
}
