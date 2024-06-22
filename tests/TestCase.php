<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Generate Token
     * 
     * dengan randome fake user data
     */
    public function user_token_generate()
    {
        $password = '123456';
        $user = \App\Models\User::factory()->create([
            'name' => fake()->name,
            'email' => fake()->unique()->email,
            'phone' => fake()->unique()->phoneNumber,
            'password' => $password
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        return $token;
    }

    /**
     * Generate Token Data Static
     * 
     * dengan data user static (jelas)
     */
    public function user_static_token()
    {
        $user = \App\Models\User::factory()->create([
            'name' => 'daffa alif',
            'email' => 'daffa@saja.com',
            'phone' => '12345678',
            'password' => '123456'
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        return $token;
    }
}
