<?php

namespace App\Filament\Resources\JenisPenyimpananResource\Pages;

use App\Filament\Resources\JenisPenyimpananResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\JenisPenyimpanan;

class CreateJenisPenyimpanan extends CreateRecord
{
    protected static string $resource = JenisPenyimpananResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $lastKode = JenisPenyimpanan::select('kode')
            ->orderBy('kode', 'desc')
            ->first();

        $newKode = 'JP01'; 
        if ($lastKode) {
            $lastNumber = (int)substr($lastKode->kode, 3); 
            $newKode = 'JP' . str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        }

        $data['kode'] = $newKode;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
