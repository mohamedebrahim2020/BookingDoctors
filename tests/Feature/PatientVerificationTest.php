<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\PatientVerificationCode;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PatientVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function patient_successfully_verified()
    {
        $code = PatientVerificationCode::factory()->create();
        $patient = Patient::findorfail($code->patient_id);
        $data = [
            "email" => $patient->email,
            "code" => $code->code
        ];
        $response = $this->postJson(route('patientVerify'), $data, ["Accept"=>"application/json"]);
        $response->assertOk();
    }

    /** @test */
    public function patient_fail_to_verify_with_not_found_email()
    {
        $code = PatientVerificationCode::factory()->create();
        $patient = Patient::findorfail($code->patient_id);
        $data = [
            "email" => $patient->email . "assd",
            "code" => $code->code
        ];
        $response = $this->postJson(route('patientVerify'), $data, ["Accept" => "application/json"]);
        $response->assertJsonValidationErrors('email');
        $response->assertStatus(422);
    }

    /** @test */
    public function patient_fail_to_verify_as_code_is_expired()
    {
        $code = PatientVerificationCode::factory()->create();
        $patient = Patient::findorfail($code->patient_id);
        $data = [
            "email" => $patient->email ,
            "code" => $code->code
        ];
        $this->travel(61)->minutes();
        $response = $this->postJson(route('patientVerify'), $data, ["Accept" => "application/json"]);
        $response->assertStatus(400);
    }

    /** @test */
    public function patient_fail_to_verify_as_code_is_wrong()
    {
        $code = PatientVerificationCode::factory()->create();
        $patient = Patient::findorfail($code->patient_id);
        $data = [
            "email" => $patient->email,
            "code" => $code->code . "kkf"
        ];
        $response = $this->postJson(route('patientVerify'), $data, ["Accept" => "application/json"]);
        $response->assertStatus(400);
    }
}
