<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RencanaKebutuhanResource\Pages;
use App\Filament\Resources\RencanaKebutuhanResource\RelationManagers;
use App\Models\RencanaKebutuhan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RencanaKebutuhanResource extends Resource
{
    protected static ?string $model = RencanaKebutuhan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    public static function getNavigationLabel(): string
    {
        return 'Rencana Kebutuhan';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode')
                    ->label('Kode')
                    ->disabled()
                    ->placeholder('Generated By System'),

                Forms\Components\TextInput::make('nama_kebutuhan')
                    ->label('Nama Kebutuhan')
                    ->required(),

                Forms\Components\TextInput::make('balance_kebutuhan')
                    ->label('Balance Kebutuhan')
                    ->numeric()
                    ->rules(['regex:/^\d*$/'])  
                    ->required(),

                Forms\Components\Textarea::make('catatan_kebutuhan')
                    ->label('Catatan Kebutuhan')
                    ->rows(3),

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

                Tables\Columns\TextColumn::make('nama_kebutuhan')
                    ->label('Nama Kebutuhan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('balance_kebutuhan')
                    ->label('Balance Kebutuhan')
                    ->formatStateUsing(fn ($state) => number_format($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_by')
                    ->label('Dibuat Oleh'),
            ])
            
            ->query(RencanaKebutuhan::where('deleted', false))

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
            'index' => Pages\ListRencanaKebutuhans::route('/'),
            'create' => Pages\CreateRencanaKebutuhan::route('/create'),
            'edit' => Pages\EditRencanaKebutuhan::route('/{record}/edit'),
        ];
    }

}


