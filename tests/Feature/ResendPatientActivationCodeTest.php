<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Notifications\PatientVerificationMail;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ResendPatientActivationCodeTest extends TestCase
{
    use RefreshDatabase;

    use RefreshDatabase;

    /** @test */
    public function patient_successfully_request_activation_code()
    {
        Notification::fake();
        Queue::fake();
        $patient = Patient::factory()->create();
        $data = [
            'email' => $patient->email,
            'password' => 123456789
        ]; 
        $response = $this->postJson(route('codeResend'), $data, ["Accept"=>"application/json"]);
        Notification::assertSentTo([$patient], PatientVerificationMail::class);
        $response->assertOk();
    }

    /** @test */
    public function verified_patient_fail_to_request_activation_code()
    {
        Notification::fake();
        Queue::fake();
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $data = [
            'email' => $patient->email,
            'password' => 123456789
        ];
        $response = $this->postJson(route('codeResend'), $data, ["Accept" => "application/json"]);
        $response->assertStatus(400);
    }

    /** @test */
    public function unauthenticated_patient_fail_to_request_activation_code()
    {
        Notification::fake();
        Queue::fake();
        $patient = Patient::factory()->create();
        $data = [
            'email' => $patient->email,
            'password' => 123456  // wrong password
        ];
        $response = $this->postJson(route('codeResend'), $data, ["Accept" => "application/json"]);
        $response->assertUnauthorized();
    }

    /** @test */
    public function patient_fail_to_request_activation_code_with_wrong_mail()
    {
        Notification::fake();
        Queue::fake();
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $data = [
            'email' => $patient->email . 'jdkd',
            'password' => 12345689
        ];
        $response = $this->postJson(route('codeResend'), $data, ["Accept" => "application/json"]);
        $response->assertJsonValidationErrors('email');
        $response->assertStatus(422);
    }

    /** @test */
    public function patient_fail_to_request_activation_code_with_after_third_try_in_thirty_minutes()
    {
        Notification::fake();
        Queue::fake();
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $data = [
            'email' => $patient->email,
            'password' => 123456789
        ];
        $i = 0;
        while ($i <= 4) {
            $response = $this->postJson(route('codeResend'), $data, ["Accept" => "application/json"]);
            $i++;
        }
        $response->assertStatus(429);
    }
}

