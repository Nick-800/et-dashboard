# API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication

This API uses Laravel Sanctum for authentication. After logging in, you'll receive a bearer token that should be included in the `Authorization` header for protected routes.

```
Authorization: Bearer {your-token}
```

---

## Authentication Endpoints

### Register
**POST** `/register`

Create a new user account.

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "1|abcdef..."
  }
}
```

---

### Login
**POST** `/login`

Authenticate a user and receive an access token.

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "2|ghijkl..."
  }
}
```

---

### Logout
**POST** `/logout`

Revoke the current access token. *(Requires Authentication)*

**Response (200):**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

## Image Gallery Endpoints

### Get All Images
**GET** `/images`

Retrieve all images from the gallery.

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Sample Image",
      "description": "A beautiful landscape",
      "url": "http://localhost:8000/storage/gallery/image.jpg",
      "filename": "image.jpg",
      "mime_type": "image/jpeg",
      "size": 102400,
      "created_at": "2026-01-14T00:00:00.000000Z"
    }
  ]
}
```

---

### Get Single Image
**GET** `/images/{id}`

Retrieve a specific image by ID.

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Sample Image",
    "description": "A beautiful landscape",
    "url": "http://localhost:8000/storage/gallery/image.jpg",
    "filename": "image.jpg",
    "mime_type": "image/jpeg",
    "size": 102400,
    "created_at": "2026-01-14T00:00:00.000000Z"
  }
}
```

---

### Upload Image
**POST** `/images` *(Requires Authentication)*

Upload one or multiple images to the gallery.

**Request (multipart/form-data):**
```
images[]: (file) - Required. One or multiple image files (jpeg, png, jpg, gif, svg, webp). Max 10MB each
title: (string) - Optional. Image title (applied to all images)
description: (string) - Optional. Image description (applied to all images)
```

**Example with curl (single image):**
```bash
curl -X POST http://localhost:8000/api/images \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "images[]=@/path/to/image1.jpg"
```

**Example with curl (multiple images):**
```bash
curl -X POST http://localhost:8000/api/images \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "images[]=@/path/to/image1.jpg" \
  -F "images[]=@/path/to/image2.jpg" \
  -F "images[]=@/path/to/image3.jpg" \
  -F "title=My Images" \
  -F "description=Gallery images"
```

**Response (201):**
```json
{
  "success": true,
  "message": "3 image(s) uploaded successfully",
  "data": [
    {
      "id": 2,
      "title": "My Images",
      "description": "Gallery images",
      "url": "http://localhost:8000/storage/gallery/image1.jpg",
      "filename": "image1.jpg"
    },
    {
      "id": 3,
      "title": "My Images",
      "description": "Gallery images",
      "url": "http://localhost:8000/storage/gallery/image2.jpg",
      "filename": "image2.jpg"
    },
    {
      "id": 4,
      "title": "My Images",
      "description": "Gallery images",
      "url": "http://localhost:8000/storage/gallery/image3.jpg",
      "filename": "image3.jpg"
    }
  ]
}
```

---

### Update Image
**POST** `/images/{id}` *(Requires Authentication)*

Update an existing image (file and/or metadata).

**Request (multipart/form-data):**
```
image: (file) - Optional. New image file
title: (string) - Optional. Updated title
description: (string) - Optional. Updated description
```

**Response (200):**
```json
{
  "success": true,
  "message": "Image updated successfully",
  "data": {
    "id": 2,
    "title": "Updated Title",
    "description": "Updated description",
    "url": "http://localhost:8000/storage/gallery/updated-image.jpg",
    "filename": "updated-image.jpg"
  }
}
```

---

### Delete Image
**DELETE** `/images/{id}` *(Requires Authentication)*

Delete an image from the gallery.

**Response (200):**
```json
{
  "success": true,
  "message": "Image deleted successfully"
}
```

---

## Project Management Endpoints

### Get All Projects
**GET** `/projects`

Retrieve all projects.

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "type": "Web Development",
      "name": "E-commerce Platform",
      "description": "A full-featured online store",
      "year": 2026,
      "services": ["Frontend", "Backend", "Database Design"],
      "images": [
        "http://localhost:8000/storage/gallery/project1.jpg",
        "http://localhost:8000/storage/gallery/project2.jpg"
      ],
      "created_at": "2026-01-14T00:00:00.000000Z",
      "updated_at": "2026-01-14T00:00:00.000000Z"
    }
  ]
}
```

---

### Get Single Project
**GET** `/projects/{id}`

Retrieve a specific project by ID.

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "type": "Web Development",
    "name": "E-commerce Platform",
    "description": "A full-featured online store",
    "year": 2026,
    "services": ["Frontend", "Backend", "Database Design"],
    "images": [
      "http://localhost:8000/storage/gallery/project1.jpg"
    ],
    "created_at": "2026-01-14T00:00:00.000000Z",
    "updated_at": "2026-01-14T00:00:00.000000Z"
  }
}
```

---

### Create Project
**POST** `/projects` *(Requires Authentication)*

Create a new project with optional image uploads.

**Request (multipart/form-data):**
```
type: (string) - Required. Project type/category
name: (string) - Required. Project name
description: (string) - Optional. Project description
year: (integer) - Required. Project year
services[]: (array of strings) - Required. Array of service names
images[]: (files) - Optional. One or multiple image files for the project
```

**Example with curl (with images):**
```bash
curl -X POST http://localhost:8000/api/projects \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "type=Web Development" \
  -F "name=E-commerce Platform" \
  -F "description=A full-featured online store" \
  -F "year=2026" \
  -F "services[]=Frontend Development" \
  -F "services[]=Backend API" \
  -F "services[]=Database Design" \
  -F "images[]=@/path/to/screenshot1.jpg" \
  -F "images[]=@/path/to/screenshot2.jpg"
```

**Response (201):**
```json
{
  "success": true,
  "message": "Project created successfully",
  "data": {
    "id": 2,
    "type": "Web Development",
    "name": "E-commerce Platform",
    "description": "A full-featured online store",
    "year": 2026,
    "services": ["Frontend Development", "Backend API", "Database Design"],
    "images": [
      "http://localhost:8000/storage/projects/screenshot1.jpg",
      "http://localhost:8000/storage/projects/screenshot2.jpg"
    ],
    "created_at": "2026-01-14T00:00:00.000000Z",
    "updated_at": "2026-01-14T00:00:00.000000Z"
  }
}
```

---

### Update Project
**PUT** `/projects/{id}` *(Requires Authentication)*

Update an existing project. Can add new images while keeping existing ones.

**Request (multipart/form-data):**
```
type: (string) - Optional. Updated project type
name: (string) - Optional. Updated project name
description: (string) - Optional. Updated description
year: (integer) - Optional. Updated year
services[]: (array) - Optional. Updated services array
new_images[]: (files) - Optional. New images to add to the project
keep_existing_images: (boolean) - Optional. Default true. Set to false to replace all images
```

**Example with curl:**
```bash
curl -X POST "http://localhost:8000/api/projects/2" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "_method=PUT" \
  -F "name=Updated Project Name" \
  -F "new_images[]=@/path/to/new-image.jpg" \
  -F "keep_existing_images=true"
```

**Response (200):**
```json
{
  "success": true,
  "message": "Project updated successfully",
  "data": {
    "id": 2,
    "type": "Web Development",
    "name": "Updated Project Name",
    "description": "Updated description",
    "year": 2026,
    "services": ["Frontend Development", "Backend API"],
    "images": [
      "http://localhost:8000/storage/projects/old-image.jpg",
      "http://localhost:8000/storage/projects/new-image.jpg"
    ],
    "created_at": "2026-01-14T00:00:00.000000Z",
    "updated_at": "2026-01-14T01:30:00.000000Z"
  }
}
```

---

### Delete Project
**DELETE** `/projects/{id}` *(Requires Authentication)*

Delete a project.

**Response (200):**
```json
{
  "success": true,
  "message": "Project deleted successfully"
}
```

---

## Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Image not found"
}
```

### Unauthorized (401)
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

### Unauthenticated (401)
```json
{
  "message": "Unauthenticated."
}
```

---

## Notes

1. **Image Storage**: 
   - **Gallery images** are stored in `storage/app/public/gallery/` 
   - **Project images** are stored in `storage/app/public/projects/`
   - All images are accessed via `/storage/{folder}/{filename}`

2. **Multiple Image Uploads**: 
   - Gallery: Use `images[]` parameter to upload multiple images at once
   - Projects: Use `images[]` parameter when creating, or `new_images[]` when updating

3. **Authentication**: Protected endpoints require a valid bearer token obtained from the login or register endpoints.

4. **File Size Limits**: Image uploads are limited to 10MB (10240KB) per file.

5. **Supported Image Formats**: jpeg, png, jpg, gif, svg, webp

6. **Services in Projects**: Services field is an array of strings containing service names.

7. **Project Images**: Images are automatically uploaded and stored with the project. When a project is deleted, all its images are automatically deleted.

8. **Year Validation**: Year must be between 1900 and current year + 10.

9. **Keeping Existing Images**: When updating a project, set `keep_existing_images=true` (default) to keep old images and add new ones, or `false` to replace all images.
