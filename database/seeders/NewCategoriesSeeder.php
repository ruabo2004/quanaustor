<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(['name' => 'Quần âu công sở']);
        Category::create(['name' => 'Quần âu dự tiệc']);
        Category::create(['name' => 'Quần âu casual']);
    }
}















