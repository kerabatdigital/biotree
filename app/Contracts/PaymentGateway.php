<?php

namespace App\Contracts;

interface PaymentGateway
{
    /**
     * Create a payment session.
     *
     * @return array{checkout_url: string, reference: ?string} The hosted checkout URL
     *         and the gateway's own bill/session reference (used later to verify the
     *         transaction). `reference` may be null for gateways that don't expose one.
     */
    public function createPaymentSession(
        string $externalReference,
        int $amountCents,
        string $description,
        string $returnUrl,
        string $callbackUrl,
        string $payorName,
        string $payorEmail,
        string $payorPhone,
    ): array;

    /**
     * Verify callback authenticity using the gateway's signature method.
     */
    public function verifyCallback(array $callbackData): bool;

    /**
     * Get transaction details from gateway API.
     * Returns null if transaction not found or gateway doesn't support it.
     */
    public function getTransaction(string $transactionId): ?array;

    /**
     * Whether this gateway supports recurring/auto-renewal billing.
     */
    public function supportsRecurring(): bool;

    /**
     * Get the gateway name for storage/logging.
     */
    public function getName(): string;
}
