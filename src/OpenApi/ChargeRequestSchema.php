<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ChargeRequest",
 *     type="object",
 *     required={"amount", "currency", "cardNumber", "cardExpYear", "cardExpMonth", "cardCvv"},
 *     @OA\Property(property="amount", type="integer", format="int64", example=100),
 *     @OA\Property(property="currency", type="string", example="USD"),
 *     @OA\Property(property="cardNumber", type="string", example="4200000000000000"),
 *     @OA\Property(property="cardExpYear", type="integer", example=2034),
 *     @OA\Property(property="cardExpMonth", type="integer", example=5),
 *     @OA\Property(property="cardCvv", type="integer", example=123),
 * )
 */
class ChargeRequestSchema {}
