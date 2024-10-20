<?php

namespace App\Service\PaymentGateway;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Shift4ServiceOld implements PaymentGatewayInterface
{
    public const SERVICE_NAME = 'shift4';

    private const TEST_CARD_NUMBER = '4200000000000000';

    private HttpClientInterface $client;
    private string $apiUrl;
    private string $username;

    public function __construct(HttpClientInterface $client, ValidatorInterface $validator, string $apiUrl, string $username, string $paymentMode)
    {
        $this->client = $client;
        $this->validator = $validator;
        $this->apiUrl = $apiUrl;
        $this->username = $username;
        $this->paymentMode = $paymentMode;
    }

    public function chargeRequest(array $params): array
    {
        try {
            $params = $this->prepareParams($params, $this->paymentMode);

            $response = $this->client->request('POST', $this->apiUrl . '/charges', [
                'auth_basic' => ['username' => $this->username, 'password' => ''],
                'json' => $params,
            ]);

            $statusCode = $response->getStatusCode();
            $data = $response->toArray();

            if ($statusCode === 200) {

                $errors = $this->validateResponse($data);

                if (count($errors) > 0) {
                    return [
                        'status' => 422,
                        'errors' => (string) $errors,
                    ];
                }
                
                return [
                    'status' => 200,
                    'data' => $data,
                ];
            } else {
                return [
                    'status' => $statusCode,
                    'errors' => (string) $data['error']['message'],
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
            'currency' => $params['currency'],
            'card' => [
                'expMonth' => $params['card_exp_month'],
                'expYear' => $params['card_exp_year'],
                'cvc' => $params['card_cvv']
            ]
        ];

        if ($paymentMode == 'test') {
            $data['card']['number'] = self::TEST_CARD_NUMBER;

        } else {
            $data['card']['number'] = $params['card_number'];
        }

        return $data;
    }

    private function validateResponse(array $data)
    {
        $constraints = new Assert\Collection([
            'fields' => [
                'id' => new Assert\NotBlank(),
                'created' => new Assert\NotBlank(),
                'amount' => new Assert\NotBlank(),
                'currency' => new Assert\NotBlank(),
                'card' => new Assert\Collection([
                    'fields' => [
                        'first6' => [new Assert\NotBlank()],
                    ],
                    'allowExtraFields' => true,
                ]),
            ],
            'allowExtraFields' => true,
        ]);

        return $this->validator->validate($data, $constraints);
    }
}
