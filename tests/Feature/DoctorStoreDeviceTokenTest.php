<?php

namespace Tests\Feature;

use App\Enums\PlatformType;
use App\Models\Doctor;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DoctorStoreDeviceTokenTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }
    
    /** @test */
    public function doctor_successfully_store_token()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        Passport::actingAs($doctor, ['*'], 'doctor');
        $data = [
            'platform' => PlatformType::WEB,
            'token' => 'kdkdkdkdkkdkd',
        ];
        $response = $this->postJson(route('doctor.storeDeviceToken'), $data, $headers=["Accept"=>"application/json"]);
        $response->assertCreated();
    }
    
    /** @test */
    public function doctor_failed_to_store_token_with_not_defined_platform()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        Passport::actingAs($doctor, ['*'], 'doctor');
        $data = [
            'platform' => 4,
            'token' => 'kdkdkdkdkkdkd',
        ];
        $response = $this->postJson(route('doctor.storeDeviceToken'), $data, $headers=["Accept"=>"application/json"]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('platform');
    }
}
