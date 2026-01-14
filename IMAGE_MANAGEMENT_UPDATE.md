# System Update - Separate Image Management

## Changes Made

### ✅ Gallery Images (Separate & Independent)
- **Location**: `storage/app/public/gallery/`
- **Purpose**: Standalone image gallery
- **Features**:
  - Upload **one or multiple images at once** via dashboard
  - Upload **one or multiple images at once** via API
  - Each image stored independently
  - Max 10 images per upload in dashboard
  - No file limit via API (just use `images[]` array)

### ✅ Project Images (Separate & Independent)
- **Location**: `storage/app/public/projects/`
- **Purpose**: Images specific to each project
- **Features**:
  - Upload **one or multiple images at once** via dashboard
  - Upload **one or multiple images at once** via API
  - Max 20 images per project in dashboard
  - Images stored with project and deleted when project is deleted
  - Can add more images when updating a project

---

## Using the Dashboard

### Gallery Management
1. Go to **Images** in the admin panel
2. Click **Create**
3. Upload one or multiple images using drag-and-drop
4. Add optional title and description (applies to all images)
5. Images are stored in the gallery

### Project Management
1. Go to **Projects** in the admin panel
2. Click **Create** or **Edit**
3. Fill in project details (name, type, year, description, services)
4. In the **Project Images** section, upload one or multiple images
5. Images are stored with the project
6. When you delete a project, its images are automatically deleted

---

## Using the API

### Upload Multiple Gallery Images

```bash
curl -X POST http://127.0.0.1:8000/api/images \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "images[]=@/path/to/image1.jpg" \
  -F "images[]=@/path/to/image2.jpg" \
  -F "images[]=@/path/to/image3.jpg" \
  -F "title=Gallery Images" \
  -F "description=My collection"
```

### Create Project with Multiple Images

```bash
curl -X POST http://127.0.0.1:8000/api/projects \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "type=Web Development" \
  -F "name=Portfolio Website" \
  -F "description=A modern portfolio" \
  -F "year=2026" \
  -F "services[]=Frontend" \
  -F "services[]=Backend" \
  -F "images[]=@/path/to/screenshot1.jpg" \
  -F "images[]=@/path/to/screenshot2.jpg" \
  -F "images[]=@/path/to/screenshot3.jpg"
```

### Update Project - Add More Images

```bash
curl -X POST "http://127.0.0.1:8000/api/projects/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "_method=PUT" \
  -F "new_images[]=@/path/to/new-screenshot.jpg" \
  -F "keep_existing_images=true"
```

---

## Key Differences

| Feature | Gallery Images | Project Images |
|---------|---------------|----------------|
| **Storage Location** | `storage/public/gallery/` | `storage/public/projects/` |
| **Purpose** | Standalone image gallery | Images for specific projects |
| **Upload Method** | Bulk upload (1-10 at once) | Bulk upload (1-20 at once) |
| **Deletion** | Manual only | Auto-deleted with project |
| **API Endpoint** | `POST /api/images` | Part of project endpoints |
| **API Parameter** | `images[]` | `images[]` (create) or `new_images[]` (update) |

---

## Important Notes

1. **Gallery and Projects are completely separate**
   - Gallery images are standalone
   - Project images belong to specific projects
   - They use different storage directories

2. **Multiple Upload Support**
   - Both support uploading multiple images at once
   - Dashboard: Use the file picker to select multiple files
   - API: Use array syntax `images[]` or `new_images[]`

3. **Automatic Cleanup**
   - Deleting an image from gallery only deletes that image
   - Deleting a project automatically deletes all its images

4. **Updating Projects**
   - Use `new_images[]` to add more images
   - Use `keep_existing_images=true` (default) to keep old images
   - Use `keep_existing_images=false` to replace all images

---

## Testing the Changes

### Test Gallery Upload (Multiple Images)
```bash
# Login first
TOKEN=$(curl -s -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@mail.com","password":"your-password"}' \
  | jq -r '.data.token')

# Upload multiple images
curl -X POST http://127.0.0.1:8000/api/images \
  -H "Authorization: Bearer $TOKEN" \
  -F "images[]=@image1.jpg" \
  -F "images[]=@image2.jpg"
```

### Test Project with Images
```bash
# Create project with images
curl -X POST http://127.0.0.1:8000/api/projects \
  -H "Authorization: Bearer $TOKEN" \
  -F "type=Web App" \
  -F "name=Test Project" \
  -F "year=2026" \
  -F "services[]=Development" \
  -F "images[]=@project-image1.jpg" \
  -F "images[]=@project-image2.jpg"
```

---

## Updated Documentation

✅ [API_DOCUMENTATION.md](API_DOCUMENTATION.md) - Updated with new endpoints and examples
✅ Dashboard forms updated with multiple file upload support
✅ All API endpoints updated to handle multiple images

---

## Summary

Your system now has **two completely separate image management systems**:

1. **Gallery**: A standalone image collection for general use
2. **Project Images**: Images that belong to specific projects

Both support **multiple image uploads** at once, making it much more efficient to manage images!
