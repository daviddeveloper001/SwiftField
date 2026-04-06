<?php

namespace App\Filament\Pages;

use App\Models\Tenant;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
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
            $config = $tenant->getSetting('landing_config', []);
            
            $this->form->fill([
                'sections' => $config['sections'] ?? [],
            ]);
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
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

        $tenant->setSetting('landing_config', [
            'theme_id' => 'default',
            'sections' => $formattedSections,
        ]);

        Notification::make()
            ->title('Estructura actualizada')
            ->body('Los cambios en las secciones se han guardado.')
            ->success()
            ->send();
    }
}
