<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::insert([
            ['name' => 'control doctors', 'guard_name' => 'admin'],
            ['name' => 'control patients', 'guard_name' => 'admin'],
            ['name' => 'control appointments', 'guard_name' => 'admin'],
        ]);
    }
}
