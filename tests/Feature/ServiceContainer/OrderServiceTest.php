<?php

namespace Tests\Feature\ServiceContainer;

use App\Models\Order;
use App\Models\Product;
use App\Models\Restorant;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private $orderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderService = $this->app->make(\App\Services\Interfaces\OrderService::class);
    }

    public function test_order_service_container(): void
    {
        $this->assertTrue(true);

        $this->assertInstanceOf(\App\Services\Interfaces\OrderService::class, $this->orderService);
    }

    public function test_break_request_into_collection()
    {
        $restorant = Restorant::factory()->create();
        $product1 = Product::factory()->create([
            'restorant_id' => $restorant->id
        ]);

        $product2 = Product::factory()->create([
            'restorant_id' => $restorant->id
        ]);


        $dummyProductsOrderRequest = [
            [
                'product_id' => $product1->id,
                'qty' => 2,
                'note'=> ''
            ],
            [
                'product_id' => $product2->id,
                'qty' => 6,
                'note'=> 'test note'
            ]
        ];

        $service = $this->orderService->breakProductsOrderRequestIntoProductsIds(collect($dummyProductsOrderRequest));

        $this->assertEquals([$product1->id, $product2->id], $service);
    }

    public function test_merge_current_order_with_product_restorant()
    {
        $restorant = Restorant::factory()->create();
        $product1 = Product::factory()->create([
            'restorant_id' => $restorant->id,
            'harga' => 5000
        ]);

        $product2 = Product::factory()->create([
            'restorant_id' => $restorant->id,
            'harga' => 2000
        ]);

        $dummyProductsOrderRequest = [
            [
                'product_id' => $product1->id,
                'qty' => 1,
                'note'=> ''
            ],
            [
                'product_id' => $product2->id,
                'qty' => 2,
                'note'=> 'test note'
            ]
        ];

        $request = new \Illuminate\Http\Request;

        $request->merge([
            'restorant_id'=> $restorant->id,
            'products_order' => $dummyProductsOrderRequest
        ]);

        $productService = new \App\Services\ProductServiceImpl();

        $products = $productService->productsByIdsAndRestorantId([$product1->id, $product2->id], $restorant->id);

        $service = $this->orderService->mergeCurrentProductsToProducsOrder($request, $products);

        $excpectData = [
            [
                'product_id' => $product1->id,
                'qty' => 1,
                'note'=> '',
                'product_name' => $product1->name,
                'product_price' => $product1->harga,
            ],
            [
                'product_id' => $product2->id,
                'qty' => 2,
                'note'=> 'test note',
                'product_name' => $product2->name,
                'product_price' => $product2->harga,
            ]
        ];

        $this->assertIsArray($service);

        $this->assertEquals($excpectData, $service);
    }

    public function test_get_total_products_order_price()
    {
        $restorant = Restorant::factory()->create();
        $product1 = Product::factory()->create([
            'restorant_id' => $restorant->id,
            'harga' => 5000
        ]);

        $product2 = Product::factory()->create([
            'restorant_id' => $restorant->id,
            'harga' => 2000
        ]);

        $dummyProductsOrderRequest = [
            [
                'product_id' => $product1->id,
                'qty' => 1,
                'note'=> ''
            ],
            [
                'product_id' => $product2->id,
                'qty' => 2,
                'note'=> 'test note'
            ]
        ];

        $request = new \Illuminate\Http\Request;

        $request->merge([
            'restorant'=> $restorant->id,
            'products_order' => $dummyProductsOrderRequest
        ]);

        $productService = new \App\Services\ProductServiceImpl();

        $products = $productService->productsByIdsAndRestorantId([$product1->id, $product2->id], $restorant->id);

        $service = $this->orderService->getTotalProductsOrderPrice($request, $products);

        $this->assertTrue($service == ((5000 * 1) + (2000 * 2)));
    }

    public function test_get_detail_order_price()
    {
        $dummyPrice = 54000;

        $servieFee = env("SERVICE_FEE", 0);

        $service = $this->orderService->getDetailOrderPrice($dummyPrice);

        $this->assertIsArray($service);

        $this->assertTrue($service['total price'] == ($dummyPrice + $servieFee));
    }

    public function test_create_order()
    {
        $user = \App\Models\User::factory()->create();

        $restorant = Restorant::factory()->create();
        $product1 = Product::factory()->create([
            'restorant_id' => $restorant->id,
            'harga' => 5000
        ]);

        $product2 = Product::factory()->create([
            'restorant_id' => $restorant->id,
            'harga' => 2000
        ]);

        $dummyProductsOrderRequest = [
            [
                'product_id' => $product1->id,
                'qty' => 1,
                'note'=> ''
            ],
            [
                'product_id' => $product2->id,
                'qty' => 2,
                'note'=> 'test note'
            ]
        ];

        $request = $request = new \Illuminate\Http\Request;

        $request->setUserResolver(function() use($user){
            return $user;
        });

        $request->merge([
            'restorant_id'=> $restorant->id,
            'products_order' => $dummyProductsOrderRequest
        ]);

        $service = $this->orderService->createOrderUserByRequest($request, $restorant);

        $this->assertInstanceOf(Order::class, $service);
    }
}
