<?php

namespace App\Filament\Pages;

use App\Models\Tenant;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use BackedEnum;

use Filament\Forms\Components\ToggleButtons;

class ManageLanding extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?string $navigationLabel = 'Constructor de Landing';

    protected static ?string $title = 'Personalizar Página de Inicio';

    protected string $view = 'filament.pages.manage-landing';

    public ?array $data = [];

    public function mount(): void
    {
        $tenant = auth()->user()->tenants()->first();

        if ($tenant) {
            // Leemos el objeto completo de la tabla tenant_settings
            $config = $tenant->getSetting('landing_config', []);
            
            // Llenamos el formulario asegurando que las llaves existan
            $this->form->fill([
                'primary_color' => $config['primary_color'] ?? '#3b82f6',
                'secondary_color' => $config['secondary_color'] ?? '#1e40af',
                'sections' => $config['sections'] ?? [],
            ]);
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Identidad Visual')
                    ->description('Define los colores base que se aplicarán a toda tu landing page.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                ColorPicker::make('primary_color')
                                    ->label('Color Primario')
                                    ->required(),
                                ColorPicker::make('secondary_color')
                                    ->label('Color Secundario')
                                    ->required(),
                            ]),
                    ]),

                Section::make('Estructura de la Página')
                    ->description('Añade, elimina y reordena las secciones de tu página de aterrizaje.')
                    ->schema([
                        Repeater::make('sections')
                            ->label('Secciones Activas')
                            ->addActionLabel('Añadir Nueva Sección')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => match($state['type'] ?? null) {
                                'hero' => "Cabecera (Hero) - " . ($state['content']['title'] ?? 'Sin título'),
                                'services' => "Cuadrícula de Servicios",
                                'contact' => "Formulario de Contacto",
                                default => "Nueva Sección",
                            })
                            ->schema([
                                ToggleButtons::make('type')
                                    ->label('Tipo de Sección')
                                    ->options([
                                        'hero' => 'Cabecera Principal (Hero)',
                                        'services' => 'Catálogo de Servicios',
                                        'contact' => 'Información de Contacto',
                                    ])
                                    ->icons([
                                        'hero' => 'heroicon-o-presentation-chart-line',
                                        'services' => 'heroicon-o-squares-2x2',
                                        'contact' => 'heroicon-o-envelope',
                                    ])
                                    ->inline()
                                    ->required()
                                    ->reactive(),

                                Grid::make(1)
                                    ->schema([
                                        TextInput::make('content.title')
                                            ->label('Título de la Sección')
                                            ->placeholder('Ej: Bienvenidos a nuestro centro')
                                            ->required()
                                            ->visible(fn ($get) => in_array($get('type'), ['hero', 'contact'])),
                                        
                                        TextInput::make('content.subtitle')
                                            ->label('Subtítulo o Descripción')
                                            ->placeholder('Ej: Expertos en servicios de campo')
                                            ->visible(fn ($get) => $get('type') === 'hero'),
                                    ])
                                    ->visible(fn ($get) => in_array($get('type'), ['hero', 'contact'])),
                            ])
                            ->defaultItems(1),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $tenant = auth()->user()->tenants()->first();
        $state = $this->form->getState();

        if (!$tenant) return;

        // Limpiamos los índices del repeater antes de guardar (array_values)
        // para asegurar un JSON limpio en la base de datos
        $formattedSections = collect($state['sections'] ?? [])
            ->values()
            ->map(function ($item, $index) {
                return [
                    'type' => $item['type'],
                    'order' => $index + 1,
                    'content' => $item['content'] ?? [],
                ];
            })
            ->toArray();

        // Persistencia ATÓMICA en tenant_settings. landing_config es la única verdad.
        $tenant->setSetting('landing_config', [
            'theme_id' => 'default',
            'primary_color' => $state['primary_color'],
            'secondary_color' => $state['secondary_color'],
            'sections' => $formattedSections,
        ]);

        Notification::make()
            ->title('Diseño actualizado en Tenant Settings')
            ->body('Los cambios se han guardado exclusivamente en la nueva estructura de configuraciones.')
            ->success()
            ->send();
    }
}
