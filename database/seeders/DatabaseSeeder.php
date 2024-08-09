<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

/**
 * Default seeder from laravel, is called when `php artisan db:seed` is called
 * Instead of writing seeding code here, create a seperate seeder class and call them here.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $path = 'DBDumps/cleema.sql';
        $file = file_get_contents($path);
        if($file != null) {
            DB::unprepared($file);
        }
    }
}
