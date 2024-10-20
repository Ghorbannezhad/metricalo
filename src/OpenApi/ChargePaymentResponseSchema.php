<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ChargePaymentResponse",
 *     type="object",
 *     @OA\Property(property="transaction_id", type="string", example="abc123"),
 *     @OA\Property(property="created_at", type="date", example="2024-10-19 15:29:19.129+0000"),
 *     @OA\Property(property="amount", type="number", example=92.00),
 *     @OA\Property(property="currency", type="string", example="EUR"),
 *     @OA\Property(property="card_bin", type="string", example="420000"),
 * )
 */
class ChargePaymentResponseSchema {}
