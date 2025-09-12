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
        User::create([
            'name' => 'Người dùng thông thường',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $this->call([
            AdminUserSeeder::class,
            AdditionalUsersSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            NewCategoriesSeeder::class,
            NewProductsSeeder::class,
            ProductSizeSeeder::class,
        ]);
    }
}
