<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(OrderType::cases());

        return [
            'user_id' => User::factory(),
            'status' => fake()->randomElement(OrderStatus::cases()),
            'type' => $type,
            'delivery_address' => $type === OrderType::Delivery ? fake()->address() : null,
            'notes' => fake()->optional()->sentence(),
            'total_amount' => fake()->randomFloat(2, 50, 2000),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => OrderStatus::Pending]);
    }

    public function delivery(): static
    {
        return $this->state([
            'type' => OrderType::Delivery,
            'delivery_address' => fake()->address(),
        ]);
    }

    public function pickup(): static
    {
        return $this->state([
            'type' => OrderType::Pickup,
            'delivery_address' => null,
        ]);
    }
}
