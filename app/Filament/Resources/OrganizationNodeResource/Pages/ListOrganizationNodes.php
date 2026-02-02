<?php

namespace App\Filament\Resources\OrganizationNodeResource\Pages;

use App\Filament\Resources\OrganizationNodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrganizationNodes extends ListRecords
{
    protected static string $resource = OrganizationNodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Organization Structure';
    }
}
