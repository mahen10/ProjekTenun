<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenunResource\Pages;
use App\Filament\Resources\TenunResource\RelationManagers;
use App\Models\Tenun;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenunResource extends Resource
{
    protected static ?string $model = Tenun::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_usaha')
                    ->required()
                    ->label('Nama Usaha'),

                Forms\Components\Textarea::make('alamat')
                    ->required()
                    ->label('Alamat'),

                Forms\Components\TextInput::make('no_telepon')
                    ->required()
                    ->label('Nomor Telepon'),

                Forms\Components\TextInput::make('user_id')
                    ->numeric()
                    ->label('ID User'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_usaha')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('alamat')->limit(20),
                Tables\Columns\TextColumn::make('no_telepon'),
                Tables\Columns\TextColumn::make('user_id'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTenuns::route('/'),
            'create' => Pages\CreateTenun::route('/create'),
            'edit' => Pages\EditTenun::route('/{record}/edit'),
        ];
    }
}
