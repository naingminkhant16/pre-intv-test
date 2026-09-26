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

## Project coding style and architecture

This project follows a clear Laravel layered architecture so each component has a specific responsibility.

### 1) Routes

All API routes are centralized in `routes/api.php`.

```php
Route::apiResource('projects', ProjectController::class);
```

This keeps route registration simple and consistent. The controller is responsible for handling HTTP concerns, while the business logic lives in the service layer.

### 2) Controller

`ProjectController` is the HTTP entry point. It:

- receives the request
- validates it through `ProjectRequest`
- converts the request data into a `ProjectDto`
- calls the appropriate service method
- returns a JSON response or API resource payload

The controller is intentionally thin and does not contain complex business rules. It focuses on request flow and API response shaping.

### 3) Request validation

`ProjectRequest` extends Laravel's `FormRequest` and is responsible for:

- `authorize()` to allow access
- `rules()` to define validation rules
- method-aware validation for `create` vs `update`

This keeps validation logic separate from controller logic and ensures all incoming payloads are checked before reaching the service layer.

Example pattern:

```php
return match ($this->method()) {
    'PUT', 'PATCH' => $this->updateRules(),
    default => $this->createRules(),
};
```

### 4) DTO (Data Transfer Object)

`ProjectDto` is used to pass validated data into the service layer in a structured way.

Benefits:

- prevents raw request arrays from being passed around directly
- keeps the service contract typed and explicit
- allows centralized access to values like `name`, `description`, `start_date`, `end_date`, and `status`

This keeps the service layer clean and predictable.

### 5) Service layer

`ProjectService` contains the real business logic for project operations:

- `getAll()` for listing projects
- `create()` for creating a project
- `update()` for updating a project
- `delete()` for soft delete or hard delete

The service uses the model directly and is responsible for persistence logic. It does not handle HTTP or validation concerns.

Typical pattern:

```php
$project = $this->model->create([
    'name' => $dto->getName(),
    'description' => $dto->getDescription(),
    'start_date' => $dto->getStartDate(),
    'end_date' => $dto->getEndDate(),
    'status' => $dto->getStatus(),
]);
```

### 6) Error handling

Error handling is done in two layers:

- validation errors are handled automatically by Laravel FormRequest and returned as structured validation responses
- controller-level exceptions are caught with `try/catch` and logged using `Log::error()` before returning a JSON error response

Example:

```php
try {
    // service call
} catch (Throwable $exception) {
    Log::error($exception->getMessage());
    return response()->json(['error' => $exception->getMessage()], 500);
}
```

This ensures application errors are visible in logs and the client receives a clear JSON error payload.

### 7) API response formatter middleware

`HandleApiResponse` middleware wraps API responses using `ApiResponseFormatter`.

Its job is to standardize the JSON response structure for all API endpoints. The middleware:

- captures request start time
- runs the next request handler
- detects API responses
- formats the response payload consistently
- includes metadata such as method, endpoint, and duration

This is useful for keeping the API consistent across endpoints and reducing duplication in controllers.

```php
if ($this->isApi($request, $response)) {
    $statusCode = $response->getStatusCode();
    $data = $response->getData();
    return $api->make($data, $statusCode);
}
```

This is the example JSON response structure.

```json
{
    "success": true,
    "status": 200,
    "meta_key": {
        "method": "get",
        "endpoint": "api/projects/01a0d319-0cee-7365-8f6b-b73c2b517342",
        "duration": 0.89
    },
    "data": {
        "id": "01a0d319-0cee-7365-8f6b-b73c2b517342",
        "name": "aut sed mollitia aut ut",
        "description": "Voluptatem autem quibusdam recusandae . Non ...",
        "start_date": "2026-10-09",
        "end_date": "2027-02-04",
        "status": "planned",
        "created_at": "2026-09-24 11:07:09",
        "updated_at": "2026-09-24 11:07:09"
    }
}
```

### 8) Resource layer

The project also uses Laravel API Resources (`ProjectResource`) to shape the data returned to clients. This helps keep responses consistent and prevents exposing raw model data directly.

### Overall Coding Style

This overall style follows a clean separation of concerns:

- Routes: entry point
- Controller: HTTP orchestration
- Request: validation
- DTO: data transfer
- Service: business logic
- Middleware/Formatter: response normalization
- Resource: response shaping

## Useful commands

```bash
php artisan serve
php artisan migrate
php artisan migrate --seed
php artisan test
```
