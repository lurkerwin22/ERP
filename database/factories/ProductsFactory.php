<?php

namespace Database\Factories;

use App\Models\Products;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Categorie;

/**
 * @extends Factory<products>
 */
class ProductsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'categorie_id' => Categorie::factory(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'url' => 'https://picsum.photos/',
            'prix' => $this->faker->randomFloat(2, 0, 100),
            'stock' => $this->faker->numberBetween(0, 100),
            'seuil_alerte' => $this->faker->numberBetween(1, 20),
        ];
    }
}
