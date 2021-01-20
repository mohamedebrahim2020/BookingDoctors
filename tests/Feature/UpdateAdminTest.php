<?php

namespace Tests\Feature;

use App\Models\Admin;
use Database\Seeders\AdminPermissionSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdateAdminTest extends TestCase
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
