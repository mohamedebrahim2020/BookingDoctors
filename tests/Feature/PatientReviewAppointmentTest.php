<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\PlatformType;
use App\Enums\RankValue;
use App\Jobs\PushNotification;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PatientReviewAppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }

    /** @test */
    public function patient_successfully_review_an_appointment()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day"=> strval(Carbon::parse(now())->subDay()->dayOfWeek),
                "is_all_day"=> "1"
            ]
        );
        $doctor->firebaseTokens()->updateOrCreate(
            ['platform' => PlatformType::WEB],
            ['token' => 'jjj'],
        );
        $appointment = Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'time' => Carbon::parse(now())->subDay()->timestamp,
            'status' => AppointmentStatus::COMPLETED,
        ])->create();
        Passport::actingAs($patient, ['*'], 'patient');
        Queue::fake();
        Bus::fake();
        $data = [
            'rank'  => RankValue::THREE,
            'comment' => 'good appointment',
        ];
        $response = $this->postJson(route('appointments.reviews.store', ['appointment' =>$appointment->id]),$data, ["Accept" => "application/json"]);
        Bus::assertDispatched(PushNotification::class);
        Bus::assertDispatchedAfterResponse(PushNotification::class);
        $response->assertCreated();
    }

    /** @test */
    public function patient_failed_to_review_an_appointment_with_invalid_rank()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day" => strval(Carbon::parse(now())->subDay()->dayOfWeek),
                "is_all_day" => "1"
            ]
        );
        $doctor->firebaseTokens()->updateOrCreate(
            ['platform' => PlatformType::WEB],
            ['token' => 'jjj'],
        );
        $appointment = Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'time' => Carbon::parse(now())->subDay()->timestamp,
            'status' => AppointmentStatus::COMPLETED,
        ])->create();
        Passport::actingAs($patient, ['*'], 'patient');
        Queue::fake();
        Bus::fake();
        $data = [
            'rank'  => 6,
            'comment' => 'good appointment',
        ];
        $response = $this->postJson(route('appointments.reviews.store', ['appointment' => $appointment->id]), $data, ["Accept" => "application/json"]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('rank');
    }

    /** @test */
    public function patient_failed_to_review_a_not_found_appointment()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day" => strval(Carbon::parse(now())->subDay()->dayOfWeek),
                "is_all_day" => "1"
            ]
        );
        $doctor->firebaseTokens()->updateOrCreate(
            ['platform' => PlatformType::WEB],
            ['token' => 'jjj'],
        );
        Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'time' => Carbon::parse(now())->subDay()->timestamp,
            'status' => AppointmentStatus::COMPLETED,
        ])->create();
        Passport::actingAs($patient, ['*'], 'patient');
        Queue::fake();
        Bus::fake();
        $data = [
            'rank'  => RankValue::THREE,
            'comment' => 'good appointment',
        ];
        $response = $this->postJson(route('appointments.reviews.store', ['appointment' => 2]), $data, ["Accept" => "application/json"]);
        $response->assertNotFound();
    }

    /** @test */
    public function patient_failed_to_review_an_appointment_not_belong_to_him()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $patient2 = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day" => strval(Carbon::parse(now())->subDay()->dayOfWeek),
                "is_all_day" => "1"
            ]
        );
        $doctor->firebaseTokens()->updateOrCreate(
            ['platform' => PlatformType::WEB],
            ['token' => 'jjj'],
        );
        Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'time' => Carbon::parse(now())->subDay()->timestamp,
            'status' => AppointmentStatus::COMPLETED,
        ])->create();

        $appointment = Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient2->id,
            'time' => Carbon::parse(now())->subDay()->timestamp,
            'status' => AppointmentStatus::COMPLETED,
        ])->create();
        Passport::actingAs($patient, ['*'], 'patient');
        Queue::fake();
        Bus::fake();
        $data = [
            'rank'  => RankValue::THREE,
            'comment' => 'good appointment',
        ];
        $response = $this->postJson(route('appointments.reviews.store', ['appointment' => $appointment->id]), $data, ["Accept" => "application/json"]);
        $response->assertForbidden();
    }

    /** @test */
    public function patient_failed_to_review_an_appointment_that_reviewed_before()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day" => strval(Carbon::parse(now())->subDay()->dayOfWeek),
                "is_all_day" => "1"
            ]
        );
        $doctor->firebaseTokens()->updateOrCreate(
            ['platform' => PlatformType::WEB],
            ['token' => 'jjj'],
        );
        $appointment = Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'time' => Carbon::parse(now())->subDay()->timestamp,
            'status' => AppointmentStatus::COMPLETED,
        ])->create();
        $appointment->review()->create([
            'rank' => RankValue::FOUR,
            'comment' => 'good'
        ]);
        Passport::actingAs($patient, ['*'], 'patient');
        Queue::fake();
        Bus::fake();
        $data = [
            'rank'  => RankValue::THREE,
            'comment' => 'good appointment',
        ];
        $response = $this->postJson(route('appointments.reviews.store', ['appointment' => $appointment->id]), $data, ["Accept" => "application/json"]);
        $response->assertStatus(400);
    }

    /** @test */
    public function patient_failed_to_review_an_appointment_that_not_completed()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day" => strval(Carbon::parse(now())->subDay()->dayOfWeek),
                "is_all_day" => "1"
            ]
        );
        $doctor->firebaseTokens()->updateOrCreate(
            ['platform' => PlatformType::WEB],
            ['token' => 'jjj'],
        );
        $appointment = Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'time' => Carbon::parse(now())->subDay()->timestamp,
            'status' => AppointmentStatus::CHECKED,
        ])->create();
        Passport::actingAs($patient, ['*'], 'patient');
        Queue::fake();
        Bus::fake();
        $data = [
            'rank'  => RankValue::THREE,
            'comment' => 'good appointment',
        ];
        $response = $this->postJson(route('appointments.reviews.store', ['appointment' => $appointment->id]), $data, ["Accept" => "application/json"]);
        $response->assertStatus(400);
    }
}
