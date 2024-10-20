# Symfony Payment Gateway API

This project is a Symfony-based API for handling payments through various gateways. It provides a flexible structure for integrating different payment methods and external APIs, while adhering to best practices in software design and development.

## Table of Contents

- [Features](#features)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)

## Features

- Integration with multiple payment gateways.
- Input validation using Data Transfer Objects (DTOs).
- JSON responses with structured error handling.
- Command-line interface for executing API calls.

## Technologies Used

- Symfony 6.4
- PHP 8.2 or higher
- PostgreSQL
- Docker for containerization

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/your-repo.git
   cd your-repo

2. Set up your environment variables:
    - Copy the .env.example to .env and configure your database and API keys.

3. Build and run the Docker containers:
    ```bash
    docker-compose up --build
    ```
    Or
    ```bash
    docker-compose up
    ```

## Usage
### API Endpoints
  - Charge Payment
      - URL: http://localhost:8000/api/payment/charge/{payment_type}
      - Method: POST
      - Request Body: JSON object conforming to the ChargeRequestDTO.
        - payment_type: 'shift4' Or 'aci'
        - amount: numeric
        - currency: ISO currency code
        - card_number: 16 digits
        - card_exp_year: 4 digits year
        - card_exp_month: 1 to 12
        - card_cvv: 3 digits
      - Successful Response Body: 
      ```bash
      Status Code: 200
      {
        "errors": null,
        "data": {
            "transaction_id": "char_GbF6421ElX2NWnByEsQT8ZlS",
            "created_at": 1729435034,
            "amount": 123,
            "currency": "USD",
            "card_bin": "420000"
        }
      }
      ```
      - Unsuccessful Responses:
      ```bash
      Status code: 422 (invalid input), 500(server error)
      {
        "errors": "Error description",
        "data": []
      }
      ```
### Command-Line Usage

```bash
php bin/console app:payment-charge --type={payment_type} --amount={amount} --currency={currency} --card_number={card_number} --card_exp_year={card_exp_year} --card_exp_month={card_exp_month} --card_cvv={card_cvv}
```
In docker container:

```bash
docker-compose exec app php bin/console app:payment-charge --type={payment_type} --amount={amount} --currency={currency} --card_number={card_number} --card_exp_year={card_exp_year} --card_exp_month={card_exp_month} --card_cvv={card_cvv}
```

Replace <payment_type> with the desired payment gateway type (e.g., aci, shift4).