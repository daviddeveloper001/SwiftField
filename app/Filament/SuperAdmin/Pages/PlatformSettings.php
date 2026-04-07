<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Models\TenantSetting;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;

class PlatformSettings extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Pagos de Plataforma';

    protected static ?string $title = 'Configuración de Pagos SwiftField';

    protected static string | UnitEnum | null $navigationGroup = 'Administración';

    protected static int | null $navigationSort = 100;

    protected string $view = 'filament.super-admin.pages.platform-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = TenantSetting::whereNull('tenant_id')
            ->where('key', 'platform_payment_methods')
            ->first();

        $this->form->fill($setting?->value ?? []);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Datos Bancarios Globales')
                    ->description('Estos datos se mostrarán a todos los tenants cuando deban realizar el pago de su suscripción.')
                    ->aside()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nequi_number')
                                    ->label('Número Nequi')
                                    ->prefixIcon('heroicon-m-phone')
                                    ->required()
                                    ->maxLength(10),
                                TextInput::make('bancolombia_account')
                                    ->label('Cuenta Bancolombia')
                                    ->prefixIcon('heroicon-m-banknotes')
                                    ->required()
                                    ->placeholder('Ahorros 000-000000-00'),
                                TextInput::make('account_holder')
                                    ->label('Titular de la Cuenta')
                                    ->prefixIcon('heroicon-m-user')
                                    ->columnSpanFull()
                                    ->required(),
                                Textarea::make('activation_message')
                                    ->label('Mensaje para el Cliente')
                                    ->helperText('Instrucciones finales que verá el tenant tras realizar el pago.')
                                    ->rows(4)
                                    ->columnSpanFull()
                                    ->required(),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            TenantSetting::updateOrCreate(
                ['tenant_id' => null, 'key' => 'platform_payment_methods'],
                ['value' => $data]
            );

            Notification::make()
                ->success()
                ->title('Configuración actualizada')
                ->body('Los datos bancarios globales han sido guardados correctamente.')
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error al guardar')
                ->body('Ocurrió un error inesperado: ' . $e->getMessage())
                ->send();
        }
    }
}
