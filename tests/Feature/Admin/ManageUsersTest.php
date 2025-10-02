<?php

namespace Tests\Feature\Admin;

use App\Models\EmpresaRecolectora;
use App\Models\Recoleccion;
use App\Models\Solicitud;
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

            $payload['localidad'] = 'Localidad '.$index;

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

    public function test_admin_can_update_recoleccion_compliance(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $company = User::factory()->create(['role' => 'empresa']);
        EmpresaRecolectora::create([
            'user_id' => $company->id,
            'nombre' => $company->name,
            'especialidades' => ['organico'],
        ]);

        $neighbor = User::factory()->create(['role' => 'user']);

        $solicitud = Solicitud::create([
            'user_id' => $neighbor->id,
            'tipo_residuo' => 'organico',
            'fecha_programada' => now()->toDateString(),
            'frecuencia' => 'programada',
            'recolecciones_por_semana' => 1,
            'turno_ruta' => 6,
            'estado' => 'pendiente',
        ]);

        $recoleccion = Recoleccion::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => $company->id,
            'fecha_real' => now(),
            'kilos' => 5,
            'puntos' => 0,
            'cumple_separacion' => false,
        ]);

        $this->from(route('admin.users.manage'))
            ->actingAs($admin)
            ->patch(route('admin.recolecciones.update', $recoleccion), [
                'cumple_separacion' => 1,
            ])
            ->assertRedirect(route('admin.users.manage'));

        $this->assertDatabaseHas('recolecciones', [
            'id' => $recoleccion->id,
            'cumple_separacion' => true,
            'puntos' => 50,
        ]);
    }
}
