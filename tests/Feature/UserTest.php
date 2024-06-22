<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function user_token_generate()
    {
        $password = '123456';
        $user = User::factory()->create([
            'name' => fake()->name,
            'email' => fake()->unique()->email,
            'phone' => fake()->unique()->phoneNumber,
            'password' => $password
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        return $token;
    }

    public function user_static_token()
    {
        $user = User::factory()->create([
            'name' => 'daffa alif',
            'email' => 'daffa@saja.com',
            'phone' => '12345678',
            'password' => '123456'
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        return $token;
    }

    public function test_update_user_success()
    {
        $token = $this->user_token_generate();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->patchJson('/api/user', [
            'name' => 'alex'
        ])
        ->assertSuccessful()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'phone']
        ]);
    }

    public function test_update_user_failed_validation()
    {
        $token = $this->user_token_generate();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->patchJson('/api/user', [
            'name' => 'alex 123'
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
    }

    public function test_update_user_failed_without_token()
    {
        $this->patchJson('/api/user', [
            'name' => 'alex',
            'phone' => '3884992834'
        ])
        ->assertStatus(401);
    }

    public function test_update_user_role_success()
    {
        $user = User::factory()->create([
            'name' => fake()->name,
            'email' => fake()->unique()->email,
            'phone' => fake()->unique()->phoneNumber,
            'password' => '1234567'
        ]);

        $token = $user->createToken('test-token')->plainTextToken;
        
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->patchJson('/api/user/role', [
            'role' => 'driver'
        ])
        ->assertStatus(200);

        // refresh data user
        $user->refresh();

        $this->assertEquals('driver', $user->role);
    }

    public function test_update_user_role_failed_validation()
    {
        $token = $this->user_token_generate();
        
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->patchJson('/api/user/role', [
            'role' => 'ntah'
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['role']);
    }

    public function test_update_user_role_failed_validation_empty_data()
    {
        $token = $this->user_token_generate();
        
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->patchJson('/api/user/role', [
            
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['role']);

    }

    public function test_update_user_role_failed_without_token()
    {
        $this->patchJson('/api/user/role', [
            'role' => 'driver'
        ])
        ->assertStatus(401);
    }
}
