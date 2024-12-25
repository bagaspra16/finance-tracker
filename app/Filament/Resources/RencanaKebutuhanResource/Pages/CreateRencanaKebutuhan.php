<?php

namespace App\Filament\Resources\RencanaKebutuhanResource\Pages;

use App\Filament\Resources\RencanaKebutuhanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\RencanaKebutuhan;

class CreateRencanaKebutuhan extends CreateRecord
{
    protected static string $resource = RencanaKebutuhanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $lastKode = RencanaKebutuhan::select('kode')
            ->orderBy('kode', 'desc')
            ->first();

        $newKode = 'RK01';
        if ($lastKode) {
            $lastNumber = (int) substr($lastKode->kode, 2);
            $newKode = 'RK' . str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        }

        $data['kode'] = $newKode;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
