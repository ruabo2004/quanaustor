<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminGmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo tài khoản admin@gmail.com
        User::updateOrCreate([
            'email' => 'admin@gmail.com'
        ], [
            'name' => 'Admin Gmail',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $this->command->info('Admin Gmail account created successfully!');
        $this->command->info('Email: admin@gmail.com | Password: admin123');
    }
}




















