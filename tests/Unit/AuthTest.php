<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name'                  => 'João Silva',
            'email'                 => 'joao@email.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'access_token',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'joao@email.com',
        ]);
    }

    public function test_user_cannot_register_with_existing_email(): void
    {
        User::factory()->create(['email' => 'joao@email.com']);

        $response = $this->postJson('/api/register', [
            'name'                  => 'João Silva',
            'email'                 => 'joao@email.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(400)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_user_can_login(): void
    {
        User::factory()->create([
            'email'    => 'joao@email.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'joao@email.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'access_token',
            ]);
    }

    public function test_user_cannot_login_with_wrong_credentials(): void
    {
        User::factory()->create(['email' => 'joao@email.com']);

        $response = $this->postJson('/api/login', [
            'email'    => 'joao@email.com',
            'password' => 'senha_errada',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Credenciais inválidas']);
    }

    public function test_user_can_logout(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logout realizado com sucesso.']);
    }

    public function test_unauthenticated_user_cannot_logout(): void
    {
        $response = $this->postJson('/api/logout');
        $response->assertStatus(401);
    }
}
