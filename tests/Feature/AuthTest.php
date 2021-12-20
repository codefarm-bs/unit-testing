<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function register_an_user()
    {
        $response = $this->post('api/register', [
            'email' => 'test@gmail.com',
            'name' => 'Test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertOk();

        $this->assertArrayHasKey('token', $response['data']);
    }

    /** @test */
    public function handle_iterative_email()
    {
        $this->post('api/register', [
            'email' => 'test@gmail.com',
            'name' => 'Test 1',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response = $this->post('api/register', [
            'email' => 'test@gmail.com',
            'name' => 'Test 2',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function handle_unacceptable_password()
    {
        $response = $this->post('api/register', [
            'email' => 'test@gmail.com',
            'name' => 'Test',
            'password' => '1',
            'password_confirmation' => '1',
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function handle_password_confirmation_mismatch()
    {
        $response = $this->post('api/register', [
            'email' => 'test@gmail.com',
            'name' => 'Test',
            'password' => 'password',
            'password_confirmation' => 'secret',
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function login_an_user()
    {
        $this->post('api/register', [
            'email' => 'test@gmail.com',
            'name' => 'Test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response = $this->post('api/login', [
            'email' => 'test@gmail.com',
            'password' => 'password',
        ]);

        $response->assertOk();

        $this->assertArrayHasKey('token', $response['data']);
    }

    /** @test */
    public function handle_login_with_wrong_credential()
    {
        $this->post('api/register', [
            'email' => 'test@gmail.com',
            'name' => 'Test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response = $this->post('api/login', [
            'email' => 'test@gmail.com',
            'password' => 'secret',
        ]);

        $response->assertStatus(401);
    }
}
