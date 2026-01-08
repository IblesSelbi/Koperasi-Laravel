<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('nama', 'admin')->first();

        if (! $adminRole) {
            $this->command->error('Role admin belum ada');
            return;
        }

        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Koperasi',
                'role_id' => $adminRole->id,
                'password' => Hash::make('12345678'),
            ]
        );
    }
}
