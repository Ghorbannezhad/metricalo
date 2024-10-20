<?php

namespace App\Command;

use App\Service\PaymentGateway\PaymentGatewayService;
use App\DTO\ChargeRequestDTO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Exception\CommandException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PaymentChargeCommand extends Command
{
    // Name the command (will be used to call it: php bin/console app:payment-charge)
    protected static $defaultName = 'app:payment-charge';

    private PaymentGatewayService $paymentGatewayService;

    // Inject payment gateway service
    public function __construct(PaymentGatewayService $paymentGatewayService, ValidatorInterface $validator)
    {
        parent::__construct();
        $this->paymentGatewayService = $paymentGatewayService;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Call an payment gateway with provided parameters.')
            ->addOption('type', null, InputOption::VALUE_REQUIRED, 'Type of payment')
            ->addOption('amount', null, InputOption::VALUE_REQUIRED, 'Amount to charge')
            ->addOption('currency', null, InputOption::VALUE_REQUIRED, 'Currency (e.g., USD, EUR)')
            ->addOption('card_number', null, InputOption::VALUE_REQUIRED, 'Card number (16 digits)')
            ->addOption('card_exp_year', null, InputOption::VALUE_REQUIRED, 'Card expiration year (4 digits)')
            ->addOption('card_exp_month', null, InputOption::VALUE_REQUIRED, 'Card expiration month (01-12)')
            ->addOption('card_cvv', null, InputOption::VALUE_REQUIRED, 'Card CVV (3 digits)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $type = $input->getOption('type');
        $amount = $input->getOption('amount');
        $currency = $input->getOption('currency');
        $cardNumber = $input->getOption('card_number');
        $cardExpYear = $input->getOption('card_exp_year');
        $cardExpMonth = $input->getOption('card_exp_month');
        $cardCvv = $input->getOption('card_cvv');

        $params = [
            'type' => $type,
            'amount' => $amount,
            'currency' => $currency,
            'card_number' => $cardNumber,
            'card_exp_month' => $cardExpMonth,
            'card_exp_year' => $cardExpYear,
            'card_cvv' => $cardCvv
        ];

        $dto = new ChargeRequestDTO();
        $dto->setType($input->getOption('type') ?? '');
        $dto->setAmount((int) $input->getOption('amount') ?? '');
        $dto->setCurrency($input->getOption('currency') ?? '');
        $dto->setCardNumber($input->getOption('card_number') ?? '');
        $dto->setCardExpYear((int) $input->getOption('card_exp_year') ?? '');
        $dto->setCardExpMonth((int) $input->getOption('card_exp_month') ?? '');
        $dto->setCardCvv($input->getOption('card_cvv') ?? '');

    
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $output->writeln($error->getMessage());
            }
            return Command::INVALID;
        }

        try {
            // Call the Payment gateway service
            $response = $this->paymentGatewayService->chargeRequest($params);

            // Output the JSON response
            $io->success('Response from payment gateway:');

            $result  = [
                'status' => $response['status'],
                'errors' => $response['errors'] ?? null,
                'data' => $response['data'] ?? [],
            ];

            $io->writeln(json_encode($response, JSON_PRETTY_PRINT));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Error calling payment gateway: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
