<?php

namespace Tests\Feature;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Client;
use Tests\TestCase;
class PatientLoginTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
        $this->artisan('passport:client', ['--password' => null, '--no-interaction' => true, '--provider' => 'patients', '--name' => 'PatientsGrantClient']);
    }

    /** @test */
    public function patient_successfully_login()
    {
        $this->withoutExceptionHandling();
        $client = Client::where('id', 1)->first();
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $data = [
            'email' => $patient->email,
            'password' => '123456789',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => 'password',
        ];
        $response = $this->postJson(route('patientLogin'), $data);
        $response->assertJsonStructure([
            'accessToken',
            'refreshToken',
        ]);
        $response->assertOk();
    }

    /** @test */
    public function notverified_patient_fail_to_login()
    {
        $client = Client::where('id', 1)->first();
        $patient = Patient::factory()->create();
        $data = [
            'email' => $patient->email,
            'password' => '123456789',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => 'password',
        ];
        $response = $this->postJson(route('patientLogin'), $data);
        $response->assertUnauthorized();
    }

    /** @test */
    public function patient_fail_to_login_with_wrong_password()
    {
        $client = Client::where('id', 1)->first();
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $data = [
            'email' => $patient->email,
            'password' => '123456',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => 'password',
        ];
        $response = $this->postJson(route('patientLogin'), $data);
        $response->assertUnauthorized();
    }

    /** @test */
    public function patient_fail_to_login_with_invalid_email()
    {
        $client = Client::where('id', 1)->first();
        $patient = Patient::factory()->create(["verified_at" => Carbon::now()]);
        $data = [
            'email' => $patient->email . 'shdkk',
            'password' => '123456789',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => 'password',
        ];
        $response = $this->postJson(route('patientLogin'), $data);
        $response->assertJsonValidationErrors('email');
        $response->assertStatus(422);
    }

}
