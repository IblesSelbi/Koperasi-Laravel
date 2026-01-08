<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $userRole = Role::where('nama', 'user')->first();

        if (! $userRole) {
            $this->command->error('Role user belum ada');
            return;
        }

        User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'User Koperasi',
                'role_id' => $userRole->id,
                'password' => Hash::make('12345678'),
            ]
        );
    }
}
