<?php

namespace App\Filament\Resources\KategoriPemasukkanResource\Pages;

use App\Filament\Resources\KategoriPemasukkanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriPemasukkans extends ListRecords
{
    protected static string $resource = KategoriPemasukkanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'List Kategori Pemasukkan'; 
    }
}
