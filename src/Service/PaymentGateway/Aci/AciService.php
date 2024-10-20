<?php

namespace App\Service\PaymentGateway\Aci;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\PaymentGateway\PaymentGatewayInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AciService implements PaymentGatewayInterface
{
    protected HttpClientInterface $client;
    protected ValidatorInterface $validator;
    protected string $apiUrl;
    protected string $authToken;


    public function __construct(
        HttpClientInterface $client,
        ValidatorInterface $validator,
        string $apiUrl,
        string $authToken,
    )
    {
        $this->client = $client;
        $this->validator = $validator;
        $this->apiUrl = $apiUrl;
        $this->authToken = $authToken;
    }

    public function chargeRequest(array $params): array
    {
        try {
            $params = $this->chargeRequestData($params);

            $response = $this->client->request('POST', $this->apiUrl . '/v1/payments', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->authToken,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => http_build_query($params),
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getContent();
            $data = json_decode($responseBody);

            if ($statusCode === 200) {
                $errors = $this->validateResponse($response->toArray());

                if (count($errors) > 0) {
                    return [
                        'status' => 422,
                        'errors' => (string) $errors,
                    ];
                }

                return [
                    'status' => 200,
                    'data' => [
                        'transaction_id' => $data->id,
                        'created_at' => $data->timestamp,
                        'amount' => $data->amount,
                        'currency' => $data->currency,
                        'card_bin' => $data->card->bin
                    ],
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

    protected function chargeRequestData(array $params)
    {
        $data = [
            'amount' => $params['amount'],
            'paymentBrand' => $params['payment_brand'],
            'paymentType' => $params['payment_type'],
            'entityId' => $params['entity_id'],
            'currency' => $params['currency'],
            'card' => [
                'number' => $params['card_number'],
                'holder' => $params['card_holder'] ?? '',
                'expiryMonth' => $params['card_exp_month'],
                'expiryYear' => $params['card_exp_year'],
                'cvv' => $params['card_cvv']
            ]
        ];
        return $data;
    }

    protected function validateResponse(array $data)
    {
        $constraints = new Assert\Collection([
            'fields' => [
                'id' => new Assert\NotBlank(),
                'timestamp' => new Assert\NotBlank(),
                'amount' => new Assert\NotBlank(),
                'currency' => new Assert\NotBlank(),
                'card' => new Assert\Collection([
                    'fields' => [
                        'bin' => new Assert\NotBlank()
                    ],
                'allowExtraFields' => true
                ])
            ],
            'allowExtraFields' => true
        ]);

        return $this->validator->validate($data, $constraints);
    }
}
