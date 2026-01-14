<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Gallery Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Project Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Project Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('type')
                            ->label('Project Type')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Web Development, Mobile App, etc.')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('year')
                            ->label('Year')
                            ->required()
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y') + 10)
                            ->default(date('Y'))
                            ->columnSpan(1),
                        Forms\Components\RichEditor::make('description')
                            ->label('Description')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'bulletList',
                                'orderedList',
                                'link',
                            ]),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Services')
                    ->schema([
                        Forms\Components\TagsInput::make('services')
                            ->label('Services Provided')
                            ->placeholder('Add a service')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Press Enter after each service to add it'),
                    ]),
                Forms\Components\Section::make('Project Images')
                    ->schema([
                        Forms\Components\FileUpload::make('images')
                            ->label('Project Images')
                            ->image()
                            ->multiple()
                            ->directory('projects')
                            ->disk('public')
                            ->columnSpanFull()
                            ->maxFiles(20)
                            ->reorderable()
                            ->helperText('Upload one or multiple images for this project (max 20 files, 10MB each)')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Project Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('year')
                    ->label('Year')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('services')
                    ->label('Services')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', array_slice($state, 0, 3)) : '')
                    ->limit(50)
                    ->tooltip(fn ($state) => is_array($state) && count($state) > 3 ? implode(', ', $state) : null),
                Tables\Columns\TextColumn::make('images')
                    ->label('Images')
                    ->formatStateUsing(fn ($state) => is_array($state) ? count($state) . ' image(s)' : '0 images')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
