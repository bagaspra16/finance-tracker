<?php

namespace App\Filament\Resources\TransaksiPemasukkanResource\Pages;

use App\Filament\Resources\TransaksiPemasukkanResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\TransaksiPemasukkan;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Model;

class EditTransaksiPemasukkan extends EditRecord
{
    protected static string $resource = TransaksiPemasukkanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        DB::beginTransaction();

        try {
            // Update TransaksiPemasukkan
            $record->fill([
                'tanggal_pemasukkan' => $data['tanggal_pemasukkan'],
                'jam_pemasukkan' => $data['jam_pemasukkan'],
                'balance_pemasukkan' => $data['balance_pemasukkan'],
                'id_kategori_pemasukkan' => $data['id_kategori_pemasukkan'],
                'id_jenis_penyimpanan' => $data['id_jenis_penyimpanan'],
                'catatan_pemasukkan' => $data['catatan_pemasukkan'],
                'updated_by' => Auth::user()->name,
                'updated_date' => now(),
            ]);
            $record->save();

            // Update Transaksi
            $transaksi = Transaksi::where('id_dokumen', $record->id)->first();
            if ($transaksi) {
                $transaksi->fill([
                    'id_jenis_penyimpanan' => $data['id_jenis_penyimpanan'],
                    'tanggal' => $data['tanggal_pemasukkan'],
                    'jam' => $data['jam_pemasukkan'],
                    'balance' => $data['balance_pemasukkan'],
                    'tipe' => 'IN',
                    'transaksi' => 'PEMASUKKAN',
                    'updated_by' => Auth::user()->name,
                    'updated_date' => now(),
                ]);
                $transaksi->save();
            }

            DB::commit();

            // Kembalikan model yang telah diperbarui
            return $record;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
