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

## Out of scope in this package revision

- Email-specific payload objects and adapters (MME-1339).
- Lifecycle state/transition scheduling and retry policies (MME-1351).
- Attempt/retry outcome tracking in Funes (MME-1346).
- Provider SDK adapters, UI concerns, campaign dispatch, and canonical evidence
  ownership.

## Testing

```bash
composer test
```

Nothing is implemented yet.
