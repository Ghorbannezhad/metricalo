<?php

namespace App\Service\PaymentGateway\Aci;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\PaymentGateway\PaymentGatewayInterface;

class AciTestService extends AciService
{
    private const TEST_ENTITY_ID = "8a8294174b7ecb28014b9699220015ca";
    private const TEST_PAYMENT_BRAND = "VISA";
    private const TEST_PAYMENT_TYPE = "DB";
    private const TEST_CARD_NUMBER = "4200000000000000";
    private const TEST_CURRENCY = "EUR";

    protected function chargeRequestData(array $params)
    {
        $data = [
            'amount' => $params['amount'],
            'paymentBrand' => self::TEST_PAYMENT_BRAND,
            'paymentType' => self::TEST_PAYMENT_TYPE,
            'entityId' => self::TEST_ENTITY_ID,
            'currency' => self::TEST_CURRENCY,
            // 'card' => [
            //     'number' => self::TEST_CARD_NUMBER,
            //     'holder' => $params['card_holder'] ?? '',
            //     'expiryMonth' => $params['card_exp_month'],
            //     'expiryYear' => $params['card_exp_year'],
            //     'cvv' => $params['card_cvv']
            // ]
            'card.number' => self::TEST_CARD_NUMBER,
            'card.holder' => $params['card_holder'] ?? '',
            'card.expiryMonth' => $params['card_exp_month'],
            'card.expiryYear' => $params['card_exp_year'],
            'card.cvv' => $params['card_cvv']
        ];
        return $data;
    }
}
