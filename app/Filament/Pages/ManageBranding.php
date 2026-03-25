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

class ManageBranding extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?string $navigationLabel = 'Personalización';

    protected static ?string $title = 'Personalización del Negocio';

    protected string $view = 'filament.pages.manage-branding';

    public ?array $data = [];

    public function mount(): void
    {
        $tenant = auth()->user()->tenants()->first();

        if ($tenant) {
            $this->form->fill([
                'primary_color' => $tenant->branding_config['primary_color'] ?? '#fbbf24',
                'logo_url' => $tenant->branding_config['logo_url'] ?? null,
                'phone' => $tenant->whatsapp_config['phone'] ?? $tenant->whatsapp_config['number'] ?? '',
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
                    ->description('Configura los colores y el logotipo que verán tus clientes.')
                    ->schema([
                        ColorPicker::make('primary_color')
                            ->label('Color Principal')
                            ->required(),
                        FileUpload::make('logo_url')
                            ->label('Logo del Negocio')
                            ->directory('tenant_logos')
                            ->image()
                            ->maxSize(1024)
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('200')
                            ->imageResizeTargetHeight('200'),
                    ])->columns(2),

                Section::make('Comunicación')
                    ->description('Datos de contacto para el botón de WhatsApp.')
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
                            ->dehydrated(true)
                            ->rules(['regex:/^3[0-9]{9}$/'])
                            ->validationMessages([
                                'regex' => 'Por favor, ingresa un número celular válido de 10 dígitos (ej: 310 123 4567)',
                            ])
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $tenant = auth()->user()->tenants()->first();
        
        // Remove spaces before validation
        if (isset($this->data['phone'])) {
            $this->data['phone'] = str_replace(' ', '', $this->data['phone']);
        }

        $state = $this->form->getState();

        if (!$tenant) {
            return;
        }

        // Cleanup old logo if a new one is uploaded or deleted
        $oldLogo = $tenant->branding_config['logo_url'] ?? null;
        if ($oldLogo && $oldLogo !== $state['logo_url']) {
            Storage::disk('public')->delete($oldLogo);
        }

        // Prepend 57 before saving to DB
        $cleanPhone = preg_replace('/[^0-9]/', '', $state['phone']);
        $finalPhone = '57' . $cleanPhone;

        $tenant->update([
            'branding_config' => [
                'primary_color' => $state['primary_color'],
                'logo_url' => $state['logo_url'],
            ],
            'whatsapp_config' => [
                'phone' => $finalPhone,
            ],
        ]);

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->send();
    }
}
