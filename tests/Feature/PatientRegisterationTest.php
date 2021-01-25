<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Notifications\PatientVerificationMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PatientRegisterationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function patient_successfully_registered()
    {
        Notification::fake();
        Queue::fake();
        $data = Patient::factory()->raw(); 
        $response = $this->postJson(route('patientRegister'), $data, ["Accept"=>"application/json"]);
        $patient = Patient::findorFail(1);
        Notification::assertSentTo([$patient], PatientVerificationMail::class);
        $response->assertCreated();
    }

    /** @test */
    public function patient_fail_to_register_with_photo_not_png()
    {
        $data = Patient::factory()->state(['photo' => UploadedFile::fake()->image('photo.jpg')])->raw();
        $response = $this->postJson(route('patientRegister'), $data, ["Accept" => "application/json"]);
        $response->assertJsonValidationErrors('photo');
        $response->assertStatus(422);
    }

    /** @test */
    public function patient_fail_to_register_with_not_unique_email()
    {
        $patient = Patient::factory()->create();
        $data = Patient::factory()->state(['email' => $patient->email])->raw();
        $response = $this->postJson(route('patientRegister'), $data, ["Accept" => "application/json"]);
        $response->assertJsonValidationErrors('email');
        $response->assertStatus(422);
    }

    /** @test */
    public function patient_fail_to_register_with_not_defined_gender()
    {
        $data = Patient::factory()->state(['gender' => ''])->raw();
        $response = $this->postJson(route('patientRegister'), $data, ["Accept" => "application/json"]);
        $response->assertJsonValidationErrors('gender');
        $response->assertStatus(422);
    }
}
