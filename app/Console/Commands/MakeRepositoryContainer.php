<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeRepositoryContainer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat sebuah repository container dengan interface dan class implementasinya';

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
        $name = $this->stripRepositorySuffix($name);

        if ($this->repositoryExists($name)) {
            $this->warn('Repository and Interface already exist!');

            return;
            
        } else {

            $this->createInterface($name);
            $this->createRepository($name);
    
            $this->info('Repository Container created successfully.');
            $this->warn('Perlu diperhatikan, repository ini belum di registrasikan ke dalam Repository Provider, perlu registrasi secara manual !');

        }
    }

    /**
     * Penghilangan kata repository pada nama
     */
    protected function stripRepositorySuffix($name)
    {
        if (str_ends_with($name, 'Repository')) {
            return substr($name, 0, -10);
        }
        return $name;
    }

    /**
     * Mencegah double file
     */
    protected function repositoryExists($name)
    {
        $interfacePath = app_path("Repositorys/Interfaces/{$name}Repository.php");
        $repositoryPath = app_path("Repositorys/{$name}RepositoryImpl.php");

        return $this->files->exists($interfacePath) && $this->files->exists($repositoryPath);
    }

    /**
     * Interface file
     */
    protected function createInterface($name)
    {
        $interfaceTemplate = $this->getInterfaceTemplate($name);
        $path = app_path("Repositorys/Interfaces/{$name}Repository.php");
        $this->createFile($path, $interfaceTemplate);
    }

    /**
     * Class Implements
     */
    protected function createRepository($name)
    {
        $repositoryTemplate = $this->getRepositoryTemplate($name);
        $path = app_path("Repositorys/{$name}RepositoryImpl.php");
        $this->createFile($path, $repositoryTemplate);
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

        namespace App\Repositorys\Interfaces;

        interface {$name}Repository
        {
            // Define your methods here
        }
        EOT;
    }

    protected function getRepositoryTemplate($name)
    {
        return <<<EOT
        <?php

        namespace App\Repositorys;

        use App\Repositorys\Interfaces\\{$name}Repository;

        class {$name}RepositoryImpl implements {$name}Repository
        {
            // Implement your methods here
        }
        EOT;
    }
}
