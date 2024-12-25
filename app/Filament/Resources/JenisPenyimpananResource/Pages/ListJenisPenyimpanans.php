<?php

namespace App\Filament\Resources\JenisPenyimpananResource\Pages;

use App\Filament\Resources\JenisPenyimpananResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJenisPenyimpanans extends ListRecords
{
    protected static string $resource = JenisPenyimpananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'List Jenis Penyimpanan'; 
    }
}
