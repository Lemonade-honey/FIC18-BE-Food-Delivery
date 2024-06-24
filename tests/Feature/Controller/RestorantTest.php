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

    public function test_current_restorant_products_success()
    {
        $user = \App\Models\User::factory()->has(\App\Models\Restorant::factory()->has(\App\Models\Product::factory()))->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->getJson('/api/restorant/products')
        ->assertStatus(200)
        ->assertJsonStructure(['data' => [
            ['id',
            'restorant_id',
            'name',
            'image',
            'deskripsi',
            'type',
            'harga'
            ]
        ]]);
    }

    public function test_current_restorant_products_failed_without_auth()
    {
        $this->getJson('/api/restorant/products', [])
        ->assertStatus(401);
    }

    public function test_current_restorant_products_failed_no_data_restorant()
    {
        $token = $this->user_token_generate();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->getJson('/api/restorant/products')
        ->assertStatus(404)
        ->assertJson([
            'massage' => 'restorant user not found'
        ]);
    }

    public function test_current_restorant_products_failed_no_data_products()
    {
        $user = \App\Models\User::factory()->has(\App\Models\Restorant::factory())->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->getJson('/api/restorant/products')
        ->assertStatus(404)
        ->assertJson([
            'massage' => 'tidak ada product terdaftar'
        ]);
    }

    public function test_current_restorant_create_product_success()
    {
        $user = \App\Models\User::factory()->has(\App\Models\Restorant::factory())->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->postJson('/api/restorant/product/create', [
            'name' => fake()->name(),
            'image' => UploadedFile::fake()->image('test.jpg'),
            'deskripsi' => fake()->paragraph(1),
            'type' => 'makanan',
            'harga' => 20000
        ])
        ->assertStatus(201)
        ->assertJsonStructure(['data' => ['name', 'image', 'type', 'harga', 'deskripsi']]);
    }

    public function test_current_restorant_create_product_failed_auth()
    {
        $this->postJson('/api/restorant/product/create')
        ->assertStatus(401);
    }

    public function test_current_restorant_create_product_failed_no_restorant()
    {
        $token = $this->user_token_generate();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->postJson('/api/restorant/product/create', [
            'name' => fake()->name(),
            'image' => UploadedFile::fake()->image('test.jpg'),
            'deskripsi' => fake()->paragraph(1),
            'type' => 'makanan',
            'harga' => 20000
        ])
        ->assertStatus(404);
    }

    public function test_current_restorant_create_product_failed_validation()
    {
        $user = \App\Models\User::factory()->has(\App\Models\Restorant::factory())->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->postJson('/api/restorant/product/create', [
            'name' => fake()->name(),
            'image' => UploadedFile::fake()->image('test.jpg'),
            'deskripsi' => fake()->paragraph(1),
            'type' => 'alat bekas',
            'harga' => 'asd123'
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('type')
        ->assertJsonValidationErrorFor('harga');
    }

    public function test_current_restorant_product_patch_success()
    {
        $user = \App\Models\User::factory()->has(\App\Models\Restorant::factory()->has(\App\Models\Product::factory()))->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->patchJson('/api/restorant/product/' . $user->restorant->products[0]->id, [
            'name' => 'ganti nama saja',
            'image' => UploadedFile::fake()->image('test.jpg')
        ])
        ->assertStatus(200);
    }

    public function test_current_restorant_product_patch_failed_auth()
    {
        $this->patchJson('/api/restorant/product/1', [
            'name' => 'ganti nama saja',
            'image' => UploadedFile::fake()->image('test.jpg')
        ])
        ->assertStatus(401);
    }

    public function test_current_restorant_product_patch_failed_no_restorant()
    {
        $user = \App\Models\User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->patchJson('/api/restorant/product/1', [
            'name' => 'ganti nama saja',
            'image' => UploadedFile::fake()->image('test.jpg')
        ])
        ->assertStatus(404);
    }

    public function test_current_restorant_product_patch_failed_no_product()
    {
        $user = \App\Models\User::factory()->has(\App\Models\Restorant::factory())->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->patchJson('/api/restorant/product/1', [
            'name' => 'ganti nama saja',
            'image' => UploadedFile::fake()->image('test.jpg')
        ])
        ->assertStatus(404);
    }

    public function test_current_restorant_product_delete_success()
    {
        $user = \App\Models\User::factory()->has(\App\Models\Restorant::factory()->has(\App\Models\Product::factory()))->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->deleteJson('/api/restorant/product/' . $user->restorant->products[0]->id)
        ->assertStatus(204);
    }

    public function test_current_restorant_product_delete_failed_auth()
    {
        $this->deleteJson('/api/restorant/product/1')
        ->assertStatus(401);
    }

    public function test_current_restorant_product_delete_failed_no_restorant()
    {
        $user = \App\Models\User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->deleteJson('/api/restorant/product/1')
        ->assertStatus(404);
    }


    public function test_current_restorant_product_delete_failed_no_product()
    {
        $user = \App\Models\User::factory()->has(\App\Models\Restorant::factory())->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->deleteJson('/api/restorant/product/1')
        ->assertStatus(404);
    }
}
