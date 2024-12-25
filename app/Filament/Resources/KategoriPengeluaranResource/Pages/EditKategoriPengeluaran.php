<?php

namespace App\Filament\Resources\KategoriPengeluaranResource\Pages;

use App\Filament\Resources\KategoriPengeluaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditKategoriPengeluaran extends EditRecord
{
    protected static string $resource = KategoriPengeluaranResource::class;

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
