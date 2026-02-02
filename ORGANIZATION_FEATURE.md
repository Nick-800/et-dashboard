# Organization Structure Management Feature

## Overview
This feature enables dynamic management of organizational structure using a hierarchical adjacency list pattern. The system provides a full admin interface via Filament and a public API for frontend consumption.

## Files Created

### Database
- **Migration**: `database/migrations/2026_02_02_000000_create_organization_nodes_table.php`
  - Creates `organization_nodes` table with hierarchical structure
  - Includes performance indexes on `parent_id`, `order`, and `is_active`

### Models
- **Model**: `app/Models/OrganizationNode.php`
  - Eloquent model with parent/child relationships
  - Automatic cache invalidation on save/delete
  - Tree building methods with active/inactive filtering
  - Validation helpers for unique titles per parent

### Filament Admin Interface
- **Resource**: `app/Filament/Resources/OrganizationNodeResource.php`
  - Full CRUD operations with validation
  - Parent-child relationships via dropdown
  - Repeater field for names array
  - Type selection (leadership, department, sub-department, team, division)
  - Bulk activate/deactivate actions
  - Filters for type, active status, and root nodes

- **Pages**:
  - `app/Filament/Resources/OrganizationNodeResource/Pages/ListOrganizationNodes.php`
  - `app/Filament/Resources/OrganizationNodeResource/Pages/CreateOrganizationNode.php`
  - `app/Filament/Resources/OrganizationNodeResource/Pages/EditOrganizationNode.php`

### API
- **Controller**: `app/Http/Controllers/Api/OrganizationController.php`
  - `GET /api/organization` - Full tree (cached 1 hour)
  - `GET /api/organization/roots` - Root nodes only
  - `GET /api/organization/{id}` - Specific node with children
  - `POST /api/organization/clear-cache` - Clear cache (auth required)

- **Routes**: Updated `routes/api.php`
  - Public access for tree retrieval
  - Protected cache clearing endpoint

## Installation Steps

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
```

### 3. Verify Filament Navigation
The "Organization Structure" menu item should appear in your Filament admin panel sidebar automatically.

## Database Schema

```sql
organization_nodes
├── id (primary key)
├── parent_id (nullable, foreign key to id)
├── title (string, unique per parent)
├── names (JSON array of strings)
├── order (integer, for sorting)
├── type (string: leadership, department, sub-department, team, division)
├── is_active (boolean, default true)
├── created_at
└── updated_at
```

## API Endpoints

### Get Full Organization Tree
```http
GET /api/organization
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Executive Leadership",
      "names": ["John Doe", "Jane Smith"],
      "type": "leadership",
      "order": 0,
      "is_active": true,
      "children": [
        {
          "id": 2,
          "title": "Engineering",
          "names": ["Bob Johnson"],
          "type": "department",
          "order": 0,
          "is_active": true,
          "children": []
        }
      ]
    }
  ],
  "cached_at": "2026-02-02T12:00:00.000000Z"
}
```

### Get Root Nodes Only
```http
GET /api/organization/roots
```

### Get Specific Node
```http
GET /api/organization/{id}
```

### Clear Cache (Protected)
```http
POST /api/organization/clear-cache
Authorization: Bearer {token}
```

## Features

### Admin Interface (Filament)
✅ Create, edit, delete organization nodes
✅ Parent-child relationships with dropdown selection
✅ Multiple names per node (repeater field)
✅ Node types with color-coded badges
✅ Sort order management
✅ Active/inactive toggle
✅ Bulk actions (activate, deactivate, delete)
✅ Filters by type, status, and root level
✅ Search by title and parent

### API
✅ Full tree structure with unlimited nesting
✅ 1-hour caching for performance
✅ Only active nodes returned to public
✅ Sorted by order field within each level
✅ Separate endpoints for roots and specific nodes
✅ Cache invalidation on model changes
✅ CORS-ready for React frontend

### Data Validation
✅ Title must be unique within same parent level
✅ Names array must have at least one entry
✅ Parent-child relationships prevent orphans (cascade delete)
✅ Type field with predefined options
✅ Required fields enforced

## Performance Optimizations

1. **Database Indexes**: Added on `parent_id`, `order`, and `is_active`
2. **Query Optimization**: Eager loading with `with('parent')` and `with('children')`
3. **Caching**: 1-hour cache for full tree (invalidated on changes)
4. **Efficient Tree Building**: Single query per level using relationships

## Usage Examples

### Creating Root Node
1. Go to Filament admin panel
2. Click "Organization Structure" in sidebar
3. Click "New Organization Node"
4. Leave "Parent Node" empty
5. Enter title, names, type
6. Set order (0 for first)
7. Save

### Creating Child Node
1. Follow steps 1-3 above
2. Select a parent from "Parent Node" dropdown
3. Complete remaining fields
4. Save

### Frontend Integration (React)
```javascript
// Fetch organization tree
const response = await fetch('/api/organization');
const { data } = await response.json();

// Recursive component example
function OrgNode({ node }) {
  return (
    <div className="org-node">
      <h3>{node.title}</h3>
      <ul>
        {node.names.map((name, i) => (
          <li key={i}>{name}</li>
        ))}
      </ul>
      {node.children.length > 0 && (
        <div className="children">
          {node.children.map(child => (
            <OrgNode key={child.id} node={child} />
          ))}
        </div>
      )}
    </div>
  );
}
```

## CORS Configuration

Laravel 11 handles CORS via the `HandleCors` middleware automatically. Ensure your `.env` has:

```env
# Allow your frontend domain
SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost:3000
SESSION_DOMAIN=localhost
```

For additional CORS control, publish the config:
```bash
php artisan config:publish cors
```

## Testing

### Test Large Trees
```php
// Tinker or seeder
use App\Models\OrganizationNode;

// Create 100 nodes with nesting
$root = OrganizationNode::create([
    'title' => 'Root',
    'names' => ['CEO'],
    'type' => 'leadership',
    'order' => 0,
]);

for ($i = 0; $i < 10; $i++) {
    $dept = OrganizationNode::create([
        'parent_id' => $root->id,
        'title' => "Department $i",
        'names' => ["Manager $i"],
        'type' => 'department',
        'order' => $i,
    ]);
    
    for ($j = 0; $j < 9; $j++) {
        OrganizationNode::create([
            'parent_id' => $dept->id,
            'title' => "Team $i-$j",
            'names' => ["Lead $j"],
            'type' => 'team',
            'order' => $j,
        ]);
    }
}
```

### Test API Performance
```bash
# Test cached response
curl http://localhost:8000/api/organization

# Clear cache
curl -X POST http://localhost:8000/api/organization/clear-cache \
  -H "Authorization: Bearer {your-token}"

# Test again to verify cache rebuild
curl http://localhost:8000/api/organization
```

## Troubleshooting

### Names field not saving
- Ensure JSON column type is supported (MySQL 5.7+, PostgreSQL 9.2+)
- Check that names array has at least one entry

### Parent dropdown empty
- Verify organization nodes exist in database
- Check that relationships are properly defined in model

### Cache not clearing automatically
- Verify model boot method includes cache invalidation
- Check cache driver in `.env` (file, redis, etc.)

### Filament resource not appearing
- Clear config cache: `php artisan config:clear`
- Verify resource is in `app/Filament/Resources/`
- Check file naming matches class name

## Future Enhancements (Optional)

- 🔄 Drag-and-drop tree reordering with `awcodes/filament-tree`
- 📊 Visual org chart in Filament with `blade-ui-kit/blade-heroicons`
- 🔍 Search across names, not just titles
- 📅 Historical tracking (archive old structures)
- 👥 User assignments to nodes
- 🎨 Custom node icons/colors

## Security Notes

- ✅ API endpoints are public (read-only) for frontend display
- ✅ Cache clearing requires authentication
- ✅ All write operations via Filament require admin login
- ✅ SQL injection protected by Eloquent ORM
- ✅ XSS protection via proper JSON encoding

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify database connection: `php artisan tinker` → `OrganizationNode::count()`
3. Test API response: `curl http://localhost:8000/api/organization`
