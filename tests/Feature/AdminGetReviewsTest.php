<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Models\Admin;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Review;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use PhpParser\Comment\Doc;
use Tests\TestCase;

class AdminGetReviewsTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
    }

    /** @test */
    public function Admin_successfully_get_all_reviews()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $admin = Admin::factory()->create();
        $doctor->workingDays()->create(
            [
                "day"=> "1",
                "is_all_day"=> "1"
            ]
        );
        Passport::actingAs($admin, ['*'], 'admin');
        Review::factory()->count(3)->for(Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::COMPLETED,
        ]))->create();
        $response = $this->getJson(route('reviews.index'), ["Accept" => "application/json"]);
        $response->assertOk();
        $response->assertJsonCount(3);
    }

    /** @test */
    public function Admin_successfully_get_reviews_filtered_by_doctor()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $doctor2 = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $admin = Admin::factory()->create();
        $doctor->workingDays()->create(
            [
                "day" => "1",
                "is_all_day" => "1"
            ]
        );
        Passport::actingAs($admin, ['*'], 'admin');
        Review::factory()->count(3)->for(Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::COMPLETED,
        ]))->create();
        Review::factory()->count(5)->for(Appointment::factory()->state([
            'doctor_id' => $doctor2->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::COMPLETED,
        ]))->create();
        $response = $this->getJson(route('reviews.index', ['byDoctor' => $doctor2->id]), ["Accept" => "application/json"]);
        $response->assertOk();
        $response->assertJsonCount(5);
    }
}
