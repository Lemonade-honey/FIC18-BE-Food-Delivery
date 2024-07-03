<?php

namespace Tests\Feature\ServiceContainer;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FlipGateServiceTest extends TestCase
{
    private $flipService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->flipService = $this->app->make(\App\Services\Interfaces\FlipGateService::class);
    }

    public function test_order_service_container(): void
    {
        $this->assertTrue(true);

        $this->assertInstanceOf(\App\Services\Interfaces\FlipGateService::class, $this->flipService);
    }

    public function test_get_balance()
    {
        $service = $this->flipService->getBalance();

        var_dump($service);
        
        $this->assertArrayHasKey('balance', $service);
    }
}
