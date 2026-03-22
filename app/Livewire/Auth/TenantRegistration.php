<?php

namespace App\Livewire\Auth;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class TenantRegistration extends Component
{
    public $business_name = '';
    public $slug = '';
    public $owner_name = '';
    public $email = '';
    public $password = '';

    public function updatedBusinessName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function updatedSlug()
    {
        $this->validateOnly('slug');
    }

    protected function rules()
    {
        return [
            'business_name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:' . Tenant::class . ',slug',
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:' . User::class . ',email',
            'password' => 'required|string|min:8',
        ];
    }

    public function register()
    {
        $this->validate();

        DB::transaction(function () {
            $tenant = Tenant::create([
                'uuid' => (string) Str::uuid(),
                'name' => $this->business_name,
                'slug' => $this->slug,
                'is_active' => true,
                'subscription_status' => \App\Enums\SubscriptionStatus::Trial,
                'trial_ends_at' => now()->addDays(7),
                'landing_config' => [
                    'html_template' => \App\Support\DefaultTenantLayout::get(),
                ],
            ]);

            $user = User::create([
                'name' => $this->owner_name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            $user->tenants()->attach($tenant->id);

            auth()->login($user);
        });

        return redirect('/admin');
    }

    public function render()
    {
        return view('livewire.auth.tenant-registration')->layout('layouts.app');
    }
}
