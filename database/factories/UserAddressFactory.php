<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAddress>
 */
class UserAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'label' => fake()->randomElement(['Home', 'Work', 'Other', null]),
            'house_street' => fake()->buildingNumber().' '.fake()->streetName(),
            'barangay' => fake()->randomElement(['San Jose', 'Santa Cruz', 'Poblacion', 'Bagong Silang', 'Sto. Nino']),
            'city' => fake()->randomElement(['Marikina City', 'Quezon City', 'Manila', 'Pasig', 'Makati']),
            'province' => fake()->randomElement(['Metro Manila', 'Rizal', 'Bulacan', 'Cavite', 'Laguna']),
            'region' => fake()->randomElement(['NCR', 'Region IV-A', 'Region III', null]),
            'zip_code' => fake()->numerify('####'),
            'is_default' => false,
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
