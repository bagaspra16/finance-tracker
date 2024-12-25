<?php

namespace App\Filament\Resources\TransaksiPengeluaranResource\Pages;

use App\Filament\Resources\TransaksiPengeluaranResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\TransaksiPengeluaran;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Model;

class EditTransaksiPengeluaran extends EditRecord
{
    protected static string $resource = TransaksiPengeluaranResource::class;

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
            // Update TransaksiPengeluaran
            $record->fill([
                'tanggal_pengeluaran' => $data['tanggal_pengeluaran'],
                'jam_pengeluaran' => $data['jam_pengeluaran'],
                'jam_pengeluaran' => $data['id_rencana_kebutuhan'],
                'balance_pengeluaran' => $data['balance_pengeluaran'],
                'id_kategori_pengeluaran' => $data['id_kategori_pengeluaran'],
                'id_jenis_penyimpanan' => $data['id_jenis_penyimpanan'],
                'catatan_pengeluaran' => $data['catatan_pengeluaran'],
                'updated_by' => Auth::user()->name,
                'updated_date' => now(),
            ]);
            $record->save();

            // Update Transaksi
            $transaksi = Transaksi::where('id_dokumen', $record->id)->first();
            if ($transaksi) {
                $transaksi->fill([
                    'id_jenis_penyimpanan' => $data['id_jenis_penyimpanan'],
                    'tanggal' => $data['tanggal_pengeluaran'],
                    'jam' => $data['jam_pengeluaran'],
                    'balance' => $data['balance_pengeluaran'],
                    'tipe' => 'OUT',
                    'transaksi' => 'PENGELUARAN',
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
