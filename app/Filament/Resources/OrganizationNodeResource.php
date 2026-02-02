<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationNodeResource\Pages;
use App\Models\OrganizationNode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrganizationNodeResource extends Resource
{
    protected static ?string $model = OrganizationNode::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Organization Structure';

    protected static ?string $modelLabel = 'Organization Node';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Node Information')
                    ->schema([
                        Forms\Components\Select::make('parent_id')
                            ->label('Parent Node')
                            ->relationship('parent', 'title')
                            ->searchable()
                            ->preload()
                            ->placeholder('None (Root Level)')
                            ->helperText('Select a parent node to nest this under. Leave empty for root level.'),

                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule, $get) {
                                return $rule->where('parent_id', $get('parent_id'));
                            })
                            ->helperText('Must be unique within the same parent level.'),

                        Forms\Components\Repeater::make('names')
                            ->label('Names')
                            ->required()
                            ->minItems(1)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('Add Name')
                            ->collapsible()
                            ->helperText('Add one or more names associated with this node.'),

                        Forms\Components\Select::make('type')
                            ->required()
                            ->options([
                                'leadership' => 'Leadership',
                                'department' => 'Department',
                                'sub-department' => 'Sub-Department',
                                'team' => 'Team',
                                'division' => 'Division',
                            ])
                            ->default('department'),

                        Forms\Components\TextInput::make('order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->helperText('Lower numbers appear first within the same parent level.'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Only active nodes will be shown in the public organizational chart.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('names')
                    ->label('Names')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return collect($state)->pluck('name')->filter()->join(', ');
                        }
                        return 'N/A';
                    })
                    ->wrap()
                    ->limit(50),

                Tables\Columns\TextColumn::make('parent.title')
                    ->label('Parent')
                    ->sortable()
                    ->placeholder('Root Level')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'leadership',
                        'success' => 'department',
                        'warning' => 'sub-department',
                        'info' => 'team',
                        'secondary' => 'division',
                    ]),

                Tables\Columns\TextColumn::make('order')
                    ->label('Order')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'leadership' => 'Leadership',
                        'department' => 'Department',
                        'sub-department' => 'Sub-Department',
                        'team' => 'Team',
                        'division' => 'Division',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All nodes')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),

                Tables\Filters\Filter::make('root_only')
                    ->label('Root Level Only')
                    ->query(fn (Builder $query): Builder => $query->whereNull('parent_id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
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
            'index' => Pages\ListOrganizationNodes::route('/'),
            'create' => Pages\CreateOrganizationNode::route('/create'),
            'edit' => Pages\EditOrganizationNode::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('parent');
    }
}
