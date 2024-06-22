<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class RestorantTest extends TestCase
{
    use RefreshDatabase;

    private static function generate_user_with_restorant(): \App\Models\User
    {
        $user = \App\Models\User::factory()->has(\App\Models\Restorant::factory())->create([
            'role' => 'restorant'
        ]);

        return $user;
    }

    /**
     * Dummy Data Restorant
     */
    private function dummy_restorant_valid_data(): array
    {
        return [
            'name' => fake()->company(),
            'address' => fake()->address(),
            'latlong' => fake()->latitude() .','. fake()->longitude(),
            'photo' => UploadedFile::fake()->image('test.jpg')
        ];
    }

    public function test_current_restorant_success()
    {
        // create user with role restorant
        $user = \App\Models\User::factory()->has(\App\Models\Restorant::factory())->create([
            'role' => 'restorant'
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->getJson('/api/restorant', [])
        ->assertStatus(200);
    }

    public function test_current_restorant_failed_no_data()
    {
        $token = $this->user_token_generate();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->getJson('/api/restorant', [])
        ->assertStatus(404);
    }

    public function test_current_restorant_failed_without_auth()
    {
        $this->getJson('/api/restorant', [])
        ->assertStatus(401);
    }

    public function test_create_restorant_success()
    {
        $token = $this->user_token_generate();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->postJson('/api/restorant/create', $this->dummy_restorant_valid_data())
        ->assertStatus(201)
        ->assertJsonStructure([
            'data' => ['id', 'name', 'latlong', 'photo']
        ]);
    }

    public function test_create_restorant_failed_already_has_restorant_data()
    {
        $user = $this->generate_user_with_restorant();

        $token = $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->postJson('/api/restorant/create', $this->dummy_restorant_valid_data())
        ->assertStatus(409)
        ->assertJsonValidationErrors(['massage']);
    }

    public function test_create_restorant_failed_validation()
    {
        $token = $this->user_token_generate();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->postJson('/api/restorant/create', [
            'name' => '',
            'address' => fake()->address(),
            'latlong' => '',
            'photo' => ''
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'latlong', 'photo']);
    }
}