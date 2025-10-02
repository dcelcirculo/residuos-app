<?php

namespace Tests\Feature;

use App\Models\EmpresaRecolectora;
use App\Models\Recoleccion;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RecoleccionExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2025, 10, 6, 9, 0, 0, 'UTC'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_user_can_download_their_recolecciones_report(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $solicitud = Solicitud::create([
            'user_id' => $user->id,
            'tipo_residuo' => 'inorganico',
            'fecha_programada' => Carbon::now()->toDateString(),
            'frecuencia' => 'programada',
            'recolecciones_por_semana' => 1,
            'turno_ruta' => 3,
            'estado' => 'recogida',
        ]);

        Recoleccion::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => $user->id,
            'fecha_real' => Carbon::now()->subHour(),
            'kilos' => 12.5,
            'puntos' => 120,
            'cumple_separacion' => true,
        ]);

        $response = $this->actingAs($user)
            ->get(route('recolecciones.export'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $csv = $response->streamedContent();

        $this->assertStringContainsString('Empresa', $csv);
        $this->assertStringContainsString('12.50', $csv);
    }

    public function test_recolector_receives_neighbor_name_in_report(): void
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
            'turno_ruta' => 7,
            'estado' => 'recogida',
        ]);

        Recoleccion::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => $recolector->id,
            'fecha_real' => Carbon::now()->subMinutes(30),
            'kilos' => 8.75,
            'puntos' => 80,
            'cumple_separacion' => true,
        ]);

        $response = $this->actingAs($recolector)
            ->get(route('recolecciones.export'));

        $response->assertOk();

        $csv = $response->streamedContent();

        $this->assertStringContainsString($neighbor->name, $csv);
        $this->assertStringContainsString('8.75', $csv);
    }

    public function test_admin_cannot_access_recolecciones_export(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('recolecciones.export'))
            ->assertStatus(403);
    }
}
