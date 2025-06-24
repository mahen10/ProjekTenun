<?php

namespace App\Filament\Resources\TenunResource\Pages;

use App\Filament\Resources\TenunResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenuns extends ListRecords
{
    protected static string $resource = TenunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
