<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenyimpananResource\Pages;
use App\Filament\Resources\PenyimpananResource\RelationManagers;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;

class PenyimpananResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationGroup = 'Manajemen Penyimpanan';

    public static function getNavigationLabel(): string
    {
        return 'Penyimpanan';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Jenis Penyimpanan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Total Balance')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state)),
                Tables\Columns\TextColumn::make('jumlah_transaksi')
                    ->label('Jumlah Transaksi')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('id_jenis_penyimpanan')
                    ->label('Jenis Penyimpanan')
                    ->relationship('jenisPenyimpanan', 'nama')
                    ->searchable(),
            ])
            ->query(fn () =>
                Transaksi::query()
                    ->join('mm_jenis_penyimpanan', 'mm_transaksi.id_jenis_penyimpanan', '=', 'mm_jenis_penyimpanan.id')
                    ->select([
                        'mm_transaksi.id_jenis_penyimpanan as id', 
                        'mm_jenis_penyimpanan.nama as nama',
                        DB::raw('
                            SUM(CASE WHEN mm_transaksi.tipe = \'IN\' THEN mm_transaksi.balance ELSE 0 END) -
                            SUM(CASE WHEN mm_transaksi.tipe = \'OUT\' THEN mm_transaksi.balance ELSE 0 END)
                            AS balance
                        '),
                        DB::raw('COUNT(mm_transaksi.id) as jumlah_transaksi'),
                    ])
                    ->where('mm_transaksi.deleted', false)
                    ->groupBy('mm_transaksi.id_jenis_penyimpanan', 'mm_jenis_penyimpanan.nama')
            )
            ->defaultSort('id_jenis_penyimpanan', 'asc');
    }
    


    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenyimpanans::route('/'),
        ];
    }
}
