# Organization Structure API - Quick Reference

## Base URL
```
http://localhost:8000/api
```

## Endpoints

### 1. Get Full Organization Tree
Retrieves the complete organizational hierarchy as nested JSON.

**Endpoint:** `GET /organization`

**Authentication:** None (Public)

**Response Example:**
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
          "children": [
            {
              "id": 3,
              "title": "Frontend Team",
              "names": ["Alice Cooper", "Charlie Brown"],
              "type": "team",
              "order": 0,
              "is_active": true,
              "children": []
            }
          ]
        }
      ]
    }
  ],
  "cached_at": "2026-02-02T12:00:00.000000Z"
}
```

**Caching:** Cached for 1 hour. Auto-invalidates on data changes.

---

### 2. Get Root Nodes Only
Retrieves only top-level organizational nodes (no parent).

**Endpoint:** `GET /organization/roots`

**Authentication:** None (Public)

**Response Example:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Executive Leadership",
      "names": ["John Doe", "Jane Smith"],
      "type": "leadership",
      "order": 0
    },
    {
      "id": 5,
      "title": "Board of Directors",
      "names": ["Michael Scott"],
      "type": "leadership",
      "order": 1
    }
  ]
}
```

---

### 3. Get Specific Node
Retrieves a single organizational node with its immediate children.

**Endpoint:** `GET /organization/{id}`

**Authentication:** None (Public)

**Path Parameters:**
- `id` (integer, required): The ID of the organization node

**Response Example:**
```json
{
  "success": true,
  "data": {
    "id": 2,
    "title": "Engineering",
    "names": ["Bob Johnson"],
    "type": "department",
    "order": 0,
    "is_active": true,
    "children": [
      {
        "id": 3,
        "title": "Frontend Team",
        "names": ["Alice Cooper", "Charlie Brown"],
        "type": "team",
        "order": 0,
        "is_active": true,
        "children": []
      },
      {
        "id": 4,
        "title": "Backend Team",
        "names": ["David Lee", "Emma Watson"],
        "type": "team",
        "order": 1,
        "is_active": true,
        "children": []
      }
    ]
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Organization node not found or inactive"
}
```

---

### 4. Clear Cache
Clears the cached organization tree. Useful for immediate updates.

**Endpoint:** `POST /organization/clear-cache`

**Authentication:** Required (Bearer token)

**Headers:**
```
Authorization: Bearer {your-sanctum-token}
```

**Response Example:**
```json
{
  "success": true,
  "message": "Organization tree cache cleared successfully"
}
```

**Error Response (401):**
```json
{
  "message": "Unauthenticated."
}
```

---

## React Integration Examples

### Fetching the Full Tree
```javascript
import { useState, useEffect } from 'react';

function OrgChart() {
  const [orgData, setOrgData] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch('/api/organization')
      .then(res => res.json())
      .then(data => {
        setOrgData(data.data);
        setLoading(false);
      })
      .catch(error => {
        console.error('Error fetching org chart:', error);
        setLoading(false);
      });
  }, []);

  if (loading) return <div>Loading...</div>;

  return (
    <div className="org-chart">
      {orgData.map(node => (
        <OrgNode key={node.id} node={node} />
      ))}
    </div>
  );
}
```

### Recursive Node Component
```javascript
function OrgNode({ node, level = 0 }) {
  const [expanded, setExpanded] = useState(level < 2);

  return (
    <div 
      className="org-node" 
      style={{ marginLeft: `${level * 20}px` }}
    >
      <div className="node-header">
        {node.children.length > 0 && (
          <button onClick={() => setExpanded(!expanded)}>
            {expanded ? '−' : '+'}
          </button>
        )}
        <h3>{node.title}</h3>
        <span className="node-type">{node.type}</span>
      </div>
      
      <div className="node-names">
        {node.names.map((name, i) => (
          <span key={i} className="name-badge">{name}</span>
        ))}
      </div>

      {expanded && node.children.length > 0 && (
        <div className="node-children">
          {node.children.map(child => (
            <OrgNode key={child.id} node={child} level={level + 1} />
          ))}
        </div>
      )}
    </div>
  );
}
```

### Fetching Specific Node
```javascript
async function fetchNode(nodeId) {
  const response = await fetch(`/api/organization/${nodeId}`);
  const { data } = await response.json();
  return data;
}

// Usage
const engineeringNode = await fetchNode(2);
console.log(engineeringNode.title); // "Engineering"
console.log(engineeringNode.children); // Array of child nodes
```

### Clearing Cache (Admin)
```javascript
async function clearOrgCache(token) {
  const response = await fetch('/api/organization/clear-cache', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
  });
  
  const result = await response.json();
  
  if (result.success) {
    console.log('Cache cleared!');
    // Optionally refetch the tree
    window.location.reload();
  }
}
```

---

## Data Structure

### Node Object
```typescript
interface OrganizationNode {
  id: number;
  title: string;
  names: string[];
  type: 'leadership' | 'department' | 'sub-department' | 'team' | 'division';
  order: number;
  is_active: boolean;
  children: OrganizationNode[];
}
```

---

## Error Handling

All endpoints return consistent error responses:

```json
{
  "success": false,
  "message": "Error description",
  "error": "Detailed error (only in debug mode)"
}
```

**HTTP Status Codes:**
- `200` - Success
- `401` - Unauthorized (authentication required)
- `404` - Not found (invalid node ID)
- `500` - Server error

---

## Performance Notes

1. **Caching:** The full tree is cached for 1 hour. Subsequent requests within this period will be served from cache.

2. **Cache Invalidation:** Cache is automatically cleared when:
   - Any node is created, updated, or deleted in Filament admin
   - Admin manually clears cache via API

3. **Large Trees:** Optimized for trees with 100+ nodes:
   - Database indexes on `parent_id`, `order`, `is_active`
   - Eager loading to prevent N+1 queries
   - Single cached response for all tree requests

4. **Recommended Usage:**
   - Use `/organization` for full chart rendering
   - Use `/organization/roots` for navigation menus
   - Use `/organization/{id}` for expandable sections

---

## Testing

### cURL Examples

**Get full tree:**
```bash
curl http://localhost:8000/api/organization
```

**Get roots only:**
```bash
curl http://localhost:8000/api/organization/roots
```

**Get specific node:**
```bash
curl http://localhost:8000/api/organization/2
```

**Clear cache (requires token):**
```bash
curl -X POST http://localhost:8000/api/organization/clear-cache \
  -H "Authorization: Bearer your-token-here" \
  -H "Accept: application/json"
```

### Postman Collection

Import these requests into Postman:

```json
{
  "info": {
    "name": "Organization API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Get Organization Tree",
      "request": {
        "method": "GET",
        "header": [],
        "url": {
          "raw": "{{base_url}}/api/organization",
          "host": ["{{base_url}}"],
          "path": ["api", "organization"]
        }
      }
    },
    {
      "name": "Get Root Nodes",
      "request": {
        "method": "GET",
        "header": [],
        "url": {
          "raw": "{{base_url}}/api/organization/roots",
          "host": ["{{base_url}}"],
          "path": ["api", "organization", "roots"]
        }
      }
    },
    {
      "name": "Get Specific Node",
      "request": {
        "method": "GET",
        "header": [],
        "url": {
          "raw": "{{base_url}}/api/organization/1",
          "host": ["{{base_url}}"],
          "path": ["api", "organization", "1"]
        }
      }
    },
    {
      "name": "Clear Cache",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{token}}"
          }
        ],
        "url": {
          "raw": "{{base_url}}/api/organization/clear-cache",
          "host": ["{{base_url}}"],
          "path": ["api", "organization", "clear-cache"]
        }
      }
    }
  ],
  "variable": [
    {
      "key": "base_url",
      "value": "http://localhost:8000"
    },
    {
      "key": "token",
      "value": "your-token-here"
    }
  ]
}
```

---

## CORS Configuration

If your React frontend is on a different domain/port, ensure CORS is configured:

**.env:**
```env
SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost:3000
SESSION_DOMAIN=localhost
```

Laravel 11 handles CORS automatically via the `HandleCors` middleware.

---

## Common Issues

### Empty Response
**Problem:** API returns empty array
**Solution:** Ensure active nodes exist and `is_active` is `true`

### Cache Not Updating
**Problem:** Changes in admin don't reflect in API
**Solution:** Cache should auto-clear. If not, call `/organization/clear-cache` endpoint

### 401 Unauthorized on Clear Cache
**Problem:** Cannot clear cache
**Solution:** Include valid Bearer token in Authorization header

### Slow Performance
**Problem:** API takes too long to respond
**Solution:** Cache should handle this. Check `storage/logs/laravel.log` for query issues
