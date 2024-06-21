<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_new_user_success()
    {
        $this->postJson('/api/user/register', [
            'name' => 'tony',
            'email' => 'tony@gmail.com',
            'phone' => '823456',
            'password' => fake()->password()
        ])
        ->assertStatus(201)
        ->assertJson([
            'data' => [
                'name' => 'tony',
                'email' => 'tony@gmail.com',
                'phone' => '823456',
            ]
        ]);
    }

    public function test_register_failed()
    {
        $this->postJson('/api/user/register', [
            'name' => 'daffa',
            'email' => 'dafasaaja',
            'phone' => '234234',
            'password' => ''
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_register_exists_email()
    {
        $user = \App\Models\User::factory()->create([
            'name' => fake()->name,
            'email' => fake()->unique()->email,
            'phone' => fake()->unique()->phoneNumber,
            'password' => fake()->password
        ]);

        $this->postJson('/api/user/register', [
            'name' => 'tony',
            'email' => $user->email,
            'phone' => fake()->phoneNumber,
            'password' => fake()->password()
        ])
        ->assertStatus(422);
    }

    public function test_register_exists_phone()
    {
        $user = \App\Models\User::factory()->create([
            'name' => fake()->name,
            'email' => fake()->unique()->email,
            'phone' => fake()->unique()->phoneNumber,
            'password' => fake()->password
        ]);

        $this->postJson('/api/user/register', [
            'name' => 'tony',
            'email' => fake()->unique()->email,
            'phone' => $user->phone,
            'password' => fake()->password()
        ])
        ->assertStatus(422);
    }
}
