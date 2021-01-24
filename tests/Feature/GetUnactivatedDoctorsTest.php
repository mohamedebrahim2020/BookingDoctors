<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class GetUnactivatedDoctorsTest extends TestCase
{
    use RefreshDatabase;

    public function setup() : void 
    {
        parent::setUp();
        $this->seed(SuperAdminSeeder::class);
        $this->seed(DoctorSpecializationsSeeder::class);
    }

    /** @test */
    public function superadmin_successfully_get_unactivated_doctors()
    {
        $this->withoutExceptionHandling();
        $admin = Admin::where('is_super', 1)->first();
        Doctor::factory()->count(60)->create(["activated_at" => Carbon::now()]);
        Doctor::factory()->count(40)->create();
        Passport::actingAs($admin, ['*'], 'admin');
        $response = $this->getJson(route('doctors.index', ["active" => 0]));
        $response->assertOk();
        $response->assertJsonCount(40, $key = null);
        $this->assertDatabaseCount('doctors', 100);
    }

    /** @test */
    public function admin_successfully_get_unactivated_doctors()
    {
        $admin = Admin::factory()->create();
        Doctor::factory()->count(60)->create(["activated_at" => Carbon::now()]);
        Doctor::factory()->count(40)->create();
        Passport::actingAs($admin, ['*'], 'admin');
        $response = $this->getJson(route('doctors.index', ["active" => 0]));
        $response->assertOk();
        $response->assertJsonCount(40, $key = null);
        $this->assertDatabaseCount('doctors', 100);
    }
}
