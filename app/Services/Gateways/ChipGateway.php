<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGateway;

class ChipGateway implements PaymentGateway
{
    public function createPaymentSession(
        string $externalReference,
        int $amountCents,
        string $description,
        string $returnUrl,
        string $callbackUrl,
        string $payorName,
        string $payorEmail,
        string $payorPhone,
    ): array {
        throw new \Exception('CHIP gateway not yet implemented');
    }

    public function verifyCallback(array $callbackData): bool
    {
        throw new \Exception('CHIP gateway not yet implemented');
    }

    public function getTransaction(string $transactionId): ?array
    {
        throw new \Exception('CHIP gateway not yet implemented');
    }

    public function supportsRecurring(): bool
    {
        return false;
    }

    public function getName(): string
    {
        return 'chip';
    }
}
