<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Fetch a random landlord
        $landlord = User::where('role', 'landlord')->inRandomOrder()->first();

        // If no landlord found, throw an error or handle gracefully
        if (!$landlord) {
            throw new \Exception('No landlord found in the database.');
        }

        return [
            'landlord_id' => $landlord->id,
            'name' => $this->faker->streetName,
            'address' => $this->faker->address,
            'description' => $this->faker->text(200),
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'zip_code' => $this->faker->postcode,
            'features' => json_encode($this->faker->words(3)),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
