<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiPemasukkanResource\Pages;
use App\Filament\Resources\TransaksiPemasukkanResource\RelationManagers;
use App\Models\TransaksiPemasukkan;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter; 
use Filament\Forms\Components\DatePicker; 
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaksiPemasukkanExport;

class TransaksiPemasukkanResource extends Resource
{
    protected static ?string $model = TransaksiPemasukkan::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-circle';

    protected static ?string $pluralLabel = 'Laporan Transaksi';

    public static function getNavigationLabel(): string
    {
        return 'Pemasukkan';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode')
                    ->label('Kode')
                    ->disabled()
                    ->placeholder('Generated By System'),

                Forms\Components\DatePicker::make('tanggal_pemasukkan')
                    ->label('Tanggal Pemasukkan')
                    ->default(now())
                    ->required(),

                Forms\Components\TextInput::make('jam_pemasukkan')
                    ->label('Jam Pemasukkan')
                    ->required()
                    ->default(now()->format('H:i')) 
                    ->placeholder('HH:MM')
                    ->hint('Gunakan format jam HH:MM, contoh: 23:59')
                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                        if (!$state) {
                            $component->state(now()->format('H:i'));
                        }
                    })
                    ->reactive(), 

                Forms\Components\TextInput::make('balance_pemasukkan')
                    ->label('Balance Pemasukkan')
                    ->numeric()
                    ->rules(['regex:/^\d*$/'])  
                    ->required(),
                    
                Forms\Components\Select::make('id_kategori_pemasukkan')
                    ->label('Kategori Pemasukkan')
                    ->relationship('kategoriPemasukkan', 'nama', function ($query) {
                        return $query->where('deleted', false);
                    })
                    ->required(),
                
                Forms\Components\Select::make('id_jenis_penyimpanan')
                    ->label('Jenis Penyimpanan')
                    ->relationship('jenisPenyimpanan', 'nama', function ($query) {
                        return $query->where('deleted', false);
                    })
                    ->required(),
                

                Forms\Components\Textarea::make('catatan_pemasukkan')
                    ->label('Catatan'),

                Forms\Components\Hidden::make('id')
                    ->default(fn () => Str::uuid()->toString())
                    ->required(),

                Forms\Components\Hidden::make('deleted')
                    ->default(false),

                Forms\Components\Hidden::make('created_by')
                    ->default(fn () => Auth::user()->name),

                Forms\Components\Hidden::make('created_date')
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode')
                    ->label('Kode')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_pemasukkan')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_pemasukkan')
                    ->label('Jam')
                    ->time('H:i'),                
                Tables\Columns\TextColumn::make('balance_pemasukkan')
                    ->label('Balance')
                    ->formatStateUsing(fn ($state) => number_format($state))
                    ->sortable(), 
                Tables\Columns\TextColumn::make('kategoriPemasukkan.nama')
                    ->label('Kategori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenisPenyimpanan.nama')
                    ->label('Penyimpanan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_by')
                    ->label('Dibuat Oleh')
                    ->searchable(),
            ])
            ->query(TransaksiPemasukkan::where('deleted', false))
            ->filters([
                Filter::make('Tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')->label('Tanggal Mulai'),
                        Forms\Components\DatePicker::make('end_date')->label('Tanggal Akhir'),
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['start_date']) {
                            $query->whereDate('tanggal_pemasukkan', '>=', $data['start_date']);
                        }

                        if ($data['end_date']) {
                            $query->whereDate('tanggal_pemasukkan', '<=', $data['end_date']);
                        }
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('Export to Excel')
                    ->action(function ($action) {
                        // Get the table filters
                        $table = $action->getTable();
                        $filters = $table->getFilters();
                        
                        // Get filter values
                        $filterData = [];
                        foreach ($filters as $filter) {
                            $filterData[$filter->getName()] = $filter->getState();
                        }
                        
                        return Excel::download(
                            new TransaksiPemasukkanExport($filterData),
                            'Laporan Pemasukkan - ' . now()->format('d-m-Y') . '.xlsx'
                        );
                    })
                    ->color('success')
                    ->icon('heroicon-o-document')
                    ->requiresConfirmation() 
                    ->modalHeading('Export Data Pemasukkan') 
                    ->modalDescription('Apakah anda yakin ingin mengexport data?') 
                    ->modalSubmitActionLabel('Ya, Export') 
                    ->modalCancelActionLabel('Batal'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            // Update Transaksi
                            Transaksi::where('id_dokumen', $record->id)->update([
                                'deleted' => true,
                                'updated_by' => Auth::user()->name,
                                'updated_date' => now(),
                            ]);

                            // Update Record
                            $record->update([
                                'deleted' => true,
                                'updated_by' => Auth::user()->name,
                                'updated_date' => now(),
                            ]);
                        });
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->action(function ($records) {
                        DB::transaction(function () use ($records) {
                            foreach ($records as $record) {
                                // Update Transaksi
                                Transaksi::where('id_dokumen', $record->id)->update([
                                    'deleted' => true,
                                    'updated_by' => Auth::user()->name,
                                    'updated_date' => now(),
                                ]);

                                // Update Record
                                $record->update([
                                    'deleted' => true,
                                    'updated_by' => Auth::user()->name,
                                    'updated_date' => now(),
                                ]);
                            }
                        });
                    }),
            ]);
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
            'index' => Pages\ListTransaksiPemasukkans::route('/'),
            'create' => Pages\CreateTransaksiPemasukkan::route('/create'),
            'edit' => Pages\EditTransaksiPemasukkan::route('/{record}/edit'),
        ];
    }

}
