<?php

declare(strict_types=1);

namespace Sifrious\Trout\Tests;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sifrious\Trout\Contracts\ApprovalState;
use Sifrious\Trout\Contracts\Cancellation;
use Sifrious\Trout\Contracts\DeliverySchedule;
use Sifrious\Trout\Contracts\DeliveryState;
use Sifrious\Trout\Contracts\DeliveryTransition;
use Sifrious\Trout\Contracts\RetryPolicy;

final class LifecycleContractsTest extends TestCase
{
    #[Test]
    public function it_publishes_an_explicit_delivery_state_transition_table(): void
    {
        self::assertSame(
            [
                'pending_approval' => ['approved', 'canceled'],
                'approved' => ['scheduled', 'dispatching', 'canceled'],
                'scheduled' => ['approved', 'dispatching', 'canceled'],
                'dispatching' => ['accepted', 'failed'],
                'accepted' => [],
                'failed' => ['dispatching', 'canceled'],
                'canceled' => [],
            ],
            DeliveryState::transitionTable(),
        );
    }

    #[Test]
    public function it_requires_approval_before_dispatch(): void
    {
        self::assertFalse(DeliveryState::PendingApproval->canTransitionTo(DeliveryState::Dispatching));
        self::assertTrue(DeliveryState::Approved->canTransitionTo(DeliveryState::Dispatching));
    }

    #[Test]
    public function it_validates_actor_attributed_delivery_transitions(): void
    {
        $transition = new DeliveryTransition(
            fromState: DeliveryState::Approved,
            toState: DeliveryState::Scheduled,
            actorIdentity: 'user:42',
            actorType: 'user',
            reason: 'defer_until_business_hours',
            transitionedAt: new DateTimeImmutable('2026-09-03T12:00:00Z'),
        );

        self::assertSame('approved', $transition->fromState->value);
        self::assertSame('scheduled', $transition->toState->value);
        self::assertSame('user:42', $transition->actorIdentity);
        self::assertSame('user', $transition->actorType);
    }

    #[Test]
    public function it_rejects_invalid_delivery_transitions(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid delivery state transition from "pending_approval" to "dispatching".');

        new DeliveryTransition(
            fromState: DeliveryState::PendingApproval,
            toState: DeliveryState::Dispatching,
            actorIdentity: 'system:dispatcher',
        );
    }

    #[Test]
    public function it_supports_schedule_and_unschedule_operations(): void
    {
        $unscheduled = DeliverySchedule::unscheduled('request-100');
        $scheduled = $unscheduled->schedule(new DateTimeImmutable('2026-09-04T09:00:00Z'), 'user:scheduler');
        $resumed = $scheduled->unschedule('user:scheduler');

        self::assertFalse($unscheduled->isScheduled());
        self::assertTrue($scheduled->isScheduled());
        self::assertSame('user:scheduler', $scheduled->scheduledByActor);
        self::assertFalse($resumed->isScheduled());
        self::assertSame('user:scheduler', $resumed->unscheduledByActor);
    }

    #[Test]
    public function it_exposes_approval_state_dispatch_eligibility(): void
    {
        self::assertFalse(ApprovalState::Pending->allowsDispatch());
        self::assertFalse(ApprovalState::Rejected->allowsDispatch());
        self::assertTrue(ApprovalState::Approved->allowsDispatch());
    }

    #[Test]
    public function it_distinguishes_non_cancelable_accepted_work(): void
    {
        $acceptedCancellation = new Cancellation('request-accepted', DeliveryState::Accepted);
        $approvedCancellation = new Cancellation('request-approved', DeliveryState::Approved);

        self::assertFalse($acceptedCancellation->isCancelable());
        self::assertSame('accepted_work_cannot_be_canceled', $acceptedCancellation->cannotCancelReason());

        self::assertTrue($approvedCancellation->isCancelable());
        self::assertNull($approvedCancellation->cannotCancelReason());
    }

    #[Test]
    public function it_supports_safe_retry_eligibility_and_preserves_prior_attempts(): void
    {
        $policy = new RetryPolicy(maxAttempts: 3, priorAttemptIdentities: ['attempt-1']);
        $afterSecondAttempt = $policy->withRecordedAttempt('attempt-2');

        self::assertTrue($policy->canRetry(DeliveryState::Failed));
        self::assertSame(['attempt-1'], $policy->priorAttemptIdentities);
        self::assertSame(['attempt-1', 'attempt-2'], $afterSecondAttempt->priorAttemptIdentities);
        self::assertTrue($afterSecondAttempt->canRetry(DeliveryState::Failed));
        self::assertFalse($afterSecondAttempt->canRetry(DeliveryState::Accepted));
    }

    #[Test]
    public function it_stops_retries_after_max_attempts(): void
    {
        $policy = new RetryPolicy(
            maxAttempts: 2,
            priorAttemptIdentities: ['attempt-1', 'attempt-2'],
        );

        self::assertFalse($policy->canRetry(DeliveryState::Failed));
    }
}
