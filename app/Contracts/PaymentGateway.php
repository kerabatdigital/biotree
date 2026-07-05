<?php

namespace App\Contracts;

interface PaymentGateway
{
    /**
     * Create a payment session and return a redirect URL for checkout.
     */
    public function createPaymentSession(
        string $externalReference,
        int $amountCents,
        string $description,
        string $returnUrl,
        string $callbackUrl,
    ): string;

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
