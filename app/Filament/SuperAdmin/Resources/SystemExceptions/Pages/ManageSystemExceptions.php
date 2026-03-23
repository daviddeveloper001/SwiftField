<?php

namespace App\Filament\SuperAdmin\Resources\SystemExceptions\Pages;

use App\Filament\SuperAdmin\Resources\SystemExceptions\SystemExceptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSystemExceptions extends ManageRecords
{
    protected static string $resource = SystemExceptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
