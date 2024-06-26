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
            'image' => UploadedFile::fake()->image('test.jpg')
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
        ->getJson('/api/user/restorant', [])
        ->assertStatus(200);
    }

    public function test_current_restorant_failed_no_data()
    {
        $token = $this->user_token_generate();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->getJson('/api/user/restorant', [])
        ->assertStatus(404);
    }

    public function test_current_restorant_failed_without_auth()
    {
        $this->getJson('/api/user/restorant', [])
        ->assertStatus(401);
    }

    public function test_create_restorant_success()
    {
        $token = $this->user_token_generate();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->postJson('/api/user/restorant/create', $this->dummy_restorant_valid_data())
        ->assertStatus(201)
        ->assertJsonStructure([
            'data' => ['id', 'name', 'latlong', 'image']
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
        ->postJson('/api/user/restorant/create', $this->dummy_restorant_valid_data())
        ->assertStatus(409);
    }

    public function test_create_restorant_failed_validation()
    {
        $token = $this->user_token_generate();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->postJson('/api/user/restorant/create', [
            'name' => '',
            'address' => fake()->address(),
            'latlong' => '',
            'image' => ''
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'latlong', 'image']);
    }

    public function test_current_restorant_patch_success()
    {
        // create user with role restorant
        $user = \App\Models\User::factory()->create([
            'role' => 'restorant'
        ]);

        $restorant = \App\Models\Restorant::factory()->create([
            'user_id' => $user->id,
            'name' => 'kfc'
        ]);

        $oldRestorantUser = \App\Models\Restorant::find($restorant->id);

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->patchJson('/api/user/restorant', [
            'name' => 'olive'
        ])
        ->assertStatus(200);

        $user->refresh();

        $newRestorantUser = \App\Models\Restorant::find($restorant->id);

        $this->assertNotEquals($oldRestorantUser, $newRestorantUser);
    }

    public function test_current_restorant_patch_failed_no_auth()
    {
        $user = \App\Models\User::factory()->has(\App\Models\Restorant::factory())->create([
            'role' => 'restorant'
        ]);

        $this->patchJson('/api/user/restorant', [
            'name' => 'olive'
        ])
        ->assertStatus(401);
    }

    public function test_current_restorant_patch_failed_no_restorant()
    {
        $user = \App\Models\User::factory()->create();

        $token = $user->createToken('token-user')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->patchJson('/api/user/restorant', [
            'name' => 'olive'
        ])
        ->assertStatus(404);
    }

    public function test_current_restorant_delete_success()
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
        ->deleteJson('/api/user/restorant', [])
        ->assertStatus(204);
    }

    public function test_current_restorant_delete_failed_auth()
    {
        $this->deleteJson('/api/user/restorant', [])
        ->assertStatus(401);
    }

    public function test_current_restorant_delete_failed_no_restorant()
    {
        $token =  $this->user_token_generate();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->deleteJson('/api/user/restorant', [])
        ->assertStatus(404);
    }

    public function test_current_restorant_products_success()
    {
        $user = \App\Models\User::factory()->has(\App\Models\Restorant::factory()->has(\App\Models\Product::factory()))->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->getJson('/api/user/restorant/products')
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
        $this->getJson('/api/user/restorant/products', [])
        ->assertStatus(401);
    }

    public function test_current_restorant_products_failed_no_data_restorant()
    {
        $token = $this->user_token_generate();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->getJson('/api/user/restorant/products')
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
        ->getJson('/api/user/restorant/products')
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
        ->postJson('/api/user/restorant/product/create', [
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
        $this->postJson('/api/user/restorant/product/create')
        ->assertStatus(401);
    }

    public function test_current_restorant_create_product_failed_no_restorant()
    {
        $token = $this->user_token_generate();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
        ->postJson('/api/user/restorant/product/create', [
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
        ->postJson('/api/user/restorant/product/create', [
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
        ->patchJson('/api/user/restorant/product/' . $user->restorant->products[0]->id, [
            'name' => 'ganti nama saja',
            'image' => UploadedFile::fake()->image('test.jpg')
        ])
        ->assertStatus(200);
    }

    public function test_current_restorant_product_patch_failed_auth()
    {
        $this->patchJson('/api/user/restorant/product/1', [
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
        ->patchJson('/api/user/restorant/product/1', [
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
        ->patchJson('/api/user/restorant/product/1', [
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
        ->deleteJson('/api/user/restorant/product/' . $user->restorant->products[0]->id)
        ->assertStatus(204);
    }

    public function test_current_restorant_product_delete_failed_auth()
    {
        $this->deleteJson('/api/user/restorant/product/1')
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
        ->deleteJson('/api/user/restorant/product/1')
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
        ->deleteJson('/api/user/restorant/product/1')
        ->assertStatus(404);
    }

    public function test_restorants_success_all()
    {
        \App\Models\Restorant::factory(2)->has(\App\Models\Product::factory())->create();

        $this->getJson('/api/restorants')
        ->assertStatus(200)
        ->assertJsonStructure([
            ["data", "total", "links"]
        ]);
    }

    public function test_restorants_success_search_restorant_name()
    {
        \App\Models\Restorant::factory(2)->has(\App\Models\Product::factory())->create();

        \App\Models\Restorant::factory()->create([
            'name' => 'rujak abc'
        ]);

        $this->getJson('/api/restorants?search=rujak')
        ->assertStatus(200)
        ->assertJsonFragment([
            "name" => 'rujak abc'
        ]);
    }

    public function test_restorants_success_no_data()
    {
        $this->getJson('/api/restorants?search=rujak')
        ->assertStatus(200)
        ->assertJsonFragment([
            "total" => 0
        ]);
    }

    public function test_restorant_by_id_success()
    {
        $restorant = \App\Models\Restorant::factory()->has(\App\Models\Product::factory())->create();

        $this->getJson('/api/restorant/' . $restorant->id)
        ->assertStatus(200)
        ->assertJsonStructure([
            "data" => ["name"]
        ]);
    }

    public function test_restorant_by_id_failed()
    {
        $this->getJson('/api/restorant/99')
        ->assertStatus(404);
    }

    public function test_restorant_product_with_id_success()
    {
        $restorant = \App\Models\Restorant::factory()->create();

        $product = \App\Models\Product::factory()->create();

        $this->getJson('/api/restorant/' . $restorant->id . '/product/' . $product->id)
        ->assertStatus(200);
    }

    public function test_restorant_product_with_id_failed_no_restorant()
    {
        $this->getJson('/api/restorant/' . 21 . '/product/' . 1)
        ->assertStatus(404);
    }

    public function test_restorant_product_with_id_failed_no_product()
    {
        $restorant = \App\Models\Restorant::factory()->create();

        $this->getJson('/api/restorant/' . $restorant->id . '/product/' . 1)
        ->assertStatus(404);
    }
}
