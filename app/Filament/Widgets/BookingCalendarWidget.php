<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\Widget;
use App\Enums\BookingStatus;
use App\Services\WhatsAppNotificationService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Facades\Filament;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class BookingCalendarWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions {
        mountAction as baseMountAction;
    }
    use InteractsWithForms;

    protected string $view = 'filament.widgets.custom-calendar-widget';
    protected int | string | array $columnSpan = 'full';
    
    public ?Booking $activeBooking = null;

    public function fetchEvents(): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return [];
        }

        $bookings = Booking::query()
            ->where('tenant_id', $tenant->id)
            ->whereNotNull('scheduled_at')
            ->with(['customer', 'service'])
            ->get();

        return $bookings->map(
                fn (Booking $booking) => [
                    'id' => $booking->id,
                    'title' => ($booking->customer?->name ?? 'Sin Cliente') . ' - ' . ($booking->service?->name ?? 'Sin Servicio'),
                    'start' => $booking->scheduled_at->toIso8601String(),
                    'end' => $booking->scheduled_at->copy()->addHours(1)->toIso8601String(),
                    'backgroundColor' => $this->getEventColor($booking),
                    'borderColor' => $this->getEventColor($booking),
                ]
            )
            ->all();
    }

    public function getMinTime(): string
    {
        $tenant = Filament::getTenant();
        if (!$tenant) return '07:00:00';

        $firstBooking = Booking::query()
            ->where('tenant_id', $tenant->id)
            ->whereNotNull('scheduled_at')
            ->orderByRaw('CAST(scheduled_at AS TIME) ASC')
            ->first();

        if ($firstBooking) {
            $hour = $firstBooking->scheduled_at->subHour()->format('H');
            return "{$hour}:00:00";
        }

        return '07:00:00';
    }

    protected function getEventColor(Booking $booking): string
    {
        return match ($booking->status) {
            BookingStatus::Confirmed => '#10b981',
            BookingStatus::Pending => '#f59e0b',
            BookingStatus::Cancelled => '#ef4444',
            default => '#3b82f6',
        };
    }
    
    public function mountAction(string $name, array $arguments = [], array $context = []): mixed
    {
        if ($name === 'viewBooking' && isset($arguments['record'])) {
            $this->activeBooking = Booking::find($arguments['record']);
        }
        return $this->baseMountAction($name, $arguments, $context);
    }

    public function viewBookingAction(): Action
    {
        return Action::make('viewBooking')
            ->modalHeading('Detalles de la Reserva')
            ->form([
                TextInput::make('customer_name')
                    ->label('Cliente')
                    ->default(fn () => $this->activeBooking?->customer?->name)
                    ->disabled(),
                TextInput::make('service_name')
                    ->label('Servicio')
                    ->default(fn () => $this->activeBooking?->service?->name)
                    ->disabled(),
                DateTimePicker::make('scheduled_at')
                    ->label('Fecha Agendada')
                    ->default(fn () => $this->activeBooking?->scheduled_at)
                    ->disabled(),
            ])
            ->extraModalFooterActions([
                Action::make('Confirmar')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (): bool => $this->activeBooking?->status === BookingStatus::Pending)
                    ->action(function () {
                        if ($this->activeBooking) {
                            $this->activeBooking->update(['status' => BookingStatus::Confirmed]);
                            $url = app(WhatsAppNotificationService::class)->getConfirmationUrl($this->activeBooking);
                            $this->js("window.open('{$url}', '_blank')");
                            $this->js("window.location.reload()");
                        }
                    }),
                Action::make('Recordatorio')
                    ->color('warning')
                    ->icon('heroicon-o-bell-alert')
                    ->visible(fn (): bool => $this->activeBooking?->status === BookingStatus::Confirmed)
                    ->action(function () {
                        if ($this->activeBooking) {
                            $url = app(WhatsAppNotificationService::class)->getReminderUrl($this->activeBooking);
                            $this->js("window.open('{$url}', '_blank')");
                        }
                    }),
            ]);
    }
}
