<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdditionalUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo admin user với @gmail.com
        User::firstOrCreate(
            ['email' => 'admin2@gmail.com'],
            [
                'name' => 'Admin Gmail',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // Tạo user thường với @gmail.com
        User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'User Gmail',
                'password' => Hash::make('admin123'),
                'role' => 'user',
            ]
        );

        $this->command->info('Additional users created successfully!');
        $this->command->info('Admin: admin2@gmail.com | Password: admin123');
        $this->command->info('User: user@gmail.com | Password: admin123');
    }
}
