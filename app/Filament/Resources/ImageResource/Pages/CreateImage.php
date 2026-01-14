<?php

namespace App\Filament\Resources\ImageResource\Pages;

use App\Filament\Resources\ImageResource;
use App\Models\Image;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CreateImage extends CreateRecord
{
    protected static string $resource = ImageResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Handle multiple file uploads
        $paths = is_array($data['path']) ? $data['path'] : [$data['path']];

        // Create first image as the main record
        $firstPath = array_shift($paths);
        $fileInfo = $this->getFileInfo($firstPath);

        $image = Image::create([
            'path' => $firstPath,
            'filename' => $fileInfo['filename'],
            'mime_type' => $fileInfo['mime_type'],
            'size' => $fileInfo['size'],
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
        ]);

        // Create additional images if multiple files were uploaded
        foreach ($paths as $path) {
            $fileInfo = $this->getFileInfo($path);
            Image::create([
                'path' => $path,
                'filename' => $fileInfo['filename'],
                'mime_type' => $fileInfo['mime_type'],
                'size' => $fileInfo['size'],
                'title' => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
            ]);
        }

        return $image;
    }

    protected function getFileInfo(string $path): array
    {
        $fullPath = Storage::disk('public')->path($path);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fullPath);
        finfo_close($finfo);

        return [
            'filename' => basename($path),
            'mime_type' => $mimeType,
            'size' => Storage::disk('public')->size($path),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
