<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $images = Image::latest()->get();
        return response()->json([
            'success' => true,
            'data' => $images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->url,
                    'filename' => $image->filename,
                    'mime_type' => $image->mime_type,
                    'size' => $image->size,
                    'created_at' => $image->created_at,
                ];
            })
        ]);
    }

    /**
     * Get only URLs of all images.
     */
    public function getUrls()
    {
        $baseUrl = config('app.url');
        $images = Image::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $images->map(function ($image) use ($baseUrl) {
                return $baseUrl . $image->url;
            })->values()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $uploadedImages = [];

        foreach ($request->file('images') as $file) {
            $path = $file->store('gallery', 'public');

            $image = Image::create([
                'path' => $path,
                'filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);

            $uploadedImages[] = [
                'id' => $image->id,
                'url' => $image->url,
                'filename' => $image->filename,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => count($uploadedImages) . ' image(s) uploaded successfully',
            'data' => $uploadedImages
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $image = Image::find($id);

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $image->id,
                'url' => $image->url,
                'filename' => $image->filename,
                'mime_type' => $image->mime_type,
                'size' => $image->size,
                'created_at' => $image->created_at,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $image = Image::find($id);

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Update image file if provided
        if ($request->hasFile('image')) {
            // Delete old image
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            $file = $request->file('image');
            $path = $file->store('gallery', 'public');

            $image->path = $path;
            $image->filename = $file->getClientOriginalName();
            $image->mime_type = $file->getMimeType();
            $image->size = $file->getSize();
            $image->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Image updated successfully',
            'data' => [
                'id' => $image->id,
                'url' => $image->url,
                'filename' => $image->filename,
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $image = Image::find($id);

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
        }

        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully'
        ]);
    }
}
