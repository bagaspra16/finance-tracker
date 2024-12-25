<?php

namespace App\Filament\Resources\TransaksiPengeluaranResource\Pages;

use App\Filament\Resources\TransaksiPengeluaranResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\TransaksiPengeluaran;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Model;
use Filament\Pages\Actions\ButtonAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateTransaksiPengeluaran extends CreateRecord
{
    protected static string $resource = TransaksiPengeluaranResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $currentMonth = now()->format('m'); // Ambil bulan saat ini
        $currentYear = now()->format('Y'); // Ambil tahun saat ini
        $romanMonth = $this->convertToRoman($currentMonth); // Konversi bulan ke format Romawi

        // Cari kode terakhir untuk bulan dan tahun saat ini
        $lastKode = TransaksiPengeluaran::whereYear('tanggal_pengeluaran', $currentYear)
            ->whereMonth('tanggal_pengeluaran', $currentMonth)
            ->select('kode')
            ->orderBy('kode', 'desc')
            ->first();

        // Hitung running number
        $runningNumber = 1;
        if ($lastKode) {
            $lastNumber = (int) explode('/', $lastKode->kode)[1];
            $runningNumber = $lastNumber + 1;
        }

        // Format kode baru
        $newKode = sprintf('PN/%02d/%s/%s', $runningNumber, $romanMonth, $currentYear);

        $data['kode'] = $newKode;
        return $data;
    }
    /**
     * Konversi angka menjadi angka Romawi.
     *
     * @param int|string $number
     * @return string
     */
    private function convertToRoman($number): string
    {
        $map = [
            'I', 'II', 'III', 'IV', 'V', 'VI',
            'VII', 'VIII', 'IX', 'X', 'XI', 'XII'
        ];
        return $map[(int) $number - 1] ?? '';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function handleRecordCreation(array $data): Model
    {
        DB::beginTransaction();

        try {

            // Create Transaksi
            $transaksi = new Transaksi();
            $transaksi->fill([
                'id' => Str::uuid(),
                'id_jenis_penyimpanan' => $data['id_jenis_penyimpanan'],
                'id_dokumen' => $data['id'],
                'kode' => $data['kode'],
                'tanggal' => $data['tanggal_pengeluaran'],
                'jam' => $data['jam_pengeluaran'],
                'balance' => $data['balance_pengeluaran'],
                'tipe' => 'OUT',
                'transaksi' => 'PENGELUARAN',
                'deleted' => false,
                'created_by' => Auth::user()->name,
                'created_date' => now(),
            ]);
            $transaksi->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return parent::handleRecordCreation($data);
    }

}
