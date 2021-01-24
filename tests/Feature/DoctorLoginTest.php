<?php

namespace Tests\Feature;

use App\Models\Doctor;
use Carbon\Carbon;
use Database\Seeders\DoctorSpecializationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Client;
use Tests\TestCase;

class DoctorLoginTest extends TestCase
{
    use RefreshDatabase;

    public function setup() : void 
    {
        parent::setUp();
        $this->seed(DoctorSpecializationsSeeder::class);
        $this->artisan('passport:client', ['--password' => null, '--no-interaction' => true, '--provider' => 'doctors', '--name' => 'DoctorGrantClient']);
    }

    /** @test */
    public function doctor_successfully_login()
    {
        $this->withoutExceptionHandling();
        $client = Client::where('id', 1)->first();
        $doctor = Doctor::factory()->create(["activated_at" => Carbon::now()]);
        $data = [
            'username' => $doctor->email,
            'password' => '123456789',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => 'password',
        ];
        $response = $this->postJson(route('doctorLogin'), $data, $headers=["Accept"=>"application/json"]);
        $response->assertJsonStructure([
            'accessToken',
            'refreshToken',
        ]);
        $response->assertOk();
    }

    /** @test */
    public function unactivated_doctor_fail_to_login()
    {
        $client = Client::where('id', 1)->first();
        $doctor = Doctor::factory()->create();
        $data = [
            'username' => $doctor->email,
            'password' => '123456789',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => 'password',
        ];
        $response = $this->postJson(route('doctorLogin'), $data);
        $response->assertStatus(401);
    }

    /** @test */
    public function doctor_fail_to_login_with_wrong_password()
    {
        $client = Client::where('id', 1)->first();
        $doctor = Doctor::factory()->create();
        $data = [
            'username' => $doctor->email,
            'password' => '123456',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => 'password',
        ];
        $response = $this->postJson(route('doctorLogin'), $data);
        $response->assertStatus(401);
    }

    /** @test */
    public function doctor_fail_to_login_with_invalid_email()
    {
        $client = Client::where('id', 1)->first();
        $doctor = Doctor::factory()->create();
        $data = [
            'username' => $doctor->email . "dhh",
            'password' => '123456789',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => 'password',
        ];
        $response = $this->postJson(route('doctorLogin'), $data);
        $response->assertJsonValidationErrors('username');
        $response->assertStatus(422);
    }

}
