<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    /**
     * Get the organization chart image URL.
     *
     * @return JsonResponse
     */
    public function getTree(): JsonResponse
    {
        try {
            $chartPath = $this->getOrganizationChartPath();

            if (!$chartPath) {
                return response()->json([
                    'success' => false,
                    'message' => 'No organization chart has been uploaded yet',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'chart_url' => Storage::url($chartPath),
                    'updated_at' => $this->getChartUpdatedAt(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve organization chart',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get organization chart (alias for getTree for backward compatibility).
     *
     * @return JsonResponse
     */
    public function getRoots(): JsonResponse
    {
        return $this->getTree();
    }

    /**
     * Get organization chart (alias for getTree for backward compatibility).
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getNode(int $id): JsonResponse
    {
        return $this->getTree();
    }

    /**
     * Clear cache (kept for backward compatibility, but not needed anymore).
     *
     * @return JsonResponse
     */
    public function clearCache(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'No cache to clear - organization chart is loaded directly',
        ], 200);
    }

    /**
     * Get the organization chart path from storage.
     *
     * @return string|null
     */
    protected function getOrganizationChartPath(): ?string
    {
        $configPath = storage_path('app/organization_chart.json');

        if (file_exists($configPath)) {
            $config = json_decode(file_get_contents($configPath), true);
            return $config['chart_path'] ?? null;
        }

        return null;
    }

    /**
     * Get when the organization chart was last updated.
     *
     * @return string|null
     */
    protected function getChartUpdatedAt(): ?string
    {
        $configPath = storage_path('app/organization_chart.json');

        if (file_exists($configPath)) {
            $config = json_decode(file_get_contents($configPath), true);
            return $config['updated_at'] ?? null;
        }

        return null;
    }
}
