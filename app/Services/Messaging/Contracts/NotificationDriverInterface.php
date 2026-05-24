<?php

declare(strict_types=1);

namespace App\Services\Messaging\Contracts;

/**
 * Contrato para cualquier driver de notificación (WhatsApp, SMS, Email, etc.).
 *
 * Cada driver recibe el mensaje final en texto plano y es responsable
 * de traducirlo a la sintaxis específica de su canal antes de enviarlo.
 */
interface NotificationDriverInterface
{
    /**
     * Envía un mensaje a un destinatario específico.
     *
     * @param string $to   Identificador del destinatario (teléfono, email, etc.)
     * @param string $message Mensaje final en texto plano
     * @return void
     */
    public function send(string $to, string $message): void;
}
