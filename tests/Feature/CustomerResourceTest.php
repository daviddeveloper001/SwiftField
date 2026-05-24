<?php

use App\Models\Customer;
use App\Models\Tenant;
use App\Models\User;
use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Customers\Pages\ListCustomers;
use App\Filament\Resources\Customers\Pages\CreateCustomer;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use function Pest\Livewire\livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    $this->user = User::factory()->create();
    $this->user->tenants()->attach($this->tenant);
    
    $this->actingAs($this->user);
});

it('can render the customer list page', function () {
    Livewire::test(ListCustomers::class, ['tenant' => $this->tenant])
        ->assertSuccessful();
});

it('can list customers only for the current tenant', function () {
    $customer1 = Customer::factory()->create(['tenant_id' => $this->tenant->id]);
    
    $otherTenant = Tenant::factory()->create();
    $customer2 = Customer::factory()->create(['tenant_id' => $otherTenant->id]);

    Livewire::test(ListCustomers::class, ['tenant' => $this->tenant])
        ->assertCanSeeTableRecords([$customer1])
        ->assertCanNotSeeTableRecords([$customer2]);
});

it('can create a customer', function () {
    Livewire::test(CreateCustomer::class, ['tenant' => $this->tenant])
        ->fillForm([
            'name' => 'John Doe',
            'phone' => '3001234567',
            'email' => 'john@example.com',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('customers', [
        'name' => 'John Doe',
        'phone' => '573001234567',
        'tenant_id' => $this->tenant->id,
    ]);
});

it('validates unique phone number per tenant', function () {
    Customer::factory()->create([
        'tenant_id' => $this->tenant->id,
        'phone' => '3001234567',
    ]);

    Livewire::test(CreateCustomer::class, ['tenant' => $this->tenant])
        ->fillForm([
            'name' => 'Jane Doe',
            'phone' => '3001234567',
        ])
        ->call('create')
        ->assertHasFormErrors(['phone' => 'unique']);
});

it('allows same phone number in different tenants', function () {
    $otherTenant = Tenant::factory()->create();
    Customer::factory()->create([
        'tenant_id' => $otherTenant->id,
        'phone' => '3001234567',
    ]);

    Livewire::test(CreateCustomer::class, ['tenant' => $this->tenant])
        ->fillForm([
            'name' => 'Jane Doe',
            'phone' => '3001234567',
        ])
        ->call('create')
        ->assertHasNoFormErrors();
    
    $this->assertDatabaseHas('customers', [
        'name' => 'Jane Doe',
        'phone' => '573001234567',
        'tenant_id' => $this->tenant->id,
    ]);
});
