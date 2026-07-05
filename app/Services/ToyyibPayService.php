<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ToyyibPayService
{
    private string $baseUrl;
    private string $secretKey;
    private string $categoryCode;

    private string $host;

    public function __construct()
    {
        $this->host = config('services.toyyibpay.production') ? 'toyyibpay.com' : 'dev.toyyibpay.com';
        $this->baseUrl = "https://{$this->host}/index.php/api/";
        $this->secretKey = config('services.toyyibpay.secret_key') ?? '';
        $this->categoryCode = config('services.toyyibpay.category_code') ?? '';
    }

    /**
     * Create a bill for checkout.
     * Amount should be in cents (100 = RM 1.00).
     * Returns bill_code or throws exception on failure.
     */
    public function createBill(
        int $amountCents,
        string $externalReference,
        string $description,
        string $returnUrl,
        string $callbackUrl,
        string $payorName,
        string $payorEmail,
        string $payorPhone = '0000000000',
    ): string {
        $response = Http::asForm()->post("{$this->baseUrl}createBill", [
            'userSecretKey' => $this->secretKey,
            'categoryCode' => $this->categoryCode,
            'billName' => 'BioTree Pro Upgrade',
            'billDescription' => $description,
            'billPriceSetting' => 1, // Fixed amount
            'billPayorInfo' => 1,
            'billAmount' => $amountCents,
            'billExternalReferenceNo' => $externalReference,
            'billReturnUrl' => $returnUrl,
            'billCallbackUrl' => $callbackUrl,
            'billContentEmail' => 'no', // Don't email invoice
            'billTo' => $payorName,
            'billEmail' => $payorEmail,
            'billPhone' => $payorPhone,
        ]);

        $result = $response->json();

        // Success responses are wrapped in a JSON array: [{"BillCode": "..."}].
        // Error responses are either a JSON object with a Message, or a bare string
        // like "[CATEGORY-NOT-MATCH]".
        $billCode = $result[0]['BillCode'] ?? null;

        if (! $billCode) {
            $message = is_array($result) ? ($result['msg'] ?? $result['Message'] ?? null) : $response->body();
            throw new \Exception('Failed to create ToyyibPay bill: ' . ($message ?? 'Unknown error'));
        }

        return $billCode;
    }

    /**
     * Get bill transactions to verify payment was made.
     * Success is a bare JSON array of transaction objects. An unpaid/unknown bill
     * returns the plain-text body "No data found!" (not JSON) — that's a normal
     * "nothing yet" result, not an error, so it resolves to an empty array.
     */
    public function getBillTransactions(string $billCode, ?int $paymentStatus = null): array
    {
        $response = Http::asForm()->post("{$this->baseUrl}getBillTransactions", array_filter([
            'userSecretKey' => $this->secretKey,
            'billCode' => $billCode,
            'billpaymentStatus' => $paymentStatus,
        ], fn ($v) => $v !== null));

        $result = $response->json();

        if (! is_array($result) || array_is_list($result) === false) {
            return [];
        }

        return $result;
    }

    /**
     * Verify callback hash using MD5(userSecretKey + status + order_id + refno + "ok").
     * Returns true if hash matches.
     */
    public function verifyCallbackHash(string $status, string $orderId, string $refNo, string $providedHash): bool
    {
        $expectedHash = md5($this->secretKey . $status . $orderId . $refNo . 'ok');
        return hash_equals($expectedHash, $providedHash);
    }

    /**
     * Get bill code for a checkout URL.
     */
    public function getCheckoutUrl(string $billCode): string
    {
        return "https://{$this->host}/{$billCode}";
    }
}
