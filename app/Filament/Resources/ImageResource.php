<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImageResource\Pages;
use App\Filament\Resources\ImageResource\RelationManagers;
use App\Models\Image;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ImageResource extends Resource
{
    protected static ?string $model = Image::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Gallery';

    protected static ?string $navigationGroup = 'Gallery Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('path')
                    ->label('Gallery Images & Videos')
                    ->acceptedFileTypes(['image/*', 'video/*'])
                    ->multiple()
                    ->directory('gallery')
                    ->disk('public')
                    ->required()
                    ->columnSpanFull()
                    ->maxSize(20480)
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->maxFiles(10)
                    ->reorderable()
                    ->helperText('Upload images or videos (max 10 files, 20MB each)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label('Image')
                    ->disk('public')
                    ->square()
                    ->size(80),
                Tables\Columns\TextColumn::make('filename')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('mime_type')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(fn ($state) => number_format($state / 1024, 2) . ' KB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListImages::route('/'),
            'create' => Pages\CreateImage::route('/create'),
            'edit' => Pages\EditImage::route('/{record}/edit'),
        ];
    }
}
