<?php

namespace App\Filament\Resources\TenunResource\Pages;

use App\Filament\Resources\TenunResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTenun extends EditRecord
{
    protected static string $resource = TenunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
