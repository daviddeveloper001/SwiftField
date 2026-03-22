<?php

namespace App\Filament\SuperAdmin\Resources\TenantResource\Pages;

use App\Filament\SuperAdmin\Resources\TenantResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $data['uuid'] = $data['uuid'] ?? (string) \Illuminate\Support\Str::uuid();
            $data['subscription_status'] = $data['subscription_status'] ?? \App\Enums\SubscriptionStatus::Trial;
            $data['trial_ends_at'] = $data['trial_ends_at'] ?? now()->addDays(7);
            
            $data['landing_config'] = $data['landing_config'] ?? [
                'html_template' => \App\Support\DefaultTenantLayout::get(),
            ];
            $tenant = static::getModel()::create($data);

            if (!empty($this->data['owner_name']) && !empty($this->data['email']) && !empty($this->data['password'])) {
                $user = User::create([
                    'name' => $this->data['owner_name'],
                    'email' => $this->data['email'],
                    'password' => Hash::make($this->data['password']),
                ]);

                $user->tenants()->attach($tenant->id);
            }

            return $tenant;
        });
    }
}
