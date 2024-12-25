<?php

namespace App\Filament\Resources\PenyimpananResource\Pages;

use App\Filament\Resources\PenyimpananResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenyimpanan extends EditRecord
{
    protected static string $resource = PenyimpananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
