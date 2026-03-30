<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gymapp.com',
            'password' => Hash::make('password'),
            'is_approved' => true,
            'gender' => 'male',
        ]);

        $admin->assignRole('admin');

        $this->command->info('Admin user created:');
        $this->command->info('Email: admin@gymapp.com');
        $this->command->info('Password: password');
    }
}

