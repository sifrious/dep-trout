<?php

declare(strict_types=1);

namespace Sifrious\Trout\Contracts;

use InvalidArgumentException;

final readonly class Cancellation
{
    public function __construct(
        public string $requestIdentity,
        public DeliveryState $state,
    ) {
        if ($this->requestIdentity === '') {
            throw new InvalidArgumentException('Cancellation request identity must not be empty.');
        }
    }

    public function isCancelable(): bool
    {
        return !$this->isAcceptedWork() && $this->state !== DeliveryState::Canceled;
    }

    public function cannotCancelReason(): ?string
    {
        if ($this->isAcceptedWork()) {
            return 'accepted_work_cannot_be_canceled';
        }

        if ($this->state === DeliveryState::Canceled) {
            return 'work_is_already_canceled';
        }

        return null;
    }

    private function isAcceptedWork(): bool
    {
        return $this->state === DeliveryState::Accepted;
    }
}
