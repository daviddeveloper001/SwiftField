<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Constants\ReminderConstants;
use App\Enums\BookingStatus;
use App\Jobs\SendReminderNotificationJob;
use App\Models\Booking;
use App\Models\NotificationChannel;
use App\Services\Messaging\NotificationDriverFactory;
use App\Services\Messaging\TemplateProcessor;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Comando programado para enviar recordatorios de re-agendamiento.
 *
 * Se ejecuta diariamente y busca citas completadas cuyo periodo de
 * re-agendamiento coincida con el día actual. Aplica un filtro antispam
 * para no notificar a clientes que ya tienen una cita futura agendada.
 */
class SendServiceReminders extends Command
{
    protected $signature = 'app:send-service-reminders';

    protected $description = 'Envía recordatorios de re-agendamiento a clientes con citas completadas cuyo periodo ha vencido.';

    public function __construct(
        private readonly TemplateProcessor $templateProcessor,
        private readonly NotificationDriverFactory $driverFactory,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $today = Carbon::today();

        $this->info("[Reminders] Ejecutando para fecha: {$today->toDateString()}");

        // 1. Buscar bookings completados con servicio que tiene reminder activo
        $bookings = Booking::query()
            ->with(['service', 'customer', 'tenant'])
            ->whereHas('service', fn ($q) => $q->where('has_reorder_reminder', true))
            ->where('status', BookingStatus::Completed->value)
            ->whereNotNull('completed_at')
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('[Reminders] No hay citas completadas con recordatorio activo.');
            return self::SUCCESS;
        }

        $activeDriverClasses = $this->getActiveDriverClasses();

        if (empty($activeDriverClasses)) {
            $this->warn('[Reminders] No hay canales de notificación activos.');
            return self::SUCCESS;
        }

        $sent = 0;
        $skippedAntispam = 0;
        $skippedDate = 0;

        foreach ($bookings as $booking) {
            $service = $booking->service;

            // Calcular la fecha de re-agendamiento esperada
            $reminderDate = $this->calculateReminderDate(
                $booking->completed_at,
                (int) $service->reorder_value,
                $service->reorder_unit,
            );

            // Solo procesar si la fecha de recordatorio coincide con hoy
            if (!$reminderDate || !$reminderDate->isSameDay($today)) {
                $skippedDate++;
                continue;
            }

            // REGLA ANTISPAM: Verificar si el cliente ya tiene cita futura para este servicio
            $hasFutureBooking = Booking::query()
                ->where('customer_id', $booking->customer_id)
                ->where('service_id', $booking->service_id)
                ->whereIn('status', [
                    BookingStatus::Pending->value,
                    BookingStatus::Confirmed->value,
                ])
                ->where('scheduled_at', '>=', $today)
                ->exists();

            if ($hasFutureBooking) {
                $skippedAntispam++;
                Log::info('[Reminders] Antispam: cliente ya tiene cita futura', [
                    'booking_id'  => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'service_id'  => $booking->service_id,
                ]);
                continue;
            }

            // Procesar plantilla
            $template = $service->reorder_message_template;

            if (empty($template)) {
                Log::warning('[Reminders] Servicio sin plantilla de mensaje', [
                    'service_id' => $service->id,
                ]);
                continue;
            }

            $processedMessage = $this->templateProcessor->process($template, $booking);

            // Obtener teléfono del cliente (formato raw con prefijo internacional)
            $customerPhone = $booking->customer?->getRawOriginal('phone') ?? '';

            if (empty($customerPhone)) {
                Log::warning('[Reminders] Cliente sin teléfono', [
                    'customer_id' => $booking->customer_id,
                ]);
                continue;
            }

            // Despachar Jobs en paralelo para cada canal activo
            foreach ($activeDriverClasses as $driverClass) {
                SendReminderNotificationJob::dispatch(
                    $driverClass,
                    $customerPhone,
                    $processedMessage,
                );
            }

            $sent++;
        }

        $this->info("[Reminders] Completado: {$sent} enviados, {$skippedAntispam} filtrados por antispam, {$skippedDate} fuera de fecha.");

        Log::info('[Reminders] Ejecución completada', [
            'date'              => $today->toDateString(),
            'sent'              => $sent,
            'skipped_antispam'  => $skippedAntispam,
            'skipped_date'      => $skippedDate,
        ]);

        return self::SUCCESS;
    }

    /**
     * Calcula la fecha de recordatorio sumando el periodo al completed_at.
     */
    private function calculateReminderDate(Carbon $completedAt, int $value, ?string $unit): ?Carbon
    {
        if ($value <= 0 || empty($unit)) {
            return null;
        }

        return match ($unit) {
            ReminderConstants::UNIT_DAYS   => $completedAt->copy()->addDays($value),
            ReminderConstants::UNIT_WEEKS  => $completedAt->copy()->addWeeks($value),
            ReminderConstants::UNIT_MONTHS => $completedAt->copy()->addMonths($value),
            default => null,
        };
    }

    /**
     * Obtiene las clases de drivers activos desde la base de datos.
     *
     * @return string[]
     */
    private function getActiveDriverClasses(): array
    {
        return NotificationChannel::query()
            ->where('is_active', true)
            ->pluck('driver')
            ->toArray();
    }
}
