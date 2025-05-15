<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Property;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::inRandomOrder()->first()->id,
            'unit_number' => $this->faker->bothify('??###'),
            'rent_amount' => $this->faker->numberBetween(500, 5000),
            'size' => $this->faker->numberBetween(50, 200),
            'bedrooms' => $this->faker->numberBetween(1, 5),
            'bathrooms' => $this->faker->numberBetween(1, 3),
            'features' => $this->faker->words(3, true),
            'status' => $this->faker->randomElement(['vacant', 'occupied', 'maintenance']),
        ];
    }
}
