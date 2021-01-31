<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Notifications\DoctorActivationMail;
use Carbon\Carbon;
use Database\Seeders\AdminPermissionSeeder;
use Database\Seeders\DoctorSpecializationsSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ActivateDoctorTest extends TestCase
{
    use RefreshDatabase;

    public function setup() : void 
    {
        parent::setUp();
        $this->seed(SuperAdminSeeder::class);
        $this->seed(AdminPermissionSeeder::class);
        $this->seed(DoctorSpecializationsSeeder::class);
    }

    /** @test */
    public function superadmin_successfully_activate_doctor()
    {
        $this->withoutExceptionHandling();
        Notification::fake();
        Queue::fake();
        $admin = Admin::where('is_super', 1)->first();
        Passport::actingAs($admin, ['*'], 'admin');
        $doctor = Doctor::factory()->create();
        $response = $this->postJson(route('activate.doctor', ['doctor' => $doctor->id]));
        Notification::assertSentTo([$doctor], DoctorActivationMail::class);
        $response->assertOk();
    }

    /** @test */
    public function admin_has_permission_control_doctor_successfully_activate_doctor()
    {
        Notification::fake();
        Queue::fake();
        $admin = Admin::factory()->create();
        $admin->givePermissionTo('control doctors');
        Passport::actingAs($admin, ['*'], 'admin');
        $doctor = Doctor::factory()->create();
        $response = $this->postJson(route('activate.doctor', ['doctor' => $doctor->id]));
        Notification::assertSentTo([$doctor], DoctorActivationMail::class);
        $response->assertOk();
    }

    /** @test */
    public function admin_has_no_permission_control_doctor_unauthorized_to_activate_doctor()
    {
        Notification::fake();
        Queue::fake();
        $admin = Admin::factory()->create();
        Passport::actingAs($admin, ['*'], 'admin');
        $doctor = Doctor::factory()->create();
        $response = $this->postJson(route('activate.doctor', ['doctor' => $doctor->id]));
        Notification::assertNotSentTo([$doctor], DoctorActivationMail::class);
        $response->assertForbidden();
    }

    /** @test */
    public function super_can_not_activate_doctor_that_already_activated_before()
    {
        Notification::fake();
        Queue::fake();
        $admin = Admin::where('is_super', 1)->first();
        Passport::actingAs($admin, ['*'], 'admin');
        $activatedDoctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $response = $this->postJson(route('activate.doctor', ['doctor' => $activatedDoctor->id]));
        Notification::assertNotSentTo([$activatedDoctor], DoctorActivationMail::class);
        $response->assertStatus(400);
    }
}
