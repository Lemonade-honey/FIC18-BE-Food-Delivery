<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'restorant_id' => \App\Models\Restorant::factory(),
            'name' => fake()->name(),
            'image' => 'https://cdn.britannica.com/98/235798-050-3C3BA15D/Hamburger-and-french-fries-paper-box.jpg',
            'deskripsi' => fake()->paragraph(1),
            'type' => fake()->boolean() ? 'makanan' : 'minuman',
            'harga' => fake()->randomFloat(2, 1000, 10000)
        ];
    }
}
