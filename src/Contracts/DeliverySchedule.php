<?php

declare(strict_types=1);

namespace Sifrious\Trout\Contracts;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class DeliverySchedule
{
    public function __construct(
        public string $requestIdentity,
        public ?DateTimeImmutable $scheduledFor = null,
        public ?string $scheduledByActor = null,
        public ?string $unscheduledByActor = null,
    ) {
        if ($this->requestIdentity === '') {
            throw new InvalidArgumentException('Delivery schedule request identity must not be empty.');
        }

        if ($this->scheduledFor === null && $this->scheduledByActor !== null) {
            throw new InvalidArgumentException('Scheduled by actor must be null when no schedule is set.');
        }

        if ($this->scheduledFor !== null && $this->scheduledByActor === null) {
            throw new InvalidArgumentException('Scheduled by actor must be set when a schedule exists.');
        }
    }

    public static function unscheduled(string $requestIdentity): self
    {
        return new self(requestIdentity: $requestIdentity);
    }

    public function isScheduled(): bool
    {
        return $this->scheduledFor !== null;
    }

    public function schedule(DateTimeImmutable $scheduledFor, string $actorIdentity): self
    {
        if ($actorIdentity === '') {
            throw new InvalidArgumentException('Scheduling actor identity must not be empty.');
        }

        return new self(
            requestIdentity: $this->requestIdentity,
            scheduledFor: $scheduledFor,
            scheduledByActor: $actorIdentity,
            unscheduledByActor: null,
        );
    }

    public function unschedule(string $actorIdentity): self
    {
        if ($actorIdentity === '') {
            throw new InvalidArgumentException('Unscheduling actor identity must not be empty.');
        }

        if (!$this->isScheduled()) {
            throw new InvalidArgumentException('Cannot unschedule a request that is not currently scheduled.');
        }

        return new self(
            requestIdentity: $this->requestIdentity,
            scheduledFor: null,
            scheduledByActor: null,
            unscheduledByActor: $actorIdentity,
        );
    }
}
