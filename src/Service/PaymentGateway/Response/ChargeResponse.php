<?php

namespace App\Service\PaymentGateway\Response;

class ChargeResponse
{
    public $transaction_id;
    public $created_at;
    public $amount;
    public $currency;
    public $card_bin;
}