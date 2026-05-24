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
use Cheesegrits\FilamentGoogleMaps\Fields\Map;

class ManageBranding extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-swatch';

    public static function getNavigationGroup(): ?string
    {
        return __('branding.navigation_groups.online_presence');
    }

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('branding.navigation_label');
    }

    public function getTitle(): string
    {
        return __('branding.title');
    }

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
                'address' => $tenant->address ?? '',
                'location' => [
                    'lat' => $tenant->latitude,
                    'lng' => $tenant->longitude,
                ],
                'latitude' => $tenant->latitude ?? '',
                'longitude' => $tenant->longitude ?? '',
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
                Section::make(__('branding.sections.visual_identity.title'))
                    ->description(__('branding.sections.visual_identity.description'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                FileUpload::make('logo_url')
                                    ->label(__('branding.sections.visual_identity.logo'))
                                    ->image()
                                    ->directory('tenant-logos')
                                    ->imageEditor()
                                    ->avatar()
                                    ->columnSpan(1),
                                
                                Grid::make(1)
                                    ->schema([
                                        ColorPicker::make('primary_color')
                                            ->label(__('branding.sections.visual_identity.primary_color'))
                                            ->required(),
                                        ColorPicker::make('secondary_color')
                                            ->label(__('branding.sections.visual_identity.secondary_color'))
                                            ->required(),
                                    ])
                                    ->columnSpan(2),
                            ]),
                    ]),

                Section::make(__('branding.sections.communication.title'))
                    ->description(__('branding.sections.communication.description'))
                    ->schema([
                        TextInput::make('phone')
                            ->label(__('branding.sections.communication.phone'))
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

                Section::make(__('branding.sections.location.title'))
                    ->description(__('branding.sections.location.description'))
                    ->schema([
                        TextInput::make('address')
                            ->label(__('branding.sections.location.address'))
                            ->placeholder(__('branding.sections.location.address_placeholder'))
                            ->required(),
                        
                        Map::make('location')
                            ->label(__('branding.sections.location.title'))
                            ->autocomplete('address')
                            ->reverseGeocode([
                                'address' => '%n %s, %L',
                            ])
                            ->defaultLocation([4.6097, -74.0817])
                            ->columnSpanFull(),

                        TextInput::make('latitude')
                            ->label(__('branding.sections.location.latitude'))
                            ->hidden()
                            ->readOnly(),

                        TextInput::make('longitude')
                            ->label(__('branding.sections.location.longitude'))
                            ->hidden()
                            ->readOnly(),
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

        // 3. Guardar Ubicación (Columnas en tabla tenants)
        $tenant->update([
            'address' => $state['address'],
            'latitude' => $state['location']['lat'] ?? $state['latitude'] ?? null,
            'longitude' => $state['location']['lng'] ?? $state['longitude'] ?? null,
        ]);

        Notification::make()
            ->title(__('branding.notifications.updated.title'))
            ->body(__('branding.notifications.updated.body'))
            ->success()
            ->send();
    }
}

