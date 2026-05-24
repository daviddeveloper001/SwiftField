<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('branding.sections.visual_identity.name') ?? 'Nombre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label(__('branding.sections.communication.phone') ?? 'Teléfono')
                    ->tel()
                    ->prefix('+57')
                    ->mask('999 999 9999')
                    ->stripCharacters(' ')
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: function (Unique $rule) {
                            return $rule->where('tenant_id', auth()->user()->tenants()->first()?->id);
                        }
                    ),

                TextInput::make('email')
                    ->label(__('Email'))
                    ->email()
                    ->maxLength(255),
            ]);
    }
}
