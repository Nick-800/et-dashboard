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

Upload a new image to the gallery.

**Request (multipart/form-data):**
```
image: (file) - Required. Image file (jpeg, png, jpg, gif, svg, webp). Max 10MB
title: (string) - Optional. Image title
description: (string) - Optional. Image description
```

**Response (201):**
```json
{
  "success": true,
  "message": "Image uploaded successfully",
  "data": {
    "id": 2,
    "title": "New Image",
    "description": "My description",
    "url": "http://localhost:8000/storage/gallery/new-image.jpg",
    "filename": "new-image.jpg"
  }
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

Create a new project.

**Request Body:**
```json
{
  "type": "Web Development",
  "name": "E-commerce Platform",
  "description": "A full-featured online store with payment integration",
  "year": 2026,
  "services": ["Frontend Development", "Backend API", "Database Design"],
  "images": [
    "http://localhost:8000/storage/gallery/project1.jpg",
    "http://localhost:8000/storage/gallery/project2.jpg"
  ]
}
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
    "description": "A full-featured online store with payment integration",
    "year": 2026,
    "services": ["Frontend Development", "Backend API", "Database Design"],
    "images": [
      "http://localhost:8000/storage/gallery/project1.jpg",
      "http://localhost:8000/storage/gallery/project2.jpg"
    ],
    "created_at": "2026-01-14T00:00:00.000000Z",
    "updated_at": "2026-01-14T00:00:00.000000Z"
  }
}
```

---

### Update Project
**PUT** `/projects/{id}` *(Requires Authentication)*

Update an existing project.

**Request Body:**
```json
{
  "type": "Mobile Development",
  "name": "Updated Project Name",
  "description": "Updated description",
  "year": 2027,
  "services": ["iOS Development", "Android Development"],
  "images": ["http://localhost:8000/storage/gallery/new-image.jpg"]
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Project updated successfully",
  "data": {
    "id": 2,
    "type": "Mobile Development",
    "name": "Updated Project Name",
    "description": "Updated description",
    "year": 2027,
    "services": ["iOS Development", "Android Development"],
    "images": ["http://localhost:8000/storage/gallery/new-image.jpg"],
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

1. **Image Upload**: Images are stored in the `storage/app/public/gallery` directory and accessed via `/storage/gallery/{filename}`.

2. **Authentication**: Protected endpoints require a valid bearer token obtained from the login or register endpoints.

3. **File Size Limits**: Image uploads are limited to 10MB (10240KB).

4. **Supported Image Formats**: jpeg, png, jpg, gif, svg, webp

5. **Services & Images in Projects**: Both fields are arrays of strings. Services contain service names, while images contain URLs or paths to images.

6. **Year Validation**: Year must be between 1900 and current year + 10.
