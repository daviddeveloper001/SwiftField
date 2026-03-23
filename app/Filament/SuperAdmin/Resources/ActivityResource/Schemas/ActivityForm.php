<?php

namespace App\Filament\SuperAdmin\Resources\ActivityResource\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Str;

class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles del Negocio')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del Negocio')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                    ])->columns(2),

                Section::make('Dueño del Negocio')
                    ->description('Crea el usuario administrador de este negocio.')
                    ->schema([
                        TextInput::make('owner_name')
                            ->label('Nombre Completo')
                            ->required()
                            ->dehydrated(false),
                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->unique(\App\Models\User::class, 'email', ignoreRecord: true)
                            ->dehydrated(false),
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->visible(fn (string $operation): bool => $operation === 'create'),
            ]);
    }
}
