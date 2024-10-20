<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\PaymentGateway\PaymentGatewayService;
use App\DTO\ChargeRequestDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;



class PaymentController extends AbstractController
{
    private $paymentGatewayService;
    private ValidatorInterface $validator;

    public function __construct(PaymentGatewayService $paymentGatewayService, ValidatorInterface $validator)
    {
        $this->paymentGatewayService = $paymentGatewayService;
        $this->validator = $validator;
    }

    #[Route('/api/payment/charge/{type}', name: 'payment_charge', methods: ['POST'])]
    #[OA\Post(
        path: "/api/payment/charge/{type}",
        summary: "Charge a payment",
        tags: ["Payment"],
        requestBody: new OA\RequestBody(required: true, content: ["application/json" => new OA\JsonContent(ref: ChargeRequestDTO::class)]),
        responses: [
            new OA\Response(response: '200', description: 'Payment charged successfully'),
            new OA\Response(response: '422', description: 'Validation error')
        ]
    )]
    public function charge(string $type, Request $request): JsonResponse
    {
        $dto = new ChargeRequestDTO();
        $dto->setType($type);
        $dto->setAmount((int) $request->get('amount'));
        $dto->setCurrency($request->get('currency'));
        $dto->setCardNumber($request->get('card_number'));
        $dto->setCardExpYear((int) $request->get('card_exp_year'));
        $dto->setCardExpMonth((int) $request->get('card_exp_month'));
        $dto->setCardCvv($request->get('card_cvv'));

    
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return new JsonResponse([
                'errors' => 'Validation failed: ' . (string) $errors,
                'data' => [],
            ], 422);
        }

        try {
            $params = json_decode($request->getContent(), true);
            $params['type'] = $type;

            $result = $this->paymentGatewayService->chargeRequest($params);
            return new JsonResponse([
                'errors' => $result['errors'] ?? null,
                'data' => $result['data'] ?? []
            ], $result['status']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
