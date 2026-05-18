<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExchangeRate>
 */
class ExchangeRateFactory extends Factory
{
    protected $model = ExchangeRate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from_currency_id' => Currency::factory(),
            'to_currency_id' => Currency::factory(),
            'rate' => $this->faker->randomFloat(8, 0.5, 2.5),
            'date' => $this->faker->dateTimeBetween('-14 days', 'now')->format('Y-m-d'),
        ];
    }

    /**
     * Create a rate for a specific currency pair
     */
    public function forCurrencies(Currency $from, Currency $to): static
    {
        return $this->state(fn (array $attributes) => [
            'from_currency_id' => $from->get('id'),
            'to_currency_id' => $from->get('id'),
        ]);
    }

    /**
     * Create a rate for today
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => now()->format('Y-m-d'),
        ]);
    }
}
