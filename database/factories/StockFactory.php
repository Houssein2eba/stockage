<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class StockFactory extends Factory
{
    protected $model = \App\Models\Stock::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
                        'id' => (string) Str::uuid(),
            'name' => $this->faker->unique()->word(),
            'status' => $this->faker->randomElement(['good']),
            'location' => $this->faker->city,
        ];
    }
}
