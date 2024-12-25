<?php

namespace App\Filament\Resources\PenyimpananResource\Pages;

use App\Filament\Resources\PenyimpananResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenyimpanans extends ListRecords
{
    protected static string $resource = PenyimpananResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Kosongkan array untuk menghapus tombol "Create"
    }

    public function getTitle(): string
    {
        return 'List Total Penyimpanan'; 
    }
}
