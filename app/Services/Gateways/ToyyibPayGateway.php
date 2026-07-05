<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGateway;
use App\Services\ToyyibPayService;

class ToyyibPayGateway implements PaymentGateway
{
    public function __construct(private ToyyibPayService $toyyibpay) {}

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
        $billCode = $this->toyyibpay->createBill(
            amountCents: $amountCents,
            externalReference: $externalReference,
            description: $description,
            returnUrl: $returnUrl,
            callbackUrl: $callbackUrl,
            payorName: $payorName,
            payorEmail: $payorEmail,
            payorPhone: $payorPhone,
        );

        return [
            'checkout_url' => $this->toyyibpay->getCheckoutUrl($billCode),
            'reference' => $billCode,
        ];
    }

    public function verifyCallback(array $callbackData): bool
    {
        $status = $callbackData['status'] ?? null;
        $orderId = $callbackData['order_id'] ?? null;
        $refNo = $callbackData['refno'] ?? null;
        $hash = $callbackData['hash'] ?? null;

        if (!$status || !$orderId || !$refNo || !$hash) {
            return false;
        }

        return $this->toyyibpay->verifyCallbackHash($status, $orderId, $refNo, $hash);
    }

    public function getTransaction(string $transactionId): ?array
    {
        try {
            // billpaymentStatus=1 filters server-side to successful transactions only.
            $transactions = $this->toyyibpay->getBillTransactions($transactionId, paymentStatus: 1);

            foreach ($transactions as $txn) {
                if (($txn['billpaymentStatus'] ?? null) === '1') {
                    return $txn;
                }
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('ToyyibPay getTransaction error', ['bill_code' => $transactionId, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function supportsRecurring(): bool
    {
        return false; // ToyyibPay doesn't natively support recurring billing; we implement it via scheduled jobs
    }

    public function getName(): string
    {
        return 'toyyibpay';
    }
}
