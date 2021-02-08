<?php

namespace Tests\Feature;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PatientProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function patient_successfully_get_his_profile()
    {
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        Passport::actingAs($patient, ['*'], 'patient');
        $response = $this->getJson(route('patient.profile'));
        $response->assertOk();
        $response->assertJsonStructure([
            "id",
            "name",
            "email",
            "photo",
            "phone",
            "gender",
        ]);
    }

    /** @test */
    public function unactivated_patient_failed_to_get_his_profile()
    {
        $patient = Patient::factory()->create();
        Passport::actingAs($patient, ['*'], 'patient');
        $response = $this->getJson(route('patient.profile'));
        $response->assertForbidden();
    }
}