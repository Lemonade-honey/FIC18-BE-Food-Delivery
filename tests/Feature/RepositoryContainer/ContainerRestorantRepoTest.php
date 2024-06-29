<?php

namespace Tests\Feature\RepositoryContainer;

use App\Models\Product;
use App\Models\Restorant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContainerRestorantRepoTest extends TestCase
{
    use RefreshDatabase;

    private $restorantRepo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restorantRepo = $this->app->make(\App\Repositorys\Interfaces\RestorantRepository::class);
    }

    public function test_get_current_restorant()
    {
        $user = \App\Models\User::factory()->has(Restorant::factory())->create();

        $result = $this->restorantRepo->getCurrentRestorantByUser($user->id);

        $this->assertInstanceOf(Restorant::class, $result);
    }

    public function test_get_current_restorant_with_products()
    {
        $user = \App\Models\User::factory()->has(Restorant::factory()->has(Product::factory()))->create();

        $result = $this->restorantRepo->getCurrentRestorantWithProducts($user->id);

        $this->assertInstanceOf(Restorant::class, $result);
    }

    public function test_get_restorants_name_or_products()
    {
        $restorants = Restorant::factory()->create([
            'name' => "geprek sambel"
        ]);

        $result = $this->restorantRepo->getRestorantsOrProductsNyNameWithPaginate($restorants->name);

        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $result);
    }


    public function test_get_restorant_by_id()
    {
        $restorant = Restorant::factory()->has(Product::factory(5))->create();

        $result = $this->restorantRepo->getRestorantById($restorant->id);

        $this->assertInstanceOf(Restorant::class, $result);

        $this->assertNotNull($result);
    }

    public function test_get_restorant_by_id_no_data()
    {
        $result = $this->restorantRepo->getRestorantById(99);

        $this->assertNull($result);
    }

    public function test_get_restorant_with_products_by_restorant_id()
    {
        $restorant = Restorant::factory()->has(Product::factory(5))->create();

        $result = $this->restorantRepo->getRestorantByIdWithProducts($restorant->id);

        $this->assertInstanceOf(Restorant::class, $result);
    }

    public function test_get_restorant_with_products_by_restorant_id_no_data()
    {
        $result = $this->restorantRepo->getRestorantByIdWithProducts(99);

        $this->assertNull($result);
    }
}
