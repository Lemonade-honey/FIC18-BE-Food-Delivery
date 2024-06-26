<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Restorant>
 */
class RestorantFactory extends Factory
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
            'name' => fake()->company() . ' dummy',
            'tags' => [],
            'address' => fake()->address(),
            'latlong' => fake()->latitude() .','. fake()->longitude(),
            'image' => 'https://png.pngtree.com/png-vector/20190721/ourlarge/pngtree-flat-restaurant-illustration-png-image_1568130.jpg'
        ];
    }
}
