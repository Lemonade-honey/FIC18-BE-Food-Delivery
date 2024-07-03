<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $restorantId = \App\Models\Restorant::inRandomOrder()->first()->id;
        $productRestorant = \App\Models\Product::inRandomOrder()->where('restorant_id', $restorantId)->first();
        
        return [
            'uuid' => fake()->uuid(),
            'user_id' => \App\Models\User::factory(),
            'restorant_id' => $restorantId,
            'orders' => [
                [
                    'product_id' => $productRestorant->id,
                    'qty' => 1,
                    'note' => '',
                    'product_name' => $productRestorant->name,
                    'product_price' => $productRestorant->harga
                ]
            ],
            'details' => [
                'detail_price' => [
                    [
                        'name' => 'price',
                        'price' => $productRestorant->harga
                    ],
                    [
                        'name' => 'service',
                        'price' => 0
                    ]
                ],
                'total_price' => $productRestorant->harga
            ],
            'price' => $productRestorant->harga,
            'status' => 1
        ];
    }
}
