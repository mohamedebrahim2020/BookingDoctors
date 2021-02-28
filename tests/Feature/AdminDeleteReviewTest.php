<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Models\Admin;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Review;
use Carbon\Carbon;
use Database\Seeders\AdminPermissionSeeder;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use PhpParser\Comment\Doc;
use Tests\TestCase;

class AdminDeleteReviewTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
        $this->seed(AdminPermissionSeeder::class);
    }

    /** @test */
    public function Admin_successfully_delete_review()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $admin = Admin::factory()->create();
        $admin->givePermissionTo('control doctors');
        $doctor->workingDays()->create(
            [
                "day"=> "1",
                "is_all_day"=> "1"
            ]
        );
        Passport::actingAs($admin, ['*'], 'admin');
        $review = Review::factory()->for(Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::COMPLETED,
        ]))->create();
        $response = $this->deleteJson(route('reviews.delete', ['review' => $review->id]), ["Accept" => "application/json"]);
        $response->assertOk();
        $this->assertSoftDeleted($review);
    }

    /** @test */
    public function Admin_failed_to_delete_not_found_review()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $admin = Admin::factory()->create();
        $admin->givePermissionTo('control doctors');
        $doctor->workingDays()->create(
            [
                "day" => "1",
                "is_all_day" => "1"
            ]
        );
        Passport::actingAs($admin, ['*'], 'admin');
        Review::factory()->for(Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::COMPLETED,
        ]))->create();
        $response = $this->deleteJson(route('reviews.delete', ['review' => 2]), ["Accept" => "application/json"]);
        $response->assertNotFound();
    }

    /** @test */
    public function admin_with_no_permission_failed_to_delete_review()
    {
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $admin = Admin::factory()->create();
        $doctor->workingDays()->create(
            [
                "day" => "1",
                "is_all_day" => "1"
            ]
        );
        Passport::actingAs($admin, ['*'], 'admin');
        $review = Review::factory()->for(Appointment::factory()->state([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::COMPLETED,
        ]))->create();
        $response = $this->deleteJson(route('reviews.delete', ['review' => $review->id]), ["Accept" => "application/json"]);
        $response->assertForbidden();
    }
}