<?php

namespace App\Filament\Resources\TransaksiPemasukkanResource\Pages;

use App\Filament\Resources\TransaksiPemasukkanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiPemasukkans extends ListRecords
{
    protected static string $resource = TransaksiPemasukkanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'List Transaksi Pemasukkan'; 
    }
}
