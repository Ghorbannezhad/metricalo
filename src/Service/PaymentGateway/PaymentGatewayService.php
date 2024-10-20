<?php

namespace App\Service\PaymentGateway;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Service\PaymentGateway\Shift4\Shift4Service;
use App\Service\PaymentGateway\Shift4\Shift4TestService;
use App\Service\PaymentGateway\Aci\AciService;
use App\Service\PaymentGateway\Aci\AciTestService;

class PaymentGatewayService implements PaymentGatewayInterface
{
    private AciService $aciService;
    private Shift4Service $shift4Service;

    public function __construct(
        AciService $aciService,
        AciTestService $aciTestService,
        Shift4Service $shift4Service,
        Shift4TestService $shift4TestService,
        string $shift4PaymentMode,
        string $aciPaymentMode
    )
    {
        $this->aciService = $aciService;
        $this->aciTestService = $aciTestService;
        $this->shift4Service = $shift4Service;
        $this->shift4TestService = $shift4TestService;
        $this->shift4PaymentMode = $shift4PaymentMode;
        $this->aciPaymentMode = $aciPaymentMode;
    }

    public function chargeRequest(array $params): array
    {
        if ($params['type'] === 'aci') {
            switch ($this->aciPaymentMode){
                case 'prod':
                    return $this->aciService->chargeRequest($params);
                case 'test':
                default:
                    return $this->aciTestService->chargeRequest($params);
            }

        } elseif ($params['type'] === 'shift4') {
            switch ($this->shift4PaymentMode){
                case 'prod':
                    return $this->shift4Service->chargeRequest($params);
                case 'test':
                default:
                    return $this->shift4TestService->chargeRequest($params);
            }
        }

        throw new \InvalidArgumentException('Invalid service type provided.');
    }
}