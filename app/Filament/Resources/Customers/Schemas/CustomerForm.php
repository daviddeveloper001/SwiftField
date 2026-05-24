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
                    ->label(__('branding.forms.customer.name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label(__('branding.forms.customer.phone'))
                    ->tel()
                    ->prefix('+57')
                    ->mask('999 999 9999')
                    ->stripCharacters(' ')
                    ->required()
                    ->rules([
                        fn (\Filament\Schemas\Components\Utilities\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                            $clean = preg_replace('/[^0-9]/', '', (string)$value);
                            if (!empty($clean) && !str_starts_with($clean, '57')) {
                                $clean = '57' . $clean;
                            }
                            
                            $tenantId = auth()->user()->tenants()->first()?->id;
                            
                            $query = \App\Models\Customer::where('tenant_id', $tenantId)
                                ->where('phone', $clean);
                            
                            // Ignorar el registro actual si estamos editando
                            $recordId = $get('id');
                            if ($recordId) {
                                $query->where('id', '!=', $recordId);
                            }

                            if ($query->exists()) {
                                $fail(__('validation.unique', ['attribute' => __('branding.forms.customer.phone')]));
                            }
                        }
                    ]),

                TextInput::make('email')
                    ->label(__('branding.forms.customer.email'))
                    ->email()
                    ->maxLength(255),
            ]);
    }
}
