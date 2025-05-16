<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Lease;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lease_id' => Lease::inRandomOrder()->first()->id,
            'amount' => $this->faker->numberBetween(500, 5000),
            'payment_date' => $this->faker->dateTimeThisYear(),
            'payment_method' => $this->faker->randomElement(['cash', 'cheque', 'bank transfer', 'credit card']),
            'transaction_reference' => $this->faker->uuid(),
            'status' => $this->faker->randomElement(['completed', 'pending', 'failed']),
            'notes' => $this->faker->optional()->text(100)
        ];
    }
}
