<?php

namespace Tests\Feature\ServiceContainer;

use App\Models\Restorant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

    private function generate_user_restorant()
    {
        return \App\Models\User::factory()->has(\App\Models\Restorant::factory())->create();
    }

    public function test_container_services(){
        $this->assertTrue(true);
    }

    public function test_restorant_user_by_request_not_null()
    {
        $user = $this->generate_user_restorant();

        $request = \Illuminate\Http\Request::create('/some-url', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $service = $this->restorantService->restorantUserByRequest($request);

        $this->assertNotNull($service);
        $this->assertInstanceOf(Restorant::class, $service);
    }

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
}
