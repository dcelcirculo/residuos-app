<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_users_of_any_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $roles = ['user', 'admin', 'empresa'];

        foreach ($roles as $index => $role) {
            $payload = [
                'name' => 'Persona '.$role,
                'email' => $role.$index.'@example.com',
                'role' => $role,
                'phone' => '+5730011122'.sprintf('%02d', $index),
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ];

            if ($role === 'empresa') {
                $payload['especialidades'] = ['organico', 'inorganico'];
            }

            $response = $this->actingAs($admin)
                ->post(route('admin.users.store'), $payload);

            $response->assertRedirect();

            $created = User::where('email', $payload['email'])->first();

            $this->assertNotNull($created);
            $this->assertSame($role, $created->role);

            if ($role === 'empresa') {
                $this->assertDatabaseHas('empresa_recolectoras', [
                    'user_id' => $created->id,
                ]);
            }
        }
    }

    public function test_non_admin_cannot_create_users(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->post(route('admin.users.store'), [
                'name' => 'Test',
                'email' => 'test@example.com',
                'role' => 'user',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    public function test_admin_can_update_points_formula(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.settings.points'), [
                'formula' => 'min(floor(kilos * 15), 1000)',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('settings', [
            'key' => 'points.formula',
            'value' => 'min(floor(kilos * 15), 1000)',
        ]);
    }
}
