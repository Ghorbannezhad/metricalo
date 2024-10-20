<?php

namespace App\Service\PaymentGateway\Shift4;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\PaymentGateway\Response\ChargeResponse;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\Validator\Constraints as Assert;

class Shift4TestService extends Shift4Service
{
    private const TEST_CARD_NUMBER = '4200000000000000';

    protected function chargeRequestData(array $params)
    {
        $requestBody = [
            'amount' => $params['amount'],
            'currency' => $params['currency'],
            'card' => [
                'number' => self::TEST_CARD_NUMBER,
                'expMonth' => $params['card_exp_month'],
                'expYear' => $params['card_exp_year'],
                'cvc' => $params['card_cvv'],
            ]
        ];

        return $requestBody;
    }
}
