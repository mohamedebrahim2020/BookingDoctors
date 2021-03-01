<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Notifications\SendDailyAppointmentNotification;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SendDailyCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }
    /** @test */
    public function command_successfully_send_daily_appointment_notification_to_patient()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day"=> Carbon::parse(now())->dayOfWeek,
                "is_all_day"=> "1"
            ]
        );
        $appointment = Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'time' => Carbon::parse(now())->timestamp,
            'status' => AppointmentStatus::APPROVED,
        ])->create();
        Notification::fake();
        Queue::fake();
        $this->artisan('appointmentReminder:notify');
        Notification::assertSentTo([$patient], SendDailyAppointmentNotification::class);
    }

    /** @test */
    public function command_successfully_send_daily_appointment_notifications_to_all_patients_have_today()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor2 = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient2 = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $doctor->workingDays()->create(
            [
                "day" => Carbon::parse(now())->dayOfWeek,
                "is_all_day" => "1"
            ]
        );
        $appointments = Appointment::factory()->count(10)->state(new Sequence(
            ['doctor_id' => $doctor->id, 'patient_id' => $patient2->id],
            ['doctor_id' => $doctor2->id, 'patient_id' => $patient->id],
        ))->create([
            'time' => Carbon::parse(now())->timestamp,
            'status' => AppointmentStatus::APPROVED,
        ]);
        Notification::fake();
        Queue::fake();
        $this->artisan('appointmentReminder:notify');
        foreach ($appointments as $appointment) {
            Notification::assertSentTo([$appointment->patient], SendDailyAppointmentNotification::class);
        }
    }
}
