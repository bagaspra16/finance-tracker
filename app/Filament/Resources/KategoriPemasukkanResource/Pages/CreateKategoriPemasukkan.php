<?php

namespace App\Filament\Resources\KategoriPemasukkanResource\Pages;

use App\Filament\Resources\KategoriPemasukkanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\KategoriPemasukkan;

class CreateKategoriPemasukkan extends CreateRecord
{
    protected static string $resource = KategoriPemasukkanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $lastKode = KategoriPemasukkan::select('kode')
            ->orderBy('kode', 'desc')
            ->first();

        $newKode = 'KPM01'; 
        if ($lastKode) {
            $lastNumber = (int)substr($lastKode->kode, 3); 
            $newKode = 'KPM' . str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        }

        $data['kode'] = $newKode;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
