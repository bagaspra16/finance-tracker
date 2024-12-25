<?php

namespace App\Filament\Resources\KategoriPengeluaranResource\Pages;

use App\Filament\Resources\KategoriPengeluaranResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\KategoriPengeluaran;

class CreateKategoriPengeluaran extends CreateRecord
{
    protected static string $resource = KategoriPengeluaranResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $lastKode = KategoriPengeluaran::select('kode')
            ->orderBy('kode', 'desc')
            ->first();

        $newKode = 'KPN01'; 
        if ($lastKode) {
            $lastNumber = (int)substr($lastKode->kode, 3); 
            $newKode = 'KPN' . str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        }

        $data['kode'] = $newKode;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
