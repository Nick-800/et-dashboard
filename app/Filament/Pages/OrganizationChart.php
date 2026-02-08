<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class OrganizationChart extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static string $view = 'filament.pages.organization-chart';

    protected static ?string $navigationLabel = 'Organization Chart';

    protected static ?string $title = 'Organization Chart';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'organization_chart' => $this->getOrganizationChart(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Organization Structure')
                    ->description('Upload a single image showing your complete organizational structure')
                    ->schema([
                        Forms\Components\FileUpload::make('organization_chart')
                            ->label('Organization Chart Image')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(5120)
                            ->directory('organization-charts')
                            ->visibility('public')
                            ->downloadable()
                            ->openable()
                            ->helperText('Upload an image of your organizational structure. Recommended: PNG or JPG, max 5MB'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Delete old chart if exists and new one is uploaded
        $oldChart = $this->getOrganizationChart();
        if ($oldChart && $oldChart !== $data['organization_chart'] && Storage::disk('public')->exists($oldChart)) {
            Storage::disk('public')->delete($oldChart);
        }

        // Save the new chart path to a config file or database
        $configPath = storage_path('app/organization_chart.json');
        file_put_contents($configPath, json_encode([
            'chart_path' => $data['organization_chart'],
            'updated_at' => now()->toDateTimeString(),
        ]));

        Notification::make()
            ->title('Organization chart updated successfully')
            ->success()
            ->send();
    }

    public function getOrganizationChart(): ?string
    {
        $configPath = storage_path('app/organization_chart.json');

        if (file_exists($configPath)) {
            $config = json_decode(file_get_contents($configPath), true);
            return $config['chart_path'] ?? null;
        }

        return null;
    }
}
