<?php

namespace Tests\Feature;

use App\Models\Admin;
use Database\Seeders\AdminPermissionSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AdminTest extends TestCase
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
        $client = Client::where('id', 1)->first();
        $admin = Admin::where('is_super', 1)->first();
        $data= [
            'username' => $admin->email,
            'password' => 'admin23456789',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => 'password',
        ];
        $response = $this->postJson('/api/admin/login', $data);
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

    /** @test */
    public function superadmin_successfully_add_admin()
    {
        $admin = Admin::where('is_super', 1)->first();
        Passport::actingAs($admin, ['*'], 'admin');
        $data = [
            'name' => 'mohamed',
            'email' => 'adam@gmail.com',
            'phone' => '01225000539',
            'permissions' => [1,2,3],
        ];
        $response = $this->postJson('/api/admins', $data);
        $response->assertCreated();
    }

    /** @test */
    public function superadmin_failed_to_add_admin_with_wrong_email_format()
    {           
        $admin = Admin::where('is_super', 1)->first();
        Passport::actingAs($admin, ['*'], 'admin');
        $data = [
            'name' => 'mohamed',
            'email' => 'adam',
            'phone' => '01225000539',
            'permissions' => [1, 2, 3],
        ];
        $response = $this->postJson('/api/admins', $data);
        $response->assertJsonValidationErrors('email');
        $response->assertStatus(422);
    } 

       /** @test */
       public function superadmin_failed_to_add_admin_with_not_found_permission()
       {
           $admin = Admin::where('is_super', 1)->first();
           Passport::actingAs($admin, ['*'], 'admin');
           $data = [
               'name' => 'mohamed',
               'email' => 'adam@gmail.com',
               'phone' => '01225000539',
               'permissions' => [1, 2, 4],
           ];
           $response = $this->postJson('/api/admins', $data);
           $response->assertJsonValidationErrors('permissions.2');
           $response->assertExactJson([
            "message" =>  "The given data was invalid.",
            "errors" => [
                "permissions.2" => [
                    "The selected permissions.2 is invalid."
                ],
            ]
        ]);
           $response->assertStatus(422);
       }

    /** @test */
    public function ordinaryadmin_unauthorized_to_add_admin()
    {
        Passport::actingAs(Admin::factory()->create(), ['*'], 'admin');
        $data = [
            'name' => 'mohamed',
            'email' => 'adam@gmail.com',
            'phone' => '01225000539',
            'permissions' => [1, 2, 3],
        ];
        $response = $this->postJson('/api/admins', $data);
        $response->assertForbidden();
    }
    
    /** @test */
    public function superadmin_successfully_delete_admin()
    {
        $superAdmin = Admin::where('is_super', 1)->first();
        Passport::actingAs($superAdmin, ['*'], 'admin');
        $admin = Admin::factory()->create();
        $response = $this->deleteJson('/api/admins/'. $admin->id);
        $response->assertOk();  
    }

    /** @test */
    public function admin_fail_to_delete_admin()
    {
        Passport::actingAs(Admin::factory()->create(), ['*'], 'admin');
        $admin = Admin::factory()->create();
        $response = $this->deleteJson('/api/admins/'. $admin->id);
        $response->assertExactJson(["only super admin can delete admin and not be deleted"]);
        $response->assertForbidden();  
    }

    /** @test */
    public function superadmin_fail_to_delete_superadmin()
    {
        $superAdmin = Admin::where('is_super', 1)->first();
        Passport::actingAs($superAdmin, ['*'], 'admin');
        $anotherSuperAdmin = Admin::factory()->create(['is_super' => 1]);
        $response = $this->deleteJson('/api/admins/' . $anotherSuperAdmin->id);
        $response->assertExactJson(["only super admin can delete admin and not be deleted"]);
        $response->assertForbidden();
    }

    /** @test */
    public function superadmin_sucessfully_update_admin()
    {
        $superAdmin = Admin::where('is_super', 1)->first();
        Passport::actingAs($superAdmin, ['*'], 'admin');
        $admin = Admin::factory()->create();
        $data = [
            'name' => 'mohamed',
            'email' => 'adam@gmail.com',
            'phone' => '01225000539',
            'permissions' => [1, 2, 3],
        ];
        $response = $this->putJson('/api/admins/' . $admin->id, $data);
        $response->assertOk();
    }

    /** @test */
    public function admin_not_authorized_to_update()
    {
        $admin = Admin::factory()->create();
        Passport::actingAs($admin, ['*'], 'admin');
        $data = [
            'name' => 'mohamed',
            'email' => 'adam@gmail.com',
            'phone' => '01225000539',
            'permissions' => [1, 2, 3],
        ];
        $response = $this->putJson('/api/admins/' . $admin->id, $data);
        $response->assertExactJson(["You do not authorized to update admin"]);
        $response->assertForbidden();
    }

    /** @test */
    public function superadmin_fail_to_update_admin_with_already_existing_email()
    {
        $superAdmin = Admin::where('is_super', 1)->first();
        $firstAdmin = Admin::factory()->create();
        $secondAdmin = Admin::factory()->create();
        Passport::actingAs($superAdmin, ['*'], 'admin');
        $data = [
            'name' => 'mohamed',
            'email' => $secondAdmin->email,
            'phone' => '01225000539',
            'permissions' => [1, 2, 3],
        ];
        $response = $this->putJson('/api/admins/' . $firstAdmin->id, $data);
        $response->assertJsonValidationErrors('email');
        $response->assertStatus(422);
    }

    /** @test */
    public function superadmin_fail_to_update_admin_with_not_existing_permission()
    {
        $superAdmin = Admin::where('is_super', 1)->first();
        $firstAdmin = Admin::factory()->create();
        Passport::actingAs($superAdmin, ['*'], 'admin');
        $data = [
            'name' => 'mohamed',
            'email' => $firstAdmin->email,
            'phone' => '01225000539',
            'permissions' => [1, 2, 4],
        ];
        $response = $this->putJson('/api/admins/' . $firstAdmin->id, $data);
        $response->assertJsonValidationErrors('permissions.2');
        $response->assertExactJson([
            "message" =>  "The given data was invalid.",
            "errors" => [
                "permissions.2" => [
                    "The selected permissions.2 is invalid."
                ],
            ]
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function superadmin_fail_to_update_not_found_admin_model()
    {
        $superAdmin = Admin::where('is_super', 1)->first();
        Passport::actingAs($superAdmin, ['*'], 'admin');
        $data = [
            'name' => 'mohamed',
            'email' => 'm@gmail.com',
            'phone' => '01225000539',
            'permissions' => [1, 2, 3],
        ];
        $response = $this->putJson('/api/admins/2', $data);
        $response->assertExactJson([
            "error" => "no model  admin with this identifier",
        ]);
        $response->assertNotFound();
    }

    
    
}
