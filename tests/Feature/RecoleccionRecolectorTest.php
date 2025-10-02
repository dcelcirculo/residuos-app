<?php

namespace Tests\Feature;

use App\Models\EmpresaRecolectora;
use App\Models\Recoleccion;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecoleccionRecolectorTest extends TestCase
{
    use RefreshDatabase;

    public function test_recolector_puede_registrar_kilos_de_inorganico(): void
    {
        $recolector = User::factory()->create(['role' => 'empresa']);
        EmpresaRecolectora::create([
            'user_id' => $recolector->id,
            'nombre' => $recolector->name,
            'especialidades' => ['inorganico'],
        ]);
        $ciudadano = User::factory()->create(['role' => 'user']);

        $solicitud = Solicitud::create([
            'user_id' => $ciudadano->id,
            'tipo_residuo' => 'inorganico',
            'fecha_programada' => now()->toDateString(),
            'frecuencia' => 'programada',
            'recolecciones_por_semana' => 1,
            'turno_ruta' => 12,
            'estado' => 'pendiente',
        ]);

        $response = $this->actingAs($recolector)
            ->post(route('recolecciones.store'), [
                'solicitud_id' => $solicitud->id,
                'kilos' => 18.75,
                'cumple_separacion' => 1,
            ]);

        $response->assertRedirect(route('recolecciones.index'));

        $this->assertDatabaseHas('recolecciones', [
            'solicitud_id' => $solicitud->id,
            'user_id' => $recolector->id,
            'kilos' => '18.75',
        ]);

        $this->assertEquals('recogida', $solicitud->fresh()->estado);
    }

    public function test_recolector_no_puede_registrar_residuos_no_inorganicos(): void
    {
        $recolector = User::factory()->create(['role' => 'empresa']);
        EmpresaRecolectora::create([
            'user_id' => $recolector->id,
            'nombre' => $recolector->name,
            'especialidades' => ['inorganico'],
        ]);
        $ciudadano = User::factory()->create(['role' => 'user']);

        $solicitud = Solicitud::create([
            'user_id' => $ciudadano->id,
            'tipo_residuo' => 'organico',
            'fecha_programada' => now()->toDateString(),
            'frecuencia' => 'programada',
            'recolecciones_por_semana' => 1,
            'turno_ruta' => 8,
            'estado' => 'pendiente',
        ]);

        $response = $this->actingAs($recolector)
            ->post(route('recolecciones.store'), [
                'solicitud_id' => $solicitud->id,
                'kilos' => 10,
                'cumple_separacion' => 1,
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('recolecciones', 0);
        $this->assertEquals('pendiente', $solicitud->fresh()->estado);
    }

    public function test_ciudadano_no_puede_registrar_dos_veces_la_misma_solicitud(): void
    {
        $ciudadano = User::factory()->create(['role' => 'user']);

        $solicitud = Solicitud::create([
            'user_id' => $ciudadano->id,
            'tipo_residuo' => 'inorganico',
            'fecha_programada' => now()->toDateString(),
            'frecuencia' => 'programada',
            'recolecciones_por_semana' => 1,
            'turno_ruta' => 5,
            'estado' => 'pendiente',
        ]);

        $this->actingAs($ciudadano)->post(route('recolecciones.store'), [
            'solicitud_id' => $solicitud->id,
            'kilos' => 9,
            'cumple_separacion' => 1,
        ])->assertRedirect(route('recolecciones.index'));

        $this->actingAs($ciudadano)->post(route('recolecciones.store'), [
            'solicitud_id' => $solicitud->id,
            'kilos' => 4,
            'cumple_separacion' => 1,
        ])->assertStatus(422);

        $this->assertEquals(1, Recoleccion::where('solicitud_id', $solicitud->id)->count());
    }
}
