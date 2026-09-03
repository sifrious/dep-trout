# Trout

Provider-neutral delivery contracts for approved content editions and delivery
destinations.

## Installation

```bash
composer require sifrious/trout
```

## Contracts published in MME-1326

- `Edition`: approved content identity, version, provenance, and immutable
  payload references.
- `Destination`: destination identity and declared delivery capability.
- `DeliveryRequest`: request identity, edition, destination, correlation
  reference, and idempotency key.
- `DeliveryResult`: provider-neutral result for accepted, refused, failed, or
  canceled outcomes.
- `DeliveryReceipt`: traceable provider acknowledgement details without exposing
  provider policy behavior to callers.

## Lifecycle contracts published in MME-1351

- `DeliveryState`: provider-neutral lifecycle states and an explicit, queryable
  transition table.
- `DeliveryTransition`: actor-attributed transition records with validation
  against allowed state changes.
- `ApprovalState`: approval lifecycle state and dispatch eligibility.
- `DeliverySchedule`: immutable schedule/unschedule state with actor
  attribution.
- `RetryPolicy`: safe retry eligibility and immutable prior-attempt
  preservation.
- `Cancellation`: cancellation eligibility classification, including accepted
  work that cannot be canceled.

### Delivery state transition table

| From state         | Allowed transitions                  |
|--------------------|--------------------------------------|
| `pending_approval` | `approved`, `canceled`               |
| `approved`         | `scheduled`, `dispatching`, `canceled` |
| `scheduled`        | `approved` (unschedule), `dispatching`, `canceled` |
| `dispatching`      | `accepted`, `failed`                 |
| `accepted`         | _(none)_                             |
| `failed`           | `dispatching` (retry), `canceled`    |
| `canceled`         | _(none)_                             |

## Fixture payloads

The package includes one fixture for each required edition family:

- `fixtures/editions/email-edition-v1.json`
- `fixtures/editions/telegram-edition-v1.json`

## Glossary

- **Edition**: a versioned, approved content identity with immutable references
  to pre-approved payload artifacts.
- **Provenance**: an approval or governance reference proving where an edition
  was approved.
- **Destination**: an identity that can receive deliveries and declares a
  channel capability (for example: `email` or `telegram`).
- **Correlation reference**: a caller-owned identifier used to correlate a
  request with upstream business context.
- **Idempotency key**: a deduplication key used to prevent duplicate delivery
  processing for equivalent requests.
- **Delivery result**: the coarse-grained provider-neutral outcome of processing
  a request.
- **Delivery receipt**: provider acknowledgement data that is traceable but does
  not leak provider-specific acceptance policy into the domain contract.
- **Approval state**: whether requested work is pending approval, approved for
  dispatch, or rejected.
- **Delivery state**: lifecycle status from approval gating through scheduling,
  dispatching, acceptance/failure, and cancellation.
- **Delivery transition**: explicit, actor-attributed, validated state change
  between two lifecycle states.
- **Delivery schedule**: immutable scheduling intent for deferred dispatch,
  including unscheduling attribution.
- **Retry policy**: max-attempt and prior-attempt history contract used to
  decide whether another retry is safe.
- **Cancellation**: contract describing cancelable vs non-cancelable lifecycle
  work (including accepted work that cannot be canceled).

## Out of scope in this package revision

- Email-specific payload objects and adapters (MME-1339).
- Attempt/retry outcome tracking in Funes (MME-1346).
- Provider SDK adapters, UI concerns, campaign dispatch, and canonical evidence
  ownership.

## Testing

```bash
composer test
```
