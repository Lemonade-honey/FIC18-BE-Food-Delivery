<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (config("app.env") == 'local')
        {
            // user dengan restorant
            User::factory(15)->has(\App\Models\Restorant::factory())->create([
                'role' => 'restorant'
            ]);

            // user dengan driver
        }
    }
}
