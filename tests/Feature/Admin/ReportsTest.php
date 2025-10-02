<?php

namespace Tests\Feature\Admin;

use App\Models\EmpresaRecolectora;
use App\Models\Recoleccion;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $neighbor;
    private User $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->neighbor = User::factory()->create([
            'role' => 'user',
            'localidad' => 'Centro',
        ]);
        $this->company = User::factory()->create(['role' => 'empresa']);

        EmpresaRecolectora::create([
            'user_id' => $this->company->id,
            'nombre' => $this->company->name,
            'especialidades' => ['organico', 'inorganico'],
        ]);

        $solicitud = Solicitud::create([
            'user_id' => $this->neighbor->id,
            'tipo_residuo' => 'inorganico',
            'fecha_programada' => now()->toDateString(),
            'frecuencia' => 'programada',
            'recolecciones_por_semana' => 1,
            'turno_ruta' => 10,
            'estado' => 'pendiente',
        ]);

        Recoleccion::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => $this->company->id,
            'fecha_real' => now(),
            'kilos' => 15,
            'cumple_separacion' => true,
            'puntos' => 150,
        ]);
    }

    public function test_admin_can_view_user_report(): void
    {
        $this->actingAs($this->admin)
            ->get(route('reports.user', [
                'user_id' => $this->neighbor->id,
                'from' => now()->subDay()->toDateString(),
                'to' => now()->addDay()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('15.00')
            ->assertSee('150');
    }

    public function test_admin_can_view_global_summary(): void
    {
        $this->actingAs($this->admin)
            ->get(route('reports.users', [
                'localidad' => 'Centro',
                'from' => now()->subDay()->toDateString(),
                'to' => now()->addDay()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Inorganico')
            ->assertSee('15.00');
    }

    public function test_admin_can_view_company_report(): void
    {
        $this->actingAs($this->admin)
            ->get(route('reports.company', [
                'company_id' => $this->company->id,
                'tipo_residuo' => 'inorganico',
                'from' => now()->subDay()->toDateString(),
                'to' => now()->addDay()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee($this->neighbor->name)
            ->assertSee('15.00');
    }

    public function test_non_admin_cannot_access_reports(): void
    {
        $this->actingAs($this->neighbor)
            ->get(route('reports.user'))
            ->assertStatus(403);
    }
}
