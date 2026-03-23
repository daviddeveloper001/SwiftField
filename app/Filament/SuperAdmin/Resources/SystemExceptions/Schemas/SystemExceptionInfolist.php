<?php

namespace App\Filament\SuperAdmin\Resources\SystemExceptions\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

class SystemExceptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
             ->components([
                Section::make('Detalles del Error')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'open' => 'danger',
                                'fixed' => 'success',
                                default => 'gray',
                            }),
                        TextEntry::make('created_at')
                            ->label('Fecha')
                            ->dateTime(),
                        TextEntry::make('user.name')
                            ->label('Usuario Afectado')
                            ->placeholder('Ninguno (Invitado)'),
                        TextEntry::make('tenant.name')
                            ->label('Tenant Afectado')
                            ->placeholder('Global'),
                        TextEntry::make('url')
                            ->label('URL')
                            ->columnSpanFull(),
                        TextEntry::make('message')
                            ->label('Mensaje de Error')
                            ->columnSpanFull(),
                        TextEntry::make('file')
                            ->label('Archivo')
                            ->columnSpanFull(),
                    ])->columns(2),
                Section::make('Stack Trace')
                    ->schema([
                        TextEntry::make('stack_trace')
                            ->hiddenLabel()
                            ->formatStateUsing(fn (string $state): HtmlString => new HtmlString("<pre style='white-space: pre-wrap; word-wrap: break-word;' class='text-xs p-4 bg-gray-950 text-gray-200 rounded-xl overflow-x-auto'>".htmlentities($state)."</pre>"))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
