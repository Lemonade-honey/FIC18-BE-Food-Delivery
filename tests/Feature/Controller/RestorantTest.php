<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestorantTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_current_restorant_failed()
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

    
}
