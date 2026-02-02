<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrganizationNode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class OrganizationController extends Controller
{
    /**
     * Get the full organizational tree structure.
     * Returns cached data for performance (1 hour cache).
     *
     * @return JsonResponse
     */
    public function getTree(): JsonResponse
    {
        try {
            // Cache the tree for 1 hour to improve performance
            $tree = Cache::remember('organization_tree', 3600, function () {
                return OrganizationNode::getTree(activeOnly: true);
            });

            return response()->json([
                'success' => true,
                'data' => $tree,
                'cached_at' => Cache::get('organization_tree_timestamp', now()->toISOString()),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve organizational tree',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get a specific node with its children.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getNode(int $id): JsonResponse
    {
        try {
            $node = OrganizationNode::active()
                ->with('activeChildren')
                ->findOrFail($id);

            $data = OrganizationNode::buildTreeNode($node, activeOnly: true);

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Organization node not found or inactive',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve organization node',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get root level nodes only.
     *
     * @return JsonResponse
     */
    public function getRoots(): JsonResponse
    {
        try {
            $roots = OrganizationNode::active()
                ->roots()
                ->get()
                ->map(function ($node) {
                    return [
                        'id' => $node->id,
                        'title' => $node->title,
                        'names' => $node->names,
                        'type' => $node->type,
                        'order' => $node->order,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $roots,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve root nodes',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Clear the organization tree cache.
     * Useful for immediate updates.
     *
     * @return JsonResponse
     */
    public function clearCache(): JsonResponse
    {
        try {
            Cache::forget('organization_tree');
            Cache::forget('organization_tree_timestamp');

            return response()->json([
                'success' => true,
                'message' => 'Organization tree cache cleared successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
