<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Notifications\AdminRegistrationMail;
use Database\Seeders\AdminPermissionSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AddAdminTest extends TestCase
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
    public function superadmin_successfully_add_admin()
    {
        Notification::fake();
        Queue::fake();
        $admin = Admin::where('is_super', 1)->first();
        Passport::actingAs($admin, ['*'], 'admin');
        $data = [
            'name' => 'mohamed',
            'email' => 'adam@gmail.com',
            'phone' => '01225000539',
            'permissions' => [1,2,3],
        ];
        $response = $this->postJson(route('admins.store'), $data);
        $admin = Admin::where('email',$data['email'])->first();
        Notification::assertSentTo([$admin], AdminRegistrationMail::class);
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
        $response = $this->postJson(route('admins.store'), $data);
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
           $response = $this->postJson(route('admins.store'), $data);
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
        $response = $this->postJson(route('admins.store'), $data);
        $response->assertForbidden();
    }
}
