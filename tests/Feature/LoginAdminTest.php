<?php

namespace Tests\Feature;

use App\Models\Admin;
use Database\Seeders\AdminPermissionSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Client;
use Tests\TestCase;

class LoginAdminTest extends TestCase
{
    use RefreshDatabase;

    public function setup() : void 
    {
        parent::setUp();
        $this->seed(SuperAdminSeeder::class);
        $this->seed(AdminPermissionSeeder::class);
        $this->artisan('passport:client', ['--password' => null, '--no-interaction' => true, '--provider' => 'admins', '--name' => 'AdminGrantClient']);
    }

    /** @test */
    public function superadmin_successfully_login()
    {
        $this->withoutExceptionHandling();
        $client = Client::where('id', 1)->first();
        $admin = Admin::where('is_super', 1)->first();
        // dd($admin->is_super);
        $data= [
            'username' => $admin->email,
            'password' => 'admin23456789',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => 'password',
        ];
        $response = $this->postJson('/api/admin/login', $data, $headers=["Accept"=>"application/json"]);
        $response->assertJsonStructure([
            'token_type'  ,
            'expires_in'  ,
            'access_token'  ,
            'refresh_token'  ,
        ]);
        $response->assertOk();

    }

    /** @test */
    public function superadmin_fail_to_authenticate_with_wrong_password()
    {
        $client = Client::where('id', 1)->first();
        $admin = Admin::where('is_super', 1)->first();
        $data = [
            'username' => $admin->email,
            'password' => 'admin234',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => 'password',
        ];
        $response = $this->postJson('/api/admin/login', $data);
        $response->assertStatus(401);
    }

    /** @test */
    public function superadmin_fail_to_authenticate_with_invalid_credntials()
    {
        $client = Client::where('id', 1)->first();
        $data = [
            'username' => 'mohamed',
            'password' => 'adm',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => 'password',
        ];
        $response = $this->postJson('/api/admin/login', $data);
        $response->assertExactJson([
            "message" =>  "The given data was invalid.",
            "errors" => [
                "username" => [
                    "The username must be a valid email address."
                ],
                "password" => [
                    "The password must be at least 4 characters."
                ]
            ]
        ]);
        $response->assertStatus(422);
    }


    
   



    
    
}
