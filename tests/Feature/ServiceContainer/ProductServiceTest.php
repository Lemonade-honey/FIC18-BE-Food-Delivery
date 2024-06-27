<?php

namespace Tests\Feature\ServiceContainer;

use App\Models\Product;
use App\Models\Restorant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    private $productService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productService = $this->app->make(\App\Services\Interfaces\ProductService::class);
    }

    public function test_product_by_id_and_restorant_id()
    {
        $restorant = Restorant::factory()->create();
        $product = Product::factory()->create([
            'restorant_id' => $restorant->id
        ]);

        $service = $this->productService->productByIdAndRestorantId($product->id, $restorant->id);

        $this->assertNotNull($service);

        $this->assertInstanceOf(Product::class, $service);
    }

    public function test_product_by_ids_and_restorant_id()
    {
        $restorant = Restorant::factory()->create();
        $product1 = Product::factory()->create([
            'restorant_id' => $restorant->id
        ]);

        $product2 = Product::factory()->create([
            'restorant_id' => $restorant->id
        ]);

        $service = $this->productService->productsByIdsAndRestorantId([$product2->id, 99], $restorant->id);

        $this->assertNotNull($service);

        $this->assertTrue($service->count() == 1);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $service);
    }
}
