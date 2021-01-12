<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'name' => 'super_hima',
            'email' => 'super_admin@gmail.com',
            'password' => bcrypt('admin23456789'),
            'phone' => '01277820380',
            'is_super' => 1,
        ]);
    }
}
