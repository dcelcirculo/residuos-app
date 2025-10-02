<?php

namespace Tests\Feature;

use App\Models\EmpresaRecolectora;
use App\Models\Solicitud;
use App\Models\User;
use App\Services\PointsCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RecoleccionPointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2025, 10, 6, 10, 0, 0, 'UTC'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_points_are_assigned_only_when_compliance_is_true(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $solicitud = Solicitud::create([
            'user_id' => $user->id,
            'tipo_residuo' => 'inorganico',
            'fecha_programada' => Carbon::now()->toDateString(),
            'frecuencia' => 'programada',
            'recolecciones_por_semana' => 1,
            'turno_ruta' => 4,
            'estado' => 'pendiente',
        ]);

        $this->actingAs($user)->post(route('recolecciones.store'), [
            'solicitud_id' => $solicitud->id,
            'kilos' => 5,
            'cumple_separacion' => 0,
        ])->assertRedirect(route('recolecciones.index'));

        $this->assertDatabaseHas('recolecciones', [
            'solicitud_id' => $solicitud->id,
            'puntos' => 0,
            'cumple_separacion' => false,
        ]);

        $segundaSolicitud = Solicitud::create([
            'user_id' => $user->id,
            'tipo_residuo' => 'inorganico',
            'fecha_programada' => Carbon::now()->addDay()->toDateString(),
            'frecuencia' => 'programada',
            'recolecciones_por_semana' => 1,
            'turno_ruta' => 5,
            'estado' => 'pendiente',
        ]);

        $this->actingAs($user)->post(route('recolecciones.store'), [
            'solicitud_id' => $segundaSolicitud->id,
            'kilos' => 5,
            'cumple_separacion' => 1,
        ])->assertRedirect(route('recolecciones.index'));

        $this->assertDatabaseHas('recolecciones', [
            'solicitud_id' => $segundaSolicitud->id,
            'cumple_separacion' => true,
            'puntos' => 50,
        ]);
    }

    public function test_updating_formula_changes_points_for_future_collections(): void
    {
        $recolector = User::factory()->create(['role' => 'empresa']);
        EmpresaRecolectora::create([
            'user_id' => $recolector->id,
            'nombre' => $recolector->name,
            'especialidades' => ['inorganico'],
        ]);
        $neighbor = User::factory()->create(['role' => 'user']);

        $solicitud = Solicitud::create([
            'user_id' => $neighbor->id,
            'tipo_residuo' => 'inorganico',
            'fecha_programada' => Carbon::now()->toDateString(),
            'frecuencia' => 'programada',
            'recolecciones_por_semana' => 1,
            'turno_ruta' => 9,
            'estado' => 'pendiente',
        ]);

        /** @var PointsCalculator $calculator */
        $calculator = $this->app->make(PointsCalculator::class);
        $calculator->updateFormula('floor(kilos * 5 + 20)');

        $this->actingAs($recolector)->post(route('recolecciones.store'), [
            'solicitud_id' => $solicitud->id,
            'kilos' => 4,
            'cumple_separacion' => 1,
        ])->assertRedirect(route('recolecciones.index'));

        $this->assertDatabaseHas('recolecciones', [
            'solicitud_id' => $solicitud->id,
            'puntos' => 40,
        ]);
    }
}
