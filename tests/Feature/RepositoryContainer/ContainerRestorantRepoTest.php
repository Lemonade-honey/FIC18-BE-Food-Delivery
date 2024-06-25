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
        $restorants = Restorant::factory(5)->has(Product::factory(5))->create();

        $result = $this->restorantRepo->getRestorantsOrProductsNyNameWithPaginate('');

        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $result);
        
        $this->assertTrue($result->total() == 5);
    }
}
