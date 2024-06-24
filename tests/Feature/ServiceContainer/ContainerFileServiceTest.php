<?php

namespace Tests\Feature\ServiceContainer;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContainerFileServiceTest extends TestCase
{
    protected $fileService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the Storage facade
        Storage::fake('public');

        $this->fileService = $this->app->make(\App\Services\Interface\FileService::class);
    }

    public function test_container_services(){
        $this->assertTrue(true);
    }

    public function test_save_file_to_storage_path()
    {
        // create fake file
        $file = UploadedFile::fake()->image('testfile.jpg');

        $savedFilePath = $this->fileService->saveFileToStoragePath($file, 'restorant');

        // check jika file ada atau telah terupload
        Storage::disk('public')->assertExists($savedFilePath);

        $this->assertStringStartsWith('restorant', $savedFilePath);
    }
}
