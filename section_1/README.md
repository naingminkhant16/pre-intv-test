# Section 1 Backend - REST API With Laravel

This project exposes a RESTful endpoints at `/api/projects` and supports listing, creating, viewing, updating, and deleting project resource.

## Requirements

- PHP 8.3+
- Composer
- Node.js and npm
- A database such as MySQL, PostgreSQL, or SQLite

## Run the project locally

1. Install PHP dependencies:

```bash
composer install
```

2. Create the environment file:

```bash
cp .env.example .env
```

3. Configure the database in `.env`.

I used SQLite for local development, it can be done with MySQL or PostgreSQL as well.

```bash
touch database/database.sqlite
```

Then set the following in `.env`:

```env
DB_CONNECTION=sqlite
```

4. Generate the application key:

```bash
php artisan key:generate
```

5. Run the database migrations and seed sample data:

```bash
php artisan migrate --seed
```

6. Start the API server:

```bash
php artisan serve
```

The APIs will be available at:

```text
http://localhost:8000
```

## Project endpoints

Base URL:

```text
/api
```

### 1) Get all projects

```http
GET /api/projects
```

Returns a paginated list of projects.

### 2) Create a project

```http
POST /api/projects
```

Request body example:

```json
{
    "name": "Website redesign",
    "description": "Redesign the marketing site and improve conversion flow.",
    "start_date": "2026-09-24",
    "end_date": "2026-10-30",
    "status": "in_progress"
}
```

Valid `status` values:

- `planned`
- `in_progress`
- `completed`

### 3) Get one project

```http
GET /api/projects/{project}
```

Returns a single project record by ID.

### 4) Update a project

```http
PUT /api/projects/{project}
```

or

```http
PATCH /api/projects/{project}
```

Example payload:

```json
{
    "name": "Website redesign v2",
    "description": "Redesign the marketing site and improve conversion flow with new content blocks.",
    "start_date": "2026-09-24",
    "end_date": "2026-11-15",
    "status": "completed"
}
```

### 5) Delete a project

```http
DELETE /api/projects/{project}
```

This performs a soft delete and responds with HTTP 204 No Content.

## Project response structure

A project object looks like this:

```json
{
    "id": "8d77d1d8-7b5e-4e77-8f1d-8b6f13865a4d",
    "name": "Website redesign",
    "description": "Redesign the marketing site and improve conversion flow.",
    "start_date": "2026-09-24",
    "end_date": "2026-10-30",
    "status": "in_progress",
    "created_at": "2026-09-24 11:00:00",
    "updated_at": "2026-09-24 11:00:00"
}
```

## Validation rules

Project requests are validated by the `ProjectRequest` rules:

- `name`: required, string, between 5 and 100 characters, unique
- `description`: required, string, between 5 and 255 characters
- `start_date`: required, valid date in `Y-m-d` format
- `end_date`: required, valid date in `Y-m-d` format, must be on or after `start_date`
- `status`: required and must be one of the enum values above

## Useful commands

```bash
php artisan serve
php artisan migrate
php artisan migrate --seed
php artisan test
```
