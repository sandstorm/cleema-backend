<?php

namespace Database\Seeders;

use App\Models\AdminUsers;
use App\Models\UpUsers;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;

/**
 * Seeder to create a admin user for development purposes
 */
class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = AdminUsers::create([
            'username' => 'admin',
            'firstname' => 'Developer',
            'lastname' => 'Admin',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'email' => 'admin@admin',
            'password' => 'password',
            'is_active' => true,
            'blocked' => false,
        ]);
        assert($admin instanceof AdminUsers);
        $admin->assignRole('super_admin');

        $editor = AdminUsers::create([
            'username' => 'editor',
            'firstname' => 'Editor',
            'lastname' => 'Developer',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'email' => 'editor@admin',
            'password' => 'password',
            'is_active' => true,
            'blocked' => false,
        ]);
        $editor->assignRole('editor');

        $author = AdminUsers::create([
            'username' => 'author',
            'firstname' => 'Author',
            'lastname' => 'Developer',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'email' => 'author@admin',
            'password' => 'password',
            'is_active' => true,
            'blocked' => false,
        ]);
        $author->assignRole('author');
    }
}
