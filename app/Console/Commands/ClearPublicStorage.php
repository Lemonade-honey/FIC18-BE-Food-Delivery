<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearPublicStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:clear-public';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all files in storage/app/public';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('file yang ada akan terhapus secara permanent');
        if (config("app.env" != "local"))
        {
            $this->info('hanya dapat di local env saja');
        }
        if ($this->confirm('Apakah Anda yakin ingin membersihkan storage di dalam direktori public?')) {
            $storagePath = public_path('storage');
    
            $publicStoragePath = storage_path('app/public');

            File::deleteDirectory($publicStoragePath);

            File::makeDirectory($publicStoragePath);

            $this->info('Public storage cleared successfully.');
        } 
        
        else {
            $this->info('Operasi membersihkan storage dibatalkan.');
        }
    }
}
