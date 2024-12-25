<?php

namespace App\Filament\Resources\KategoriPemasukkanResource\Pages;

use App\Filament\Resources\KategoriPemasukkanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditKategoriPemasukkan extends EditRecord
{
    protected static string $resource = KategoriPemasukkanResource::class;

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
