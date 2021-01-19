<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Seeder;

class DoctorSpecializationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Specialization::insert([
            ['name' => 'ortho'],
            ['name' => 'dentist'],
            ['name' => 'knee'],
        ]);
    }
}
