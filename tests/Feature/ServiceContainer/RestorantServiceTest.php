<?php

namespace Tests\Feature\ServiceContainer;

use App\Models\Product;
use App\Models\Restorant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class RestorantServiceTest extends TestCase
{
    use RefreshDatabase;
    protected $restorantService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restorantService = $this->app->make(\App\Services\Interfaces\RestorantService::class);
    }

    private function generate_user()
    {
        return \App\Models\User::factory()->create();
    }

    private function generate_user_restorant(int $total = 1)
    {
        return \App\Models\User::factory($total)->has(Restorant::factory())->create();
    }

    public function test_container_services(){
        $this->assertTrue(true);
    }

    public function test_restorant_by_id()
    {
        $restorant = Restorant::factory()->create();

        $service = $this->restorantService->restorantById($restorant->id);

        $this->assertNotNull($service);
        $this->assertInstanceOf(Restorant::class, $service);
    }

    // public function test_restorant_user_by_request_not_null()
    // {
    //     $user = $this->generate_user_restorant();

    //     $request = \Illuminate\Http\Request::create('/some-url', 'GET');
    //     $request->setUserResolver(function () use ($user) {
    //         return $user;
    //     });

    //     $service = $this->restorantService->restorantUserByRequest($request);

    //     $this->assertNotNull($service);
    //     $this->assertInstanceOf(Restorant::class, $service);
    // }

    public function test_restorant_user_by_request_null()
    {
        $user = $this->generate_user();

        $request = \Illuminate\Http\Request::create('/some-url', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $service = $this->restorantService->restorantUserByRequest($request);

        $this->assertNull($service);
    }

    public function test_restorants_name_or_products()
    {
        $this->generate_user_restorant(3);

        $request = \Illuminate\Http\Request::create('/some-url', 'GET');

        $service = $this->restorantService->restorantsByNameOrProducts($request);

        $this->assertTrue($service->total() > 1, $service->total());
    }

    public function test_restorants_name_or_products_search_restorant_name()
    {

        Restorant::factory()->create([
            'name' => 'preksu penyet'
        ]);

        $this->generate_user_restorant(2);

        $request = \Illuminate\Http\Request::create('/some-url', 'GET', ['search' => 'preksu penyet']);

        $service = $this->restorantService->restorantsByNameOrProducts($request);

        $this->assertTrue($service->total() == 1, $service->total());
    }

    public function test_restorants_name_or_products_search_products_name()
    {
        $restorants = Restorant::factory()->create();
        $products = Product::factory()->create([
            'restorant_id' => $restorants->id,
            'name' => 'red valved'
        ]);

        $this->generate_user_restorant(3);

        $request = \Illuminate\Http\Request::create('/some-url', 'GET', ['search' => 'red valved']);

        $service = $this->restorantService->restorantsByNameOrProducts($request);

        $this->assertTrue($service->total() == 1, $service->total());
    }

    public function test_restorant_with_product_by_restorant_id()
    {
        $restorants = Restorant::factory()->has(Product::factory(4))->create();
        
        $service = $this->restorantService->restorantWithProductByRestorantId($restorants->id);
        
        $this->instance(Restorant::class, $service);
        $this->assertTrue($service->count() > 1);
        $this->assertTrue($service->products()->count() == 4);
    }

    public function test_restorant_with_product_by_restorant_id_null()
    {
        $service = $this->restorantService->restorantWithProductByRestorantId(99);
        
        $this->assertNull($service);
    }
}
