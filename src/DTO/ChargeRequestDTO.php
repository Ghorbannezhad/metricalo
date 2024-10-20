<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource]
class ChargeRequestDTO
{
    #[Assert\NotBlank(message: 'The "type" field is required.')]
    #[Assert\Choice(choices:['shift4', 'aci'],message:"Type must be one of 'shift4' or 'aci'.")]
    private string $type;

    #[Assert\NotBlank(message: 'The "amount" field is required.')]
    #[Assert\Type(type:"numeric", message:"Amount must be an integer.")]
    #[Assert\GreaterThanOrEqual(value:1, message:"Amount must be at least 1.")]
    private string $amount;

    #[Assert\NotBlank(message: 'The "currency" field is required.')]
    #[Assert\Currency(message:"Currency must be a valid three-letter ISO currency code.")]
    private string $currency;

    #[Assert\NotBlank(message: 'The "card_number" field is required.')]
    #[Assert\Regex(pattern:"/^[1-9][0-9]{15}$/",message:"Card number must be 16 digits and cannot start with zero.")]
    private string $card_number;

    #[Assert\NotBlank(message: 'The "card_exp_year" field is required.')]
    #[Assert\Range(min:2000, max:9999, notInRangeMessage:"Card expiration year must be between 2000 and 9999.")]
    private string $card_exp_year;

    #[Assert\NotBlank(message: 'The "card_exp_month" field is required.')]
    #[Assert\Range(min:1, max:12, notInRangeMessage:"Card expiration month must be between 01 and 12.")]
    private string $card_exp_month;

    #[Assert\NotBlank(message: 'The "card_cvv" field is required.')]
    #[Assert\Regex(pattern:"/^\d{3}$/",message:"Card CVV must be exactly 3 digits.")]
    private string $card_cvv;

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function setAmount(int $amount)
    {
        $this->amount = $amount;
    }

    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
    }

    public function setCardNumber(int $card_number)
    {
        $this->card_number = $card_number;
    }

    public function setCardExpYear(int $card_exp_year)
    {
        $this->card_exp_year = $card_exp_year;
    }

    public function setCardExpMonth(string $card_exp_month)
    {
        $this->card_exp_month = $card_exp_month;
    }

    public function setCardCvv(string $card_cvv)
    {
        $this->card_cvv = $card_cvv;
    }
}
