<?php

namespace App\Service\PaymentGateway\Shift4;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\PaymentGateway\Response\ChargeResponse;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\PaymentGateway\PaymentGatewayInterface;

use Symfony\Component\Validator\Constraints as Assert;

class Shift4Service implements PaymentGatewayInterface
{
    protected HttpClientInterface $client;
    protected string $apiUrl;
    protected string $username;

    public function __construct(
        HttpClientInterface $client,
        ValidatorInterface $validator,
        // SerializerInterface $serializer,
        string $apiUrl,
        string $username,
        string $password,
    )
    {
        $this->client = $client;
        $this->validator = $validator;
        // $this->serializer = $serializer;
        $this->apiUrl = $apiUrl;
        $this->username = $username;
        $this->password = $username;
    }

    public function chargeRequest(array $params): array
    {
        try {
            $request_body = $this->chargeRequestData($params);

            $response = $this->client->request('POST', $this->apiUrl . '/charges', [
                'auth_basic' => ['username' => $this->username, 'password' => $this->password],
                'json' => $request_body,
            ]);

            $status_code = $response->getStatusCode();
            $response_content = $response->getContent();
            $data = json_decode($response_content);

            if ($status_code === 200) {

                $errors = $this->validateResponse($response->toArray());
                if (count($errors) > 0) {
                    return [
                        'status' => 422,
                        'errors' => (string) $errors,
                    ];
                }

                // $response_data = new ChargeResponse();
                // $response_data->transaction_id = $data->id;
                // $response_data->created_at = $data->created;
                // $response_data->amount = $data->amount;
                // $response_data->currency = $data->currency;
                // $response_data->card_bin = $data->card->first6;

                return [
                    'status' => 200,
                    // 'data' => $this->serializer->normalize($response_data),
                    'data' => [
                        'transaction_id' => $data->id,
                        'created_at' => $data->created,
                        'amount' => $data->amount,
                        'currency' => $data->currency,
                        'card_bin' => $data->card->first6
                    ],
                ];

            } else {
                return [
                    'status' => $status_code,
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

    protected function chargeRequestData(array $params)
    {
        $requestBody = [
            'amount' => $params['amount'],
            'currency' => $params['currency'],
            'card' => [
                'number' => $params['card_number'],
                'expMonth' => $params['card_exp_month'],
                'expYear' => $params['card_exp_year'],
                'cvc' => $params['card_cvv'],
            ]
        ];

        return $requestBody;
    }

    protected function validateResponse(array $data)
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
