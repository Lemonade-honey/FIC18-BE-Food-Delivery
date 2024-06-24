<?php

namespace Tests\Feature\Controller;

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

    public function test_login_success_with_email()
    {
        $password = '123456';
        $user = \App\Models\User::factory()->create([
            'name' => fake()->name,
            'email' => fake()->unique()->email,
            'phone' => fake()->unique()->phoneNumber,
            'password' => $password
        ]);

        $this->postJson('/api/user/login', [
            'credential' => $user->email,
            'password' => $password
        ])
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'phone', 'token']
        ]);

        return $user;
    }

    public function test_login_success_with_phone()
    {
        $password = '123456';
        $user = \App\Models\User::factory()->create([
            'name' => fake()->name,
            'email' => fake()->unique()->email,
            'phone' => fake()->unique()->phoneNumber,
            'password' => $password
        ]);

        $this->postJson('/api/user/login', [
            'credential' => $user->phone,
            'password' => $password
        ])
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'phone', 'token']
        ]);
    }

    public function test_login_failed()
    {
        $this->postJson('/api/user/login', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['credential', 'password']);
    }

    public function test_logout_success()
    {
        $user = $this->test_login_success_with_email();

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/user/logout')
        ->assertSuccessful();
    }

    public function test_logout_failed()
    {
        $this->deleteJson('/api/user/logout')
        ->assertStatus(401);
    }

    public function test_get_current_user_success()
    {
        $user = $this->test_login_success_with_email();

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->getJson('/api/user')
        ->assertSuccessful()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'phone']
        ]);
    }

    public function test_get_current_user_failed()
    {
        $user = $this->test_login_success_with_email();

        $token = $user->createToken('test-token')->plainTextToken;

        $this->getJson('/api/user')
        ->assertStatus(401);
    }
}
