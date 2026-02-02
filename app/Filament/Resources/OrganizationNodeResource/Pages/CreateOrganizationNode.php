<?php

namespace App\Filament\Resources\OrganizationNodeResource\Pages;

use App\Filament\Resources\OrganizationNodeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrganizationNode extends CreateRecord
{
    protected static string $resource = OrganizationNodeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure names array has the correct structure
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
