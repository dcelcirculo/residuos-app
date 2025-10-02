<?php

namespace App\Console\Commands;

use App\Models\Solicitud;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class SendSolicitudReminders extends Command
{
    /**
     * Nombre del comando.
     * Permite ejecutar: php artisan solicitudes:recordatorios
     */
    protected $signature = 'solicitudes:recordatorios {--dry-run : Muestra qué solicitudes se notificarían sin enviar mensajes}';

    /**
     * Descripción breve del comando.
     */
    protected $description = 'Envía recordatorios por WhatsApp para las solicitudes programadas (día previo y mismo día).';

    public function __construct(private readonly WhatsAppService $whatsApp)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $canSend = $this->canSendMessages();
        if (!$canSend && !$dryRun) {
            $this->warn('WhatsApp deshabilitado o credenciales incompletas. Ejecuta con --dry-run o configura services.whatsapp.');
            return self::SUCCESS;
        }

        $now = now();
        $windows = [
            [
                'label' => 'día previo',
                'column' => 'recordatorio_prev_enviado_at',
                'date' => $now->copy()->addDay()->toDateString(),
                'prefix' => 'Mañana',
            ],
            [
                'label' => 'mismo día',
                'column' => 'recordatorio_dia_enviado_at',
                'date' => $now->toDateString(),
                'prefix' => 'Hoy',
            ],
        ];

        $total = 0;
        foreach ($windows as $window) {
            $count = $this->processWindow($window, $dryRun);
            $total += $count;
            $this->components->info(sprintf('%s: %d recordatorios procesados', Arr::get($window, 'label'), $count));
        }

        if ($dryRun) {
            $this->comment('Dry run completado. No se enviaron mensajes ni se actualizaron marcas.');
        }

        $this->info(sprintf('Total general: %d solicitudes procesadas.', $total));

        return self::SUCCESS;
    }

    private function processWindow(array $window, bool $dryRun): int
    {
        $processed = 0;
        $date = Arr::get($window, 'date');
        $column = Arr::get($window, 'column');
        $prefix = Arr::get($window, 'prefix');

        Solicitud::with('user')
            ->whereDate('fecha_programada', $date)
            ->whereNull($column)
            ->where('frecuencia', 'programada')
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->whereHas('user', fn ($q) => $q->whereNotNull('phone'))
            ->chunkById(100, function ($solicitudes) use (&$processed, $column, $prefix, $dryRun, $date) {
                foreach ($solicitudes as $solicitud) {
                    $user = $solicitud->user;
                    if (!$user || empty($user->phone)) {
                        continue;
                    }

                    $message = $this->buildMessage($solicitud, $prefix);

                    if ($dryRun) {
                        $this->line(sprintf('• [SIMULADO] #%d %s -> %s (%s)', $solicitud->id, $user->name, $user->phone, $message));
                    } else {
                        $this->whatsApp->send($user->phone, $message);
                        $solicitud->forceFill([$column => now()])->save();
                        $this->line(sprintf('• #%d notificado a %s (%s)', $solicitud->id, $user->name, $user->phone));
                    }

                    $processed++;
                }
            });

        return $processed;
    }

    private function buildMessage(Solicitud $solicitud, string $prefix): string
    {
        $fecha = $solicitud->fecha_programada instanceof Carbon
            ? $solicitud->fecha_programada
            : Carbon::parse($solicitud->fecha_programada);

        $turno = $solicitud->turno_ruta ? '#'.$solicitud->turno_ruta : 'sin turno asignado';
        $tipo = ucfirst((string) $solicitud->tipo_residuo);

        return sprintf(
            '%s %s visitaremos tu domicilio. Residuo: %s. Tu turno en la ruta es %s.',
            $prefix,
            $fecha->format('d/m/Y'),
            $tipo,
            $turno
        );
    }

    private function canSendMessages(): bool
    {
        $config = config('services.whatsapp');

        return (bool) (
            data_get($config, 'enabled') &&
            !empty(data_get($config, 'sid')) &&
            !empty(data_get($config, 'token')) &&
            !empty(data_get($config, 'from'))
        );
    }
}
