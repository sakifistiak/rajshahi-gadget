<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Default Administrator
        User::updateOrCreate(
            ['email' => 'admin@khangadget.com'],
            [
                'name' => 'Khan Gadget Admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}
