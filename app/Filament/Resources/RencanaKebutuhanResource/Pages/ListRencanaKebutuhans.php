<?php

namespace App\Filament\Resources\RencanaKebutuhanResource\Pages;

use App\Filament\Resources\RencanaKebutuhanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRencanaKebutuhans extends ListRecords
{
    protected static string $resource = RencanaKebutuhanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'List Rencana Kebutuhan'; 
    }
}
