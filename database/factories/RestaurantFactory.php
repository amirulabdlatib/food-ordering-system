<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Restaurant>
 */
class RestaurantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            //
            'name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'phone_number' => $this->faker->phoneNumber(),
            'description' => $this->faker->paragraph(),
            'category_id' => Category::factory(),
            'manager_id' => User::factory()->restaurant_manager()->create()->id,
            'is_approved' => $this->faker->boolean(),
        ];
    }
}