<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Models\Service;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Registro de Negocio - SwiftField')]
class TenantRegistration extends Component
{
    public string $ownerName = '';
    public string $email = '';
    public string $password = '';
    public string $businessName = '';
    public string $whatsapp = '';
    public string $slug = '';

    protected array $rules = [
        'ownerName' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        'businessName' => 'required|string|max:255',
        'whatsapp' => 'required|string|max:20',
        'slug' => 'required|string|unique:tenants,slug',
    ];

    public function updatedBusinessName(string $value): void
    {
        $this->slug = Str::slug($value);
    }

    public function register(): void
    {
        $this->validate();

        DB::transaction(function () {
            // 1. Create Tenant
            $tenant = Tenant::create([
                'uuid' => (string) Str::uuid(),
                'name' => $this->businessName,
                'slug' => $this->slug,
                'whatsapp_config' => [
                    'number' => $this->whatsapp,
                ],
                'is_active' => true,
                'subscription_status' => \App\Enums\SubscriptionStatus::Trial,
                'trial_ends_at' => now()->addDays(7),
            ]);

            // 2. Create User
            $user = User::create([
                'name' => $this->ownerName,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            // 3. Link User to Tenant
            $user->tenants()->attach($tenant->id);

            // 4. Create Courtesy Service
            Service::create([
                'tenant_id' => $tenant->id,
                'name' => 'Mi primer servicio',
                'slug' => 'mi-primer-servicio',
                'price' => 0,
                'is_active' => true,
                'description' => 'Este es un servicio de ejemplo creado automáticamente para ayudarte a comenzar.',
                'field_definitions' => [],
            ]);

            Auth::login($user);
        });

        $this->redirect(route('filament.admin.pages.dashboard', ['tenant' => $this->slug]));
    }

    public function render()
    {
        return view('livewire.auth.tenant-registration')
            ->layout('layouts.app');
    }
}
