<?php

namespace App\Controller\Api;

use App\DTO\ChargeRequestDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class TestController
{
    #[Route('/payment/charge/{type}', methods: ['POST'])]
    #[OA\Post(
        path: "/payment/charge/{type}",
        summary: "Charge a payment",
        tags: ["Payment"],
        requestBody: new OA\RequestBody(required: true, content: ["application/json" => new OA\JsonContent(ref: ChargeRequestDTO::class)]),
        responses: [
            new OA\Response(response: '200', description: 'Payment charged successfully'),
            new OA\Response(response: '422', description: 'Validation error')
        ]
    )]
    public function chargePayment(string $type, Request $request): Response
    {
        // Your logic here
        return new Response('Payment processed', 200);
    }
}