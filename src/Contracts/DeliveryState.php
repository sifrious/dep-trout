<?php

declare(strict_types=1);

namespace Sifrious\Trout\Contracts;

enum DeliveryState: string
{
    case PendingApproval = 'pending_approval';
    case Approved = 'approved';
    case Scheduled = 'scheduled';
    case Dispatching = 'dispatching';
    case Accepted = 'accepted';
    case Failed = 'failed';
    case Canceled = 'canceled';

    /**
     * @return array<string, list<string>>
     */
    public static function transitionTable(): array
    {
        return [
            self::PendingApproval->value => [
                self::Approved->value,
                self::Canceled->value,
            ],
            self::Approved->value => [
                self::Scheduled->value,
                self::Dispatching->value,
                self::Canceled->value,
            ],
            self::Scheduled->value => [
                self::Approved->value,
                self::Dispatching->value,
                self::Canceled->value,
            ],
            self::Dispatching->value => [
                self::Accepted->value,
                self::Failed->value,
            ],
            self::Accepted->value => [],
            self::Failed->value => [
                self::Dispatching->value,
                self::Canceled->value,
            ],
            self::Canceled->value => [],
        ];
    }

    public function canTransitionTo(self $nextState): bool
    {
        return in_array($nextState->value, self::transitionTable()[$this->value], true);
    }
}
