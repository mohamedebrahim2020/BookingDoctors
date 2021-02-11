<?php

namespace Tests\Feature;

use App\Jobs\StoreAppointment;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Notifications\RequestAppointmentNotification;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PatientRequestAppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }

    /** @test */
    public function patient_successfully_request_an_appointment()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day"=> "1",
                "is_all_day"=> "1"
            ]
        );
        Passport::actingAs($patient, ['*'], 'patient');
        Notification::fake();
        Queue::fake();
        Bus::fake();
        $data = [
            'time'  => Carbon::parse(now())->addWeeks(3)->next('Monday')->timestamp * 1000,
            'duration' => 30,
        ];
        $response = $this->postJson(route('doctors.appointments.store', ['doctor' =>$doctor->id]),$data, ["Accept" => "application/json"]);
        Notification::assertSentTo([$doctor], RequestAppointmentNotification::class);
        Bus::assertDispatched(StoreAppointment::class);
        $response->assertCreated();
    }

    /** @test */
    public function patient_fail_to_request_an_appointment_with_unactivated_doctor()
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day" => "1",
                "is_all_day" => "1"
            ]
        );
        Passport::actingAs($patient, ['*'], 'patient');
        Notification::fake();
        Queue::fake();
        $data = [
            'time'  => Carbon::parse(now())->addWeeks(3)->next('Monday')->timestamp * 1000,
            'duration' => 30,
        ];
        $response = $this->postJson(route('doctors.appointments.store', ['doctor' => $doctor->id]), $data, ["Accept" => "application/json"]);
        $response->assertForbidden();
    }

    /** @test */
    public function unverified_patient_fail_to_request_an_appointment()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create();
        $doctor->workingDays()->create(
            [
                "day" => "1",
                "is_all_day" => "1"
            ]
        );
        Passport::actingAs($patient, ['*'], 'patient');
        Notification::fake();
        Queue::fake();
        $data = [
            'time'  => Carbon::parse(now())->addWeeks(3)->next('Monday')->timestamp * 1000,
            'duration' => 30,
        ];
        $response = $this->postJson(route('doctors.appointments.store', ['doctor' => $doctor->id]), $data, ["Accept" => "application/json"]);
        $response->assertForbidden();
    }

    /** @test */
    public function patient_fail_to_request_an_appointment_not_meet_doctor_shift()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create();
        $doctor->workingDays()->create(
            [
                "day" => "2",
                "is_all_day" => "1"
            ]
        );
        Passport::actingAs($patient, ['*'], 'patient');
        Notification::fake();
        Queue::fake();
        $data = [
            'time'  => Carbon::parse(now())->addWeeks(3)->next('Monday')->timestamp * 1000,
            'duration' => 30,
        ];
        $response = $this->postJson(route('doctors.appointments.store', ['doctor' => $doctor->id]), $data, ["Accept" => "application/json"]);
        $response->assertForbidden();
    }

    /** @test */
    public function patient_fail_to_request_an_appointment_not_meet_doctor_shift_duration()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create();
        $doctor->workingDays()->create(
            [
                "day" => "1",
                "from" => "10:00 PM",
                "to" => "11:00 PM",
                "is_all_day" => "0"
            ]
        );
        Passport::actingAs($patient, ['*'], 'patient');
        Notification::fake();
        Queue::fake();
        $data = [
            'time'  => Carbon::parse(now())->addWeeks(3)->next('Monday')->timestamp * 1000,
            'duration' => 90,
        ];
        $response = $this->postJson(route('doctors.appointments.store', ['doctor' => $doctor->id]), $data, ["Accept" => "application/json"]);
        $response->assertForbidden();
    }

    /** @test */
    public function patient_fail_to_request_an_appointment_has_same_time_of_already_approved_appointment()
    {
        $time = Carbon::parse(now())->addWeeks(3)->next('Monday')->timestamp * 1000;
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day" => "1",
                "from" => "10:00 PM",
                "to" => "11:00 PM",
                "is_all_day" => "0"
            ]
        );
        Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'time' => $time,
        ])->create();
        Passport::actingAs($patient, ['*'], 'patient');
        Notification::fake();
        Queue::fake();
        $data = [
            'time'  => $time,
            'duration' => 30,
        ];
        $response = $this->postJson(route('doctors.appointments.store', ['doctor' => $doctor->id]), $data, ["Accept" => "application/json"]);
        $response->assertStatus(400);
    }
}
