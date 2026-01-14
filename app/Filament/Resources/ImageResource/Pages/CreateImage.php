<?php

namespace App\Filament\Resources\ImageResource\Pages;

use App\Filament\Resources\ImageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CreateImage extends CreateRecord
{
    protected static string $resource = ImageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['path'])) {
            $file = request()->file('data.path');
            if ($file) {
                $data['filename'] = $file->getClientOriginalName();
                $data['mime_type'] = $file->getMimeType();
                $data['size'] = $file->getSize();
            }
        }
        return $data;
    }
}
