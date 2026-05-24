<?php

declare(strict_types=1);

namespace App\Services\Messaging;

use App\Models\NotificationChannel;
use App\Services\Messaging\Contracts\NotificationDriverInterface;
use Illuminate\Support\Facades\Log;

/**
 * Factory que consulta los canales activos en la base de datos
 * e instancia dinámicamente sus respectivos drivers.
 *
 * Arquitectura Zero Deploy: activar/desactivar canales se hace
 * desde la tabla notification_channels sin necesidad de re-deploy.
 */
class NotificationDriverFactory
{
    /**
     * Devuelve una colección de drivers activos listos para envío.
     *
     * @return NotificationDriverInterface[]
     */
    public function getActiveDrivers(): array
    {
        $channels = NotificationChannel::query()
            ->where('is_active', true)
            ->get();

        $drivers = [];

        foreach ($channels as $channel) {
            $driverClass = $channel->driver;

            if (!class_exists($driverClass)) {
                Log::warning('[NotificationDriverFactory] Driver class not found', [
                    'channel' => $channel->name,
                    'driver'  => $driverClass,
                ]);
                continue;
            }

            $driver = app($driverClass);

            if (!$driver instanceof NotificationDriverInterface) {
                Log::warning('[NotificationDriverFactory] Driver does not implement NotificationDriverInterface', [
                    'channel' => $channel->name,
                    'driver'  => $driverClass,
                ]);
                continue;
            }

            $drivers[] = $driver;
        }

        return $drivers;
    }
}
