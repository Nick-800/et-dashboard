<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resources.
     */
    public function index()
    {
        $projects = Project::orderBy('order')->get()->map(function ($project) {
            return [
                'id' => $project->id,
                'type' => $project->type,
                'name' => $project->name,
                'description' => $project->description,
                'year' => $project->year,
                'services' => $project->services,
                'images' => $project->image_urls,
                'image_count' => is_array($project->images) ? count($project->images) : 0,
                'order' => $project->order,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'services' => 'required|array',
            'services.*' => 'string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('projects', 'public');
                $imagePaths[] = $path;
            }
        }

        // Set order to highest + 1
        $maxOrder = Project::max('order') ?? -1;

        $project = Project::create([
            'type' => $request->type,
            'name' => $request->name,
            'description' => $request->description,
            'year' => $request->year,
            'services' => $request->services,
            'images' => $imagePaths,
            'order' => $maxOrder + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully',
            'data' => [
                'id' => $project->id,
                'type' => $project->type,
                'name' => $project->name,
                'description' => $project->description,
                'year' => $project->year,
                'services' => $project->services,
                'images' => $project->image_urls,
                'order' => $project->order,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at,
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $project->id,
                'type' => $project->type,
                'name' => $project->name,
                'description' => $project->description,
                'year' => $project->year,
                'services' => $project->services,
                'images' => $project->image_urls,
                'image_count' => is_array($project->images) ? count($project->images) : 0,
                'order' => $project->order,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'services' => 'nullable|array',
            'services.*' => 'string',
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'keep_existing_images' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle image updates
        $imagePaths = $request->input('keep_existing_images', true) ? $project->images : [];

        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $path = $image->store('projects', 'public');
                $imagePaths[] = $path;
            }
        }

        // Update project data
        $updateData = array_filter([
            'type' => $request->type,
            'name' => $request->name,
            'description' => $request->description,
            'year' => $request->year,
            'services' => $request->services,
        ], function ($value) {
            return $value !== null;
        });

        $updateData['images'] = $imagePaths;

        $project->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully',
            'data' => [
                'id' => $project->id,
                'type' => $project->type,
                'name' => $project->name,
                'description' => $project->description,
                'year' => $project->year,
                'services' => $project->services,
                'images' => $project->image_urls,
                'order' => $project->order,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at,
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully'
        ]);
    }

    /**
     * Reorder projects
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'projects' => 'required|array',
            'projects.*.id' => 'required|exists:projects,id',
            'projects.*.order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->projects as $projectData) {
            Project::where('id', $projectData['id'])->update(['order' => $projectData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Projects reordered successfully'
        ]);
    }
}
