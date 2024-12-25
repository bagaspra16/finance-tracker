<?php

namespace App\Filament\Resources\JenisPenyimpananResource\Pages;

use App\Filament\Resources\JenisPenyimpananResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditJenisPenyimpanan extends EditRecord
{
    protected static string $resource = JenisPenyimpananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Auth::user()->name;
        $data['updated_date'] = now();

        return $data;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
