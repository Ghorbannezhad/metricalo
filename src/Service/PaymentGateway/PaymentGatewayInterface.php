<?php

namespace App\Service\PaymentGateway;

interface PaymentGatewayInterface
{
    public function chargeRequest(array $params): array;
}
