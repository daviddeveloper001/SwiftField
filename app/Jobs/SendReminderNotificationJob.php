<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Messaging\Contracts\NotificationDriverInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job que envía un recordatorio de re-agendamiento a través de un driver específico.
 *
 * Se despacha en paralelo desde el comando send-service-reminders,
 * uno por cada canal activo devuelto por NotificationDriverFactory.
 */
class SendReminderNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Número máximo de reintentos.
     */
    public int $tries = 3;

    /**
     * @param string $driverClass Fully qualified class name del driver
     * @param string $to          Destinatario (teléfono, email, etc.)
     * @param string $message     Mensaje en texto plano
     */
    public function __construct(
        private readonly string $driverClass,
        private readonly string $to,
        private readonly string $message,
    ) {}

    /**
     * Ejecuta el Job resolviendo el driver desde el container.
     */
    public function handle(): void
    {
        if (!class_exists($this->driverClass)) {
            Log::error('[SendReminderNotificationJob] Driver class not found', [
                'driver' => $this->driverClass,
            ]);
            return;
        }

        /** @var NotificationDriverInterface $driver */
        $driver = app($this->driverClass);

        if (!$driver instanceof NotificationDriverInterface) {
            Log::error('[SendReminderNotificationJob] Invalid driver instance', [
                'driver' => $this->driverClass,
            ]);
            return;
        }

        $driver->send($this->to, $this->message);

        Log::info('[SendReminderNotificationJob] Reminder sent successfully', [
            'driver' => class_basename($this->driverClass),
            'to'     => $this->to,
        ]);
    }
}
