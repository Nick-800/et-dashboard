# ET Dashboard - Gallery & Project Management System

A Laravel-based RESTful API and Filament admin dashboard for managing an image gallery and project portfolio.

## Features

### 🖼️ Image Gallery Management
- Upload, edit, and delete images
- Store images in a centralized gallery
- Automatic file metadata tracking (size, mime type, filename)
- Image preview and management via Filament dashboard

### 📁 Project Management
- Create, update, and delete projects
- Project attributes: type, name, description, year, services, images
- Services as an array of service names
- Images as an array of URLs/paths from the gallery
- Rich text editor for project descriptions

### 🔐 Authentication
- Laravel Sanctum token-based authentication
- User registration and login
- Protected API endpoints
- Filament admin panel with secure login

### 📊 Admin Dashboard
- Beautiful Filament v3 interface
- Image gallery management with visual previews
- Project management with intuitive forms
- Bulk actions and search functionality

---

## Installation

### Prerequisites
- PHP 8.2+
- Composer
- MySQL/PostgreSQL/SQLite
- Node.js & NPM (for frontend assets)

### Setup Steps

1. **Clone the repository** (if applicable)
   ```bash
   cd /home/nick/Documents/Projects/et/ET-dashboard
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   
   Configure your database in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=et_dashboard
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Create storage symlink**
   ```bash
   php artisan storage:link
   ```

7. **Create admin user for Filament**
   ```bash
   php artisan make:filament-user
   ```
   
   Enter admin credentials when prompted.

8. **Build frontend assets**
   ```bash
   npm run build
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

---

## Access Points

### Filament Admin Dashboard
```
http://localhost:8000/admin
```
Login with the credentials you created during setup.

### API Base URL
```
http://localhost:8000/api
```

---

## Quick Start Guide

### 1. Create an Admin User
Already done during installation:
- Email: `admin@mail.com`
- Password: (as entered during setup)

### 2. Access the Dashboard
Navigate to `http://localhost:8000/admin` and log in.

### 3. Upload Images
- Go to "Images" in the admin sidebar
- Click "Create" 
- Upload an image and optionally add title/description
- Images are stored in `storage/app/public/gallery/`

### 4. Create Projects
- Go to "Projects" in the admin sidebar
- Click "Create"
- Fill in:
  - **Name**: Project name
  - **Type**: Project category (e.g., "Web Development")
  - **Year**: Project completion year
  - **Description**: Rich text description
  - **Services**: Add service tags (press Enter after each)
  - **Images**: Add image URLs from your gallery

### 5. Use the API
See [API_DOCUMENTATION.md](API_DOCUMENTATION.md) for detailed API endpoints.

#### Example: Login via API
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@mail.com",
    "password": "your-password"
  }'
```

#### Example: Upload Image via API
```bash
curl -X POST http://localhost:8000/api/images \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "image=@/path/to/image.jpg" \
  -F "title=My Image" \
  -F "description=A beautiful image"
```

#### Example: Get All Projects
```bash
curl -X GET http://localhost:8000/api/projects
```

---

## Project Structure

```
app/
├── Filament/
│   └── Resources/          # Filament admin resources
│       ├── ImageResource.php
│       └── ProjectResource.php
├── Http/
│   └── Controllers/
│       └── Api/            # API Controllers
│           ├── AuthController.php
│           ├── ImageController.php
│           └── ProjectController.php
└── Models/
    ├── Image.php           # Image model
    ├── Project.php         # Project model
    └── User.php

database/
└── migrations/
    ├── 2026_01_14_010318_create_images_table.php
    └── 2026_01_14_010351_create_projects_table.php

routes/
└── api.php                 # API routes

storage/
└── app/
    └── public/
        └── gallery/        # Image storage directory
```

---

## Database Schema

### Images Table
| Column      | Type    | Description                    |
|-------------|---------|--------------------------------|
| id          | bigint  | Primary key                    |
| title       | string  | Image title (optional)         |
| description | text    | Image description (optional)   |
| path        | string  | Storage path                   |
| filename    | string  | Original filename              |
| mime_type   | string  | File MIME type                 |
| size        | integer | File size in bytes             |
| created_at  | timestamp | Creation timestamp           |
| updated_at  | timestamp | Last update timestamp        |

### Projects Table
| Column      | Type    | Description                    |
|-------------|---------|--------------------------------|
| id          | bigint  | Primary key                    |
| type        | string  | Project type/category          |
| name        | string  | Project name                   |
| description | text    | Project description            |
| year        | integer | Project year                   |
| services    | json    | Array of services              |
| images      | json    | Array of image URLs            |
| created_at  | timestamp | Creation timestamp           |
| updated_at  | timestamp | Last update timestamp        |

---

## API Features

### Public Endpoints
- `GET /api/images` - List all images
- `GET /api/images/{id}` - Get single image
- `GET /api/projects` - List all projects
- `GET /api/projects/{id}` - Get single project

### Protected Endpoints (Require Authentication)
- `POST /api/register` - Register new user
- `POST /api/login` - Login
- `POST /api/logout` - Logout
- `POST /api/images` - Upload image
- `POST /api/images/{id}` - Update image
- `DELETE /api/images/{id}` - Delete image
- `POST /api/projects` - Create project
- `PUT /api/projects/{id}` - Update project
- `DELETE /api/projects/{id}` - Delete project

See [API_DOCUMENTATION.md](API_DOCUMENTATION.md) for detailed documentation.

---

## Technology Stack

- **Backend**: Laravel 11
- **Admin Panel**: Filament v3
- **Authentication**: Laravel Sanctum
- **Database**: MySQL/PostgreSQL/SQLite
- **File Storage**: Laravel Storage (local disk)
- **Frontend**: Livewire (for Filament)

---

## Development

### Running Tests
```bash
php artisan test
```

### Code Formatting
```bash
./vendor/bin/pint
```

### Clear Caches
```bash
php artisan optimize:clear
```

---

## Security

- All image upload/edit/delete operations require authentication
- All project create/update/delete operations require authentication
- CSRF protection on web routes
- Rate limiting on API routes
- Input validation on all endpoints
- Secure file upload handling

---

## Troubleshooting

### Images not displaying
```bash
php artisan storage:link
```

### Permission issues
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Clear all caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## License

This project is open-sourced software licensed under the MIT license.

---

## Support

For issues or questions, please refer to:
- [API Documentation](API_DOCUMENTATION.md)
- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
