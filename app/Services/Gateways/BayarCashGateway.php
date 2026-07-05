<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGateway;

class BayarCashGateway implements PaymentGateway
{
    public function createPaymentSession(
        string $externalReference,
        int $amountCents,
        string $description,
        string $returnUrl,
        string $callbackUrl,
        string $payorName,
        string $payorEmail,
    ): array {
        throw new \Exception('BayarCash gateway not yet implemented');
    }

    public function verifyCallback(array $callbackData): bool
    {
        throw new \Exception('BayarCash gateway not yet implemented');
    }

    public function getTransaction(string $transactionId): ?array
    {
        throw new \Exception('BayarCash gateway not yet implemented');
    }

    public function supportsRecurring(): bool
    {
        return false;
    }

    public function getName(): string
    {
        return 'bayarcash';
    }
}
