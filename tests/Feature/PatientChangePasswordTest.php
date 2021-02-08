<?php

namespace Tests\Feature;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PatientChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function patient_successfully_change_his_password()
    {
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        Passport::actingAs($patient, ['*'], 'patient');
        $data = [
            'old_password' => '123456789',
            'new_password' => '1234567',
            'new_password_confirmation' => '1234567',
        ];
        $response = $this->postJson(route('patient.changePassword'), $data, $headers=["Accept"=>"application/json"]);
        $response->assertOk();
    }

    /** @test */
    public function patient_failed_to_change_password_with_wrong_old_password()
    {
        $patient = patient::factory()->create(["verified_at" => Carbon::now()]);
        Passport::actingAs($patient, ['*'], 'patient');
        $data = [
            'old_password' => '12345678910', //wrong password
            'new_password' => '1234567',
            'new_password_confirmation' => '1234567',
        ];
        $response = $this->postJson(route('patient.changePassword'), $data, $headers=["Accept"=>"application/json"]);
        $response->assertUnauthorized();
    }

    /** @test */
    public function patient_failed_to_change_password_with_wrong_unconfirmed_new_password()
    {
        $patient = patient::factory()->create(["verified_at" => Carbon::now()]);
        Passport::actingAs($patient, ['*'], 'patient');
        $data = [
            'old_password' => '12345678910', //wrong password
            'new_password' => '1234567',
            'new_password_confirmation' => '12345',
        ];
        $response = $this->postJson(route('patient.changePassword'), $data, $headers=["Accept"=>"application/json"]);
        $response->assertStatus(422);
    }
}