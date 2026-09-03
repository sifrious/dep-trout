<?php

declare(strict_types=1);

namespace Sifrious\Trout\Tests;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sifrious\Trout\Contracts\DeliveryReceipt;
use Sifrious\Trout\Contracts\DeliveryRequest;
use Sifrious\Trout\Contracts\DeliveryResult;
use Sifrious\Trout\Contracts\DeliveryResultStatus;
use Sifrious\Trout\Contracts\Destination;
use Sifrious\Trout\Contracts\Edition;
use Sifrious\Trout\Contracts\PayloadReference;

final class DeliveryContractsTest extends TestCase
{
    #[Test]
    public function it_creates_an_edition_from_fixture_data(): void
    {
        $edition = Edition::fromArray($this->fixture('email-edition-v1.json'));

        self::assertSame('edition:email:welcome:v1', $edition->identity);
        self::assertSame('v1', $edition->version);
        self::assertSame('approval://catalogue/welcome-email@2026-09-03T00:00:00Z', $edition->provenance);
        self::assertCount(1, $edition->payloadReferences);
        self::assertSame('payload://email/welcome/v1.mime', $edition->payloadReferences[0]->uri);
    }

    #[Test]
    public function it_creates_telegram_edition_fixture_with_payload_reference(): void
    {
        $edition = Edition::fromArray($this->fixture('telegram-edition-v1.json'));

        self::assertSame('edition:telegram:incident-alert:v1', $edition->identity);
        self::assertSame('telegram-markdown', $edition->payloadReferences[0]->kind);
    }

    #[Test]
    public function it_builds_a_provider_neutral_delivery_request(): void
    {
        $request = new DeliveryRequest(
            identity: 'delivery-request-001',
            edition: new Edition(
                identity: 'edition:email:welcome:v1',
                version: 'v1',
                provenance: 'approval://catalogue/welcome-email@2026-09-03T00:00:00Z',
                payloadReferences: [new PayloadReference('mime', 'payload://email/welcome/v1.mime')],
            ),
            destination: new Destination(
                identity: 'destination:user:42',
                capability: 'email',
            ),
            correlationReference: 'corr-42',
            idempotencyKey: 'idempotency-42',
        );

        self::assertSame('delivery-request-001', $request->identity);
        self::assertSame('destination:user:42', $request->destination->identity);
        self::assertSame('email', $request->destination->capability);
    }

    #[Test]
    public function it_represents_provider_neutral_delivery_result_states(): void
    {
        $accepted = new DeliveryResult('request-1', DeliveryResultStatus::Accepted);
        $refused = new DeliveryResult('request-2', DeliveryResultStatus::Refused, 'destination_capability_mismatch');
        $failed = new DeliveryResult('request-3', DeliveryResultStatus::Failed, 'transport_unavailable');
        $canceled = new DeliveryResult('request-4', DeliveryResultStatus::Canceled, 'request_canceled');

        self::assertSame('accepted', $accepted->status->value);
        self::assertSame('refused', $refused->status->value);
        self::assertSame('failed', $failed->status->value);
        self::assertSame('canceled', $canceled->status->value);
    }

    #[Test]
    public function it_captures_a_provider_acknowledgement_without_provider_policy(): void
    {
        $receipt = new DeliveryReceipt(
            requestIdentity: 'request-1',
            acknowledgementIdentity: 'ack-123',
            acknowledgedAt: new DateTimeImmutable('2026-09-03T11:59:00Z'),
            traceReference: 'trace-abc',
            metadata: ['providerMessageId' => 'provider-opaque-999'],
        );

        self::assertSame('request-1', $receipt->requestIdentity);
        self::assertSame('ack-123', $receipt->acknowledgementIdentity);
        self::assertSame('trace-abc', $receipt->traceReference);
        self::assertSame('provider-opaque-999', $receipt->metadata['providerMessageId']);
    }

    #[Test]
    public function it_rejects_missing_edition_payload_references(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Edition must include at least one payload reference.');

        new Edition(
            identity: 'edition:email:welcome:v1',
            version: 'v1',
            provenance: 'approval://catalogue/welcome-email@2026-09-03T00:00:00Z',
            payloadReferences: [],
        );
    }

    /**
     * @return array{
     *     identity: string,
     *     version: string,
     *     provenance: string,
     *     payloadReferences: list<array{kind: string, uri: string, checksum?: string}>
     * }
     */
    private function fixture(string $filename): array
    {
        $path = __DIR__ . '/../fixtures/editions/' . $filename;
        $json = file_get_contents($path);

        if ($json === false) {
            self::fail('Unable to read fixture: ' . $filename);
        }

        /** @var array{
         *     identity: string,
         *     version: string,
         *     provenance: string,
         *     payloadReferences: list<array{kind: string, uri: string, checksum?: string}>
         * } $decoded
         */
        $decoded = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

        return $decoded;
    }
}
