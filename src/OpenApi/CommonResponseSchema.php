<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CommonResponse",
 *     type="object",
 *     required={"errors", "data"},
 *     @OA\Property(property="errors", type="string", example="Operation successful"),
 *     @OA\Property(property="data", type="object", additionalProperties={}),
 * )
 */
class CommonResponseSchema {}
