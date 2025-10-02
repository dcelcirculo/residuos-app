<?php

namespace Tests\Feature;

use App\Models\Solicitud;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

class SolicitudReminderCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Mockery::close();

        parent::tearDown();
    }

    public function test_it_sends_reminders_for_today_and_tomorrow(): void
    {
        Carbon::setTestNow(Carbon::create(2025, 10, 5, 9, 0, 0, 'UTC'));

        config(['services.whatsapp' => [
            'enabled' => true,
            'sid' => 'ACXXXX',
            'token' => 'secret',
            'from' => '+573001112233',
        ]]);

        $user = User::factory()->create([
            'role' => 'user',
            'phone' => '+573001234567',
        ]);

        $tomorrow = Solicitud::create([
            'user_id' => $user->id,
            'tipo_residuo' => 'organico',
            'fecha_programada' => Carbon::now()->addDay()->toDateString(),
            'frecuencia' => 'programada',
            'recolecciones_por_semana' => 1,
            'turno_ruta' => 7,
            'estado' => 'pendiente',
        ]);

        $today = Solicitud::create([
            'user_id' => $user->id,
            'tipo_residuo' => 'inorganico',
            'fecha_programada' => Carbon::now()->toDateString(),
            'frecuencia' => 'programada',
            'recolecciones_por_semana' => 2,
            'turno_ruta' => 3,
            'estado' => 'pendiente',
        ]);

        $whatsApp = Mockery::mock(WhatsAppService::class);
        $whatsApp->shouldReceive('send')->twice();
        $this->app->instance(WhatsAppService::class, $whatsApp);

        $this->artisan('solicitudes:recordatorios')
            ->assertExitCode(0);

        $this->assertNotNull($tomorrow->fresh()->recordatorio_prev_enviado_at);
        $this->assertNull($tomorrow->fresh()->recordatorio_dia_enviado_at);
        $this->assertNotNull($today->fresh()->recordatorio_dia_enviado_at);
    }

    public function test_dry_run_does_not_send_messages_or_update_marks(): void
    {
        Carbon::setTestNow(Carbon::create(2025, 10, 5, 9, 0, 0, 'UTC'));

        config(['services.whatsapp' => [
            'enabled' => true,
            'sid' => 'ACXXXX',
            'token' => 'secret',
            'from' => '+573001112233',
        ]]);

        $user = User::factory()->create([
            'role' => 'user',
            'phone' => '+573001234567',
        ]);

        $solicitud = Solicitud::create([
            'user_id' => $user->id,
            'tipo_residuo' => 'peligroso',
            'fecha_programada' => Carbon::now()->addDay()->toDateString(),
            'frecuencia' => 'programada',
            'recolecciones_por_semana' => 1,
            'turno_ruta' => 9,
            'estado' => 'pendiente',
        ]);

        $whatsApp = Mockery::mock(WhatsAppService::class);
        $whatsApp->shouldNotReceive('send');
        $this->app->instance(WhatsAppService::class, $whatsApp);

        $this->artisan('solicitudes:recordatorios --dry-run')
            ->assertExitCode(0);

        $fresh = $solicitud->fresh();
        $this->assertNull($fresh->recordatorio_prev_enviado_at);
        $this->assertNull($fresh->recordatorio_dia_enviado_at);
    }
}
