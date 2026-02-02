<?php

namespace App\Filament\Resources\OrganizationNodeResource\Pages;

use App\Filament\Resources\OrganizationNodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrganizationNode extends EditRecord
{
    protected static string $resource = OrganizationNodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert names array to repeater format
        if (isset($data['names']) && is_array($data['names'])) {
            $data['names'] = collect($data['names'])->map(function ($name) {
                return ['name' => $name];
            })->toArray();
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Convert repeater format back to simple array
        if (isset($data['names']) && is_array($data['names'])) {
            $data['names'] = collect($data['names'])
                ->pluck('name')
                ->filter()
                ->values()
                ->toArray();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
