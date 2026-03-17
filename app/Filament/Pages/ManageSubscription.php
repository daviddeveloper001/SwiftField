<?php

namespace App\Filament\Pages;

use App\Models\Tenant;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Pages\Page;
use Filament\Forms\Components\Placeholder;
use App\Enums\SubscriptionStatus;
use BackedEnum;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;

class ManageSubscription extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $title = 'Mi Suscripción';
    protected static ?string $navigationLabel = 'Facturación';
    protected string $view = 'filament.pages.manage-subscription';
    
    public ?array $data = [];

    public function mount(): void
    {
        $tenant = Filament::getTenant();
        $this->form->fill([
            'payment_proof' => $tenant->payment_proof,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Estado del Plan')
                    ->description('Información sobre tu periodo de prueba o suscripción activa.')
                    ->schema([
                        Placeholder::make('status')
                            ->label('Estado Actual')
                            ->content(fn () => Filament::getTenant()->subscription_status->getLabel()),
                        Placeholder::make('expiration')
                            ->label('Fecha de Vencimiento')
                            ->content(function () {
                                $tenant = Filament::getTenant();
                                $date = $tenant->subscription_ends_at ?? $tenant->trial_ends_at;
                                return $date ? $date->format('d M Y') . ' (' . $date->diffForHumans() . ')' : 'No definida';
                            }),
                    ])
                    ->columns(2),

                Section::make('Instrucciones de Pago')
                    ->description('Realiza tu pago para activar o renovar tu suscripción.')
                    ->schema([
                        Placeholder::make('payment_info')
                            ->label('Datos de Transferencia')
                            ->content(view('components.payment-instructions')),
                    ]),

                Section::make('Carga de Soporte')
                    ->description('Sube una captura de tu comprobante de pago.')
                    ->schema([
                        FileUpload::make('payment_proof')
                            ->label('Comprobante')
                            ->image()
                            ->directory('proofs')
                            ->required()
                            ->afterStateUpdated(function ($state) {
                                if ($state) {
                                    $tenant = Filament::getTenant();
                                    $tenant->update([
                                        'subscription_status' => SubscriptionStatus::PendingPayment,
                                        'payment_proof' => $state,
                                    ]);
                                }
                            }),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        // The update is handled by afterStateUpdated in the FileUpload
        \Filament\Notifications\Notification::make()
            ->title('Soporte Enviado')
            ->body('Tu comprobante ha sido subido. El estado ahora es Pago Pendiente.')
            ->success()
            ->send();
    }
}
