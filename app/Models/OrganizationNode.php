<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class OrganizationNode extends Model
{
    protected $fillable = [
        'parent_id',
        'title',
        'names',
        'order',
        'type',
        'is_active',
    ];

    protected $casts = [
        'names' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        // Invalidate cache when model is saved or deleted
        static::saved(function () {
            Cache::forget('organization_tree');
        });

        static::deleted(function () {
            Cache::forget('organization_tree');
        });
    }

    /**
     * Get the parent node.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(OrganizationNode::class, 'parent_id');
    }

    /**
     * Get all children nodes.
     */
    public function children(): HasMany
    {
        return $this->hasMany(OrganizationNode::class, 'parent_id')
            ->orderBy('order');
    }

    /**
     * Get all active children nodes.
     */
    public function activeChildren(): HasMany
    {
        return $this->children()->where('is_active', true);
    }

    /**
     * Get all descendant nodes recursively.
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Scope to get only root nodes (no parent).
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id')->orderBy('order');
    }

    /**
     * Scope to get only active nodes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the full organizational tree as nested array.
     */
    public static function getTree(bool $activeOnly = true): array
    {
        $query = static::with('children')->roots();

        if ($activeOnly) {
            $query->active();
        }

        $roots = $query->get();

        return $roots->map(function ($node) use ($activeOnly) {
            return static::buildTreeNode($node, $activeOnly);
        })->toArray();
    }

    /**
     * Recursively build tree node with children.
     */
    protected static function buildTreeNode(OrganizationNode $node, bool $activeOnly = true): array
    {
        $children = $activeOnly ? $node->activeChildren : $node->children;

        return [
            'id' => $node->id,
            'title' => $node->title,
            'names' => $node->names,
            'type' => $node->type,
            'order' => $node->order,
            'is_active' => $node->is_active,
            'children' => $children->map(function ($child) use ($activeOnly) {
                return static::buildTreeNode($child, $activeOnly);
            })->toArray(),
        ];
    }

    /**
     * Validate that title is unique per parent.
     */
    public static function validateUniqueTitlePerParent(?int $parentId, string $title, ?int $excludeId = null): bool
    {
        $query = static::where('parent_id', $parentId)
            ->where('title', $title);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->doesntExist();
    }
}
