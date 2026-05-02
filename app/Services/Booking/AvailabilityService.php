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

        if (!$availability || empty($availability->ranges)) {
            return [];
        }

        // 2. Reservas existentes y Bloqueos temporales
        $occupiedRanges = collect();

        Booking::where('tenant_id', $tenantId)
            ->whereDate('scheduled_at', $date)
            ->whereNotIn('status', [BookingStatus::Cancelled])
            ->with('service')
            ->get()
            ->each(function ($booking) use ($occupiedRanges) {
                $start = Carbon::parse($booking->scheduled_at);
                $dur = (int) ($booking->service->duration_minutes ?? 60);
                $occupiedRanges->push([
                    'start' => $start,
                    'end' => $start->copy()->addMinutes($dur)
                ]);
            });

        \App\Models\AvailabilityBlock::where('tenant_id', $tenantId)
            ->whereDate('start_time', '<=', $date)
            ->whereDate('end_time', '>=', $date)
            ->get()
            ->each(function ($block) use ($occupiedRanges) {
                $occupiedRanges->push([
                    'start' => Carbon::parse($block->start_time),
                    'end' => Carbon::parse($block->end_time)
                ]);
            });

        // 3. Generación de slots con Ventana Deslizable para cada rango
        $slots = [];
        $searchInterval = 30; // El intervalo de búsqueda

        foreach ($availability->ranges as $range) {
            if (!isset($range['start_time']) || !isset($range['end_time'])) {
                continue;
            }

            $workStart = Carbon::parse($date . ' ' . $range['start_time']);
            $workEnd = Carbon::parse($date . ' ' . $range['end_time']);
            
            // Margen para hoy
            if ($carbonDate->isToday()) {
                $now = Carbon::now()->addMinutes(15)->ceilMinute(15);
                if ($now->gt($workStart)) {
                    $workStart = $now;
                }
            }

            $current = $workStart->copy();
            
            while ($current->copy()->addMinutes($duration)->lte($workEnd)) {
                $windowStart = $current->copy();
                $windowEnd = $current->copy()->addMinutes($duration);

                // Verificar si la VENTANA COMPLETA está libre
                $isConflict = $occupiedRanges->contains(function ($occupied) use ($windowStart, $windowEnd) {
                    // Hay traslape si: (Ventana_Inicio < Rango_Fin) Y (Ventana_Fin > Rango_Inicio)
                    return $windowStart->lt($occupied['end']) && $windowEnd->gt($occupied['start']);
                });

                if (!$isConflict) {
                    $slots[] = $windowStart->format('H:i');
                }

                $current->addMinutes($searchInterval);
            }
        }

        // Retornar slots únicos y ordenados por si hay cruces de rangos
        $slots = array_unique($slots);
        sort($slots);
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
