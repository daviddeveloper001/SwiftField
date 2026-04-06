<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Storage;
use BackedEnum;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Grid;

class ManageBranding extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationLabel = 'Personalización';

    protected static ?string $title = 'Personalización del Negocio';

    protected string $view = 'filament.pages.manage-branding';

    public ?array $data = [];

    public function mount(): void
    {
        $tenant = auth()->user()->tenants()->first();

        if ($tenant) {
            $branding = $tenant->getSetting('branding_config', []);
            $whatsapp = $tenant->getSetting('whatsapp_config', []);
            
            $this->form->fill([
                'logo_url' => $branding['logo_url'] ?? null,
                'primary_color' => $branding['primary_color'] ?? '#3b82f6',
                'secondary_color' => $branding['secondary_color'] ?? '#1e40af',
                'phone' => $whatsapp['phone'] ?? '',
            ]);
        }
    }

    protected function getHeaderActions(): array
    {
        $tenant = auth()->user()->tenants()->first();
        
        return [
            Action::make('preview')
                ->label('Previsualizar mi Landing')
                ->color('gray')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn () => $tenant ? route('tenant.landing', ['slug' => $tenant->slug]) : '#')
                ->openUrlInNewTab(),
        ];
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Identidad Visual')
                    ->description('Configura los colores y el logotipo corporativo.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                FileUpload::make('logo_url')
                                    ->label('Logotipo')
                                    ->image()
                                    ->directory('tenant-logos')
                                    ->imageEditor()
                                    ->avatar()
                                    ->columnSpan(1),
                                
                                Grid::make(1)
                                    ->schema([
                                        ColorPicker::make('primary_color')
                                            ->label('Color Primario')
                                            ->required(),
                                        ColorPicker::make('secondary_color')
                                            ->label('Color Secundario')
                                            ->required(),
                                    ])
                                    ->columnSpan(2),
                            ]),
                    ]),

                Section::make('Comunicación')
                    ->description('Datos de contacto para integraciones (WhatsApp).')
                    ->schema([
                        TextInput::make('phone')
                            ->label('Número de WhatsApp')
                            ->prefix('+57')
                            ->mask('999 999 9999')
                            ->stripCharacters(' ')
                            ->afterStateHydrated(function (TextInput $component, $state) {
                                if ($state && str_starts_with($state, '57')) {
                                    $component->state(substr($state, 2));
                                }
                            })
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $tenant = auth()->user()->tenants()->first();
        $state = $this->form->getState();

        if (!$tenant) return;

        // 1. Guardar Branding Centralizado
        $tenant->setSetting('branding_config', [
            'primary_color' => $state['primary_color'],
            'secondary_color' => $state['secondary_color'],
            'logo_url' => $state['logo_url'],
        ]);

        // 2. Guardar WhatsApp Config
        $cleanPhone = preg_replace('/[^0-9]/', '', $state['phone']);
        $tenant->setSetting('whatsapp_config', [
            'phone' => '57' . $cleanPhone,
        ]);

        Notification::make()
            ->title('Identidad actualizada')
            ->body('Los cambios se han aplicado a todo el sistema.')
            ->success()
            ->send();
    }
}
