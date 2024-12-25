<?php

namespace App\Filament\Resources\TransaksiPemasukkanResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\TransaksiPemasukkanResource;

class ViewTransaksiPemasukkan extends ViewRecord
{
    protected static string $resource = TransaksiPemasukkanResource::class;

    /**
     * Configure the page to display only detailed view without edit options.
     */
    protected function getActions(): array
    {
        // No actions to disable editing or deleting
        return [];
    }
}
