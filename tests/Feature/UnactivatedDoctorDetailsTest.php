<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use Database\Seeders\DoctorSpecializationsSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UnactivatedDoctorDetailsTest extends TestCase
{
    use RefreshDatabase;

    protected $routeName = 'api/admin/unactivatedDoctors/';

    public function setup() : void 
    {
        parent::setUp();
        $this->seed(SuperAdminSeeder::class);
        $this->seed(DoctorSpecializationsSeeder::class);
    }

    /** @test */
    public function superadmin_successfully_get_unactivated_doctor_details()
    {
        $admin = Admin::where('is_super', 1)->first();
        Doctor::factory()->count(40)->create();
        $doctor = Doctor::factory()->create();
        Passport::actingAs($admin, ['*'], 'admin');
        $response = $this->getJson($this->routeName . $doctor->id);
        $response->assertOk();
        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'specialization',
            'photo',
            'degree_copy',
        ]);
    }

    /** @test */
    public function admin_successfully_get_unactivated_doctor_details()
    {
        $admin = Admin::factory()->create();
        Doctor::factory()->count(40)->create();
        $doctor = Doctor::factory()->create();
        Passport::actingAs($admin, ['*'], 'admin');
        $response = $this->getJson($this->routeName . $doctor->id);
        $response->assertOk();
        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'specialization',
            'photo',
            'degree_copy',
        ]);
    }
}
