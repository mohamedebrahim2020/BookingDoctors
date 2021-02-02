<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PatientGetDoctorDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function setup() : void 
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }

    /** @test */
    public function verified_patient_successfully_get_activated_doctor_details()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        Passport::actingAs($patient, ['*'], 'patient');
        $response = $this->getJson(route('doctors.details', ["doctor" => $doctor->id]));
        $response->assertOk();
    }

    /** @test */
    public function unverified_patient_fail_to_get_activated_doctor_details()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create();
        Passport::actingAs($patient, ['*'], 'patient');
        $response = $this->getJson(route('doctors.details',["doctor" => $doctor->id]));
        $response->assertForbidden();
    }
}
