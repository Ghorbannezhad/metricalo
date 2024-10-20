<?php

namespace App\Service\PaymentGateway;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AciServiceOld implements PaymentGatewayInterface
{
    public const SERVICE_NAME = 'aci';

    private const TEST_ENTITY_ID = "8a8294174b7ecb28014b9699220015ca";
    private const TEST_PAYMENT_BRAND = "VISA";
    private const TEST_PAYMENT_TYPE = "DB";
    private const TEST_CARD_NUMBER = "4200000000000000";
    private const TEST_CURRENCY = "EUR";

    private HttpClientInterface $client;
    private ValidatorInterface $validator;
    private string $apiUrl;
    private string $authToken;
    private string $paymentMode;


    public function __construct(HttpClientInterface $client, string $paymentMode, string $apiUrl, string $authToken, ValidatorInterface $validator)
    {
        $this->client = $client;
        $this->apiUrl = $apiUrl;
        $this->authToken = $authToken;
        $this->validator = $validator;
        $this->paymentMode = $paymentMode;
    }

    public function chargeRequest(array $params): array
    {
        try {
            $params = $this->prepareParams($params, $this->paymentMode);

            $response = $this->client->request('POST', $this->apiUrl . '/v1/payments', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->authToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $params,
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getContent();
            $responseObject = json_decode($responseBody, false);

            if ($statusCode === 200) {
                // $data = $response->toArray();

                $errors = $this->validateResponse($responseObject);

                if (count($errors) > 0) {
                    return [
                        'status' => 422,
                        'errors' => (string) $errors,
                    ];
                }

                return [
                    'status' => 200,
                    'data' => $responseObject,
                ];

            } else {
                return [
                    'status' => $statusCode,
                    'errors' => (string) $responseObject->error->message,
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'errors' => 'Internal server error: ' . $e->getMessage(),
            ];
        }
    }

    private function prepareParams(array $params, string $paymentMode)
    {
        $data = [
            'amount' => $params['amount'],
            'card' => [
                'holder' => $params['card_holder'] ?? '',
                'expiryMonth' => $params['card_exp_month'],
                'expiryYear' => $params['card_exp_year'],
                'cvv' => $params['card_cvv']
            ]
        ];

        if ($paymentMode == 'test') {
            $data['paymentBrand'] = self::ACI_ENTITY_ID;
            $data['paymentType'] = self::TEST_PAYMENT_BRAND;
            $data['entityId'] = self::TEST_ENTITY_ID;
            $data['currency'] = self::TEST_CURRENCY;
            $data['card']['number'] = self::TEST_CARD_NUMBER;

        } else {
            $data['paymentBrand'] = $params['payment_brand'];
            $data['paymentType'] = $params['payment_type'];
            $data['entityId'] = $params['entity_id'];
            $data['currency'] = $params['currency'];
            $data['card']['number'] = $params['card_number'];
        }

        return $data;
    }

    private function validateResponse(object $data)
    {
        $constraints = new Assert\Collection([
            'id' => new Assert\NotBlank(),
            'created' => new Assert\NotBlank(),
            'amount' => new Assert\NotBlank(),
            'card.bin' => new Assert\NotBlank(),
            'currency' => new Assert\NotBlank()
        ]);

        return $this->validator->validate($data, $constraints);
    }
}
