<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['nama' => 'admin']);
        Role::firstOrCreate(['nama' => 'user']);
    }
}
