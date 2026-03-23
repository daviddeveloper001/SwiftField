<?php

namespace App\Policies;

use App\Models\User;
use Filament\Facades\Filament;

class ExceptionPolicy
{
    public function viewAny(User $user): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'superadmin';
    }

    public function view(User $user, $exception): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'superadmin';
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, $exception): bool
    {
        return false;
    }

    public function delete(User $user, $exception): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'superadmin';
    }
}
