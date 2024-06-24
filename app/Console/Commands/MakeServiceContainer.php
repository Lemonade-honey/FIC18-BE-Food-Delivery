<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeServiceContainer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat sebuah service container dengan interface dan class implementasinya';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $name = $this->stripServiceSuffix($name);

        if ($this->serviceExists($name)) {
            $this->warn('Service and Interface already exist!');

            return;

        } else {

            $this->createInterface($name);
            $this->createService($name);
    
            $this->info('Service Container created successfully.');
            $this->warn('Perlu diperhatikan, service ini belum di registrasikan ke dalam App Provider, perlu registrasi secara manual !');

        }
    }

    /**
     * Penghilangan kata service pada nama
     */
    protected function stripServiceSuffix($name)
    {
        if (str_ends_with($name, 'Service')) {
            return substr($name, 0, -7);
        }
        return $name;
    }

    /**
     * Mencegah double file
     */
    protected function serviceExists($name)
    {
        $interfacePath = app_path("Services/Interfaces/{$name}Service.php");
        $servicePath = app_path("Services/{$name}ServiceImpl.php");

        return $this->files->exists($interfacePath) && $this->files->exists($servicePath);
    }

    /**
     * Interface file
     */
    protected function createInterface($name)
    {
        $interfaceTemplate = $this->getInterfaceTemplate($name);
        $path = app_path("Services/Interfaces/{$name}Service.php");
        $this->createFile($path, $interfaceTemplate);
    }

    /**
     * Class Implements
     */
    protected function createService($name)
    {
        $serviceTemplate = $this->getServiceTemplate($name);
        $path = app_path("Services/{$name}ServiceImpl.php");
        $this->createFile($path, $serviceTemplate);
    }

    protected function createFile($path, $content)
    {
        if (!$this->files->exists(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true);
        }

        if (!$this->files->exists($path)) {
            $this->files->put($path, $content);
        } else {
            $this->warn("File {$path} already exists!");
        }
    }

    protected function getInterfaceTemplate($name)
    {
        return <<<EOT
        <?php

        namespace App\Services\Interfaces;

        interface {$name}Service
        {
            // Define your methods here
        }
        EOT;
    }

    protected function getServiceTemplate($name)
    {
        return <<<EOT
        <?php

        namespace App\Services;

        use App\Services\Interfaces\\{$name}Service;

        class {$name}ServiceImpl implements {$name}Service
        {
            // Implement your methods here
        }
        EOT;
    }
}
