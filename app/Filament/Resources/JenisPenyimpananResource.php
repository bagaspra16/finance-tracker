<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JenisPenyimpananResource\Pages;
use App\Filament\Resources\JenisPenyimpananResource\RelationManagers;
use App\Models\JenisPenyimpanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class JenisPenyimpananResource extends Resource
{
    protected static ?string $model = JenisPenyimpanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Manajemen Penyimpanan';

    public static function getNavigationLabel(): string
    {
        return 'Jenis Penyimpanan';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode')
                    ->label('Kode')
                    ->disabled(fn ($record) => $record !== null)
                    ->placeholder('Generated By System'),

                Forms\Components\TextInput::make('nama')
                    ->label('Nama')
                    ->required(),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan'),

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
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_by')
                    ->label('Dibuat Oleh'),
            ])

            ->query(JenisPenyimpanan::where('deleted', false))

            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function ($record) {
                        $record->update([
                            'deleted' => true,
                            'updated_by' => Auth::user()->name,
                            'updated_date' => now(),
                        ]);
                    })
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->update([
                                'deleted' => true,
                                'updated_by' => Auth::user()->name,
                                'updated_date' => now(),
                            ]);
                        }
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
            'index' => Pages\ListJenisPenyimpanans::route('/'),
            'create' => Pages\CreateJenisPenyimpanan::route('/create'),
            'edit' => Pages\EditJenisPenyimpanan::route('/{record}/edit'),
        ];
    }
}
