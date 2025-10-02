<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function send(string $to, string $message): void
    {
        $config = config('services.whatsapp');

        if (!data_get($config, 'enabled') || empty($config['from']) || empty($config['sid']) || empty($config['token'])) {
            Log::channel('stack')->info('WhatsApp notifications skipped', [
                'to' => $to,
                'message' => $message,
            ]);

            return;
        }

        $from = $this->normalize($config['from']);
        $recipient = $this->normalize($to);

        $url = sprintf('https://api.twilio.com/2010-04-01/Accounts/%s/Messages.json', $config['sid']);

        $payload = [
            'From' => 'whatsapp:' . $from,
            'To' => 'whatsapp:' . $recipient,
            'Body' => $message,
        ];

        try {
            Http::withBasicAuth($config['sid'], $config['token'])
                ->asForm()
                ->post($url, $payload)
                ->throw();
        } catch (\Throwable $e) {
            Log::channel('stack')->error('Error enviando mensaje de WhatsApp', [
                'to' => $recipient,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function normalize(string $phone): string
    {
        $phone = trim($phone);
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . ltrim($phone, '+');
        }

        return $phone;
    }
}
