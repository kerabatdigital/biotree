<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use App\Services\Gateways\BayarCashGateway;
use App\Services\Gateways\ChipGateway;
use App\Services\Gateways\ToyyibPayGateway;

class PaymentGatewayFactory
{
    public static function resolve(string $gatewayName = 'toyyibpay'): PaymentGateway
    {
        return match ($gatewayName) {
            'toyyibpay' => app(ToyyibPayGateway::class),
            'chip' => app(ChipGateway::class),
            'bayarcash' => app(BayarCashGateway::class),
            default => throw new \InvalidArgumentException("Unknown payment gateway: {$gatewayName}"),
        };
    }
}
