<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ToyyibPayService
{
    private string $baseUrl;
    private string $secretKey;
    private string $categoryCode;

    public function __construct()
    {
        $env = config('app.env') === 'production' ? 'toyyibpay.com' : 'dev.toyyibpay.com';
        $this->baseUrl = "https://{$env}/index.php/api/";
        $this->secretKey = config('services.toyyibpay.secret_key') ?? '';
        $this->categoryCode = config('services.toyyibpay.category_code') ?? '';
    }

    /**
     * Create a bill for checkout.
     * Amount should be in cents (100 = RM 1.00).
     * Returns bill_code or throws exception on failure.
     */
    public function createBill(int $amountCents, string $externalReference, string $description, string $returnUrl, string $callbackUrl): string
    {
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
        ]);

        $result = $response->json();

        if (! isset($result['BillCode'])) {
            throw new \Exception('Failed to create ToyyibPay bill: ' . ($result['Message'] ?? 'Unknown error'));
        }

        return $result['BillCode'];
    }

    /**
     * Get bill transactions to verify payment was made.
     * Returns array of transaction details or throws exception.
     */
    public function getBillTransactions(string $billCode): array
    {
        $response = Http::asForm()->post("{$this->baseUrl}getBillTransactions", [
            'userSecretKey' => $this->secretKey,
            'billCode' => $billCode,
        ]);

        $result = $response->json();

        if (! isset($result['bills'])) {
            throw new \Exception('Failed to fetch bill transactions: ' . ($result['Message'] ?? 'Unknown error'));
        }

        return $result['bills'] ?? [];
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
        $host = config('app.env') === 'production' ? 'toyyibpay.com' : 'dev.toyyibpay.com';
        return "https://{$host}/{$billCode}";
    }
}
