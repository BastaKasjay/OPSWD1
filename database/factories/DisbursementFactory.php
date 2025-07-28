<?php

namespace Database\Factories;

use App\Models\Disbursement;
use App\Models\CashPayment;
use App\Models\CheckPayment;
use App\Models\Client;
use App\Models\Claim;
use Illuminate\Database\Eloquent\Factories\Factory;

class DisbursementFactory extends Factory
{
    protected $model = Disbursement::class;

    public function definition(): array
    {
        $client = Client::inRandomOrder()->first();
        $claim = Claim::inRandomOrder()->first();

        $formOfPayment = $this->faker->randomElement(['cash', 'cheque']);

        $cashPayment = $formOfPayment === 'cash'
            ? CashPayment::inRandomOrder()->first()
            : null;

        $checkPayment = $formOfPayment === 'cheque'
            ? CheckPayment::inRandomOrder()->first()
            : null;

        return [
            'claim_id' => $claim?->id,
            'client_id' => $client?->id,
            'cash_payment_id' => $cashPayment?->id,
            'check_payment_id' => $checkPayment?->id,
            'form_of_payment' => $formOfPayment, 
            'amount' => $this->faker->randomFloat(2, 1000, 10000),
            'payout_date' => $this->faker->optional()->date(),
            'date_received_claimed' => $this->faker->optional()->date(),
            'date_released' => $this->faker->optional()->date(),
            'total_amount_claimed' => $this->faker->randomFloat(2, 1000, 10000),
        ];
    }
}
