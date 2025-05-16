<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tenant;
use App\Models\Unit;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lease>
 */
class LeaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unit_id' => Unit::inRandomOrder()->first()->id,
            'tenant_id' => Tenant::inRandomOrder()->first()->id,
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'monthly_rent' => $this->faker->numberBetween(1000, 5000),
            'security_deposit' => $this->faker->numberBetween(500, 1500),
            'status' => $this->faker->randomElement(['active', 'ended', 'terminated']),
            'terms' => $this->faker->text(200),
        ];
    }
}
