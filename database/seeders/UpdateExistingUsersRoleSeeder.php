<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateExistingUsersRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing users to use new role system
        
        // Update main admin
        User::updateOrCreate([
            'email' => 'admin@admin.com'
        ], [
            'name' => 'Administrator',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Update admin
        User::updateOrCreate([
            'email' => 'admin2@gmail.com'
        ], [
            'name' => 'Admin Gmail',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Update regular user
        User::updateOrCreate([
            'email' => 'user@gmail.com'
        ], [
            'name' => 'User Gmail',
            'password' => Hash::make('admin123'),
            'role' => 'user',
        ]);

        // Set default role for any users without role
        User::whereNull('role')->update(['role' => 'user']);

        $this->command->info('Users roles updated successfully!');
        $this->command->info('Admin: admin@admin.com | Password: admin123');
        $this->command->info('Admin: admin2@gmail.com | Password: admin123');
        $this->command->info('User: user@gmail.com | Password: admin123');
    }
}