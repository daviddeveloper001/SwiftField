<?php

namespace App\Services\Booking;

use App\Models\Service;
use App\Models\Availability;
use App\Models\Booking;
use App\Enums\BookingStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    /**
     * Algoritmo de Ventana Deslizable: 
     * Retorna solo las horas de inicio donde el servicio COMPLETO quepa de forma continua.
     */
    public function getAvailableSlots(int $serviceId, string $date): array
    {
        $service = Service::findOrFail($serviceId);
        $tenantId = $service->tenant_id;
        $duration = (int) $service->duration_minutes;
        $carbonDate = Carbon::parse($date);
        
        // 1. Horario laboral del Tenant
        $availability = Availability::where('tenant_id', $tenantId)
            ->where('day_of_week', $carbonDate->dayOfWeek)
            ->where('is_open', true)
            ->first();

        if (!$availability || !$availability->start_time || !$availability->end_time) {
            return [];
        }

        // 2. Límites operativos del día
        $workStart = Carbon::parse($date . ' ' . $availability->start_time->format('H:i'));
        $workEnd = Carbon::parse($date . ' ' . $availability->end_time->format('H:i'));
        
        // Margen para hoy
        if ($carbonDate->isToday()) {
            $now = Carbon::now()->addMinutes(15)->ceilMinute(15);
            if ($now->gt($workStart)) {
                $workStart = $now;
            }
        }

        if ($workStart->copy()->addMinutes($duration)->gt($workEnd)) {
            return [];
        }

        // 3. Reservas existentes (mapeadas a rangos ocupados)
        $occupiedRanges = Booking::where('tenant_id', $tenantId)
            ->whereDate('scheduled_at', $date)
            ->whereNotIn('status', [BookingStatus::Cancelled])
            ->with('service')
            ->get()
            ->map(function ($booking) {
                $start = Carbon::parse($booking->scheduled_at);
                $dur = (int) ($booking->service->duration_minutes ?? 60);
                return [
                    'start' => $start,
                    'end' => $start->copy()->addMinutes($dur)
                ];
            });

        // 4. Generación de slots con Ventana Deslizable
        $slots = [];
        $current = $workStart->copy();
        
        // El intervalo de búsqueda es cada 30 minutos (ajustable)
        $searchInterval = 30;

        while ($current->copy()->addMinutes($duration)->lte($workEnd)) {
            $windowStart = $current->copy();
            $windowEnd = $current->copy()->addMinutes($duration);

            // Verificar si la VENTANA COMPLETA está libre
            $isConflict = $occupiedRanges->contains(function ($range) use ($windowStart, $windowEnd) {
                // Hay traslape si: (Ventana_Inicio < Rango_Fin) Y (Ventana_Fin > Rango_Inicio)
                return $windowStart->lt($range['end']) && $windowEnd->gt($range['start']);
            });

            if (!$isConflict) {
                $slots[] = $windowStart->format('H:i');
            }

            $current->addMinutes($searchInterval);
        }

        return $slots;
    }

    /**
     * Valida si un slot específico sigue estando disponible.
     */
    public function isSlotAvailable(int $serviceId, string $date, string $time): bool
    {
        $availableSlots = $this->getAvailableSlots($serviceId, $date);
        return in_array($time, $availableSlots);
    }
}
