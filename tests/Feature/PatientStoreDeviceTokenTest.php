<?php

namespace Tests\Feature;

use App\Enums\PlatformType;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PatientStoreDeviceTokenTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
    }
    
    /** @test */
    public function patient_successfully_store_token()
    {
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        Passport::actingAs($patient, ['*'], 'patient');
        $data = [
            'platform' => PlatformType::WEB,
            'token' => 'kdkdkdkdkkdkd',
        ];
        $response = $this->postJson(route('patient.storeDeviceToken'), $data, $headers=["Accept"=>"application/json"]);
        $response->assertCreated();
    }
    
    /** @test */
    public function patient_failed_to_store_token_with_not_defined_platform()
    {
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        Passport::actingAs($patient, ['*'], 'patient');
        $data = [
            'platform' => 4,
            'token' => 'kdkdkdkdkkdkd',
        ];
        $response = $this->postJson(route('patient.storeDeviceToken'), $data, $headers=["Accept"=>"application/json"]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('platform');
    }
}
