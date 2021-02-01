<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PatientGetDoctorListTest extends TestCase
{
    use RefreshDatabase;

    public function setup() : void 
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }

    /** @test */
    public function verified_patient_successfully_get_activated_doctors_list()
    {
        Doctor::factory(50)->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        Passport::actingAs($patient, ['*'], 'patient');
        $response = $this->getJson(route('doctors.list', ["active" => 1]));
        $response->assertOk();
    }

    /** @test */
    public function unverified_patient_fail_to_get_activated_doctors()
    {
        Doctor::factory(50)->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create();
        Passport::actingAs($patient, ['*'], 'patient');
        $response = $this->getJson(route('doctors.list', ["active" => 1]));
        $response->assertForbidden();
    }
}
