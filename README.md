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

# Section 2 - React Task Manager

A React task manager built with Vite.

## Features

- Add a new task with validation
- Mark tasks as complete or incomplete
- Delete tasks
- Filter tasks by status
- Clean UI styled with Bootstrap

## How to run

1. Install dependencies:

   ```bash
   npm install
   ```

2. Start the development server:

   ```bash
   npm run dev
   ```

3. Open the local URL shown in the terminal, usually:
   ```bash
   http://localhost:5173
   ```

## How it is implemented

This app uses React with Vite and a simple component-based structure.

### Main components

- `src/App.jsx` - Root component that renders the task manager in a centered layout.
- `src/components/TaskList.jsx` - Holds the main task state, filter state, and all task logic.
- `src/components/TaskInput.jsx` - Handles new task input and validation.
- `src/components/TaskItem.jsx` - Displays an individual task with a checkbox and delete button.

### State and behavior

- `TaskList` stores the tasks in React state.
- `handleAddTask` adds a new task to the top of the list.
- `toggleTask` switches the `completed` status.
- `deleteTask` removes a task from the list.
- The filter state controls whether tasks show as `all`, `active`, or `completed`.

### Styling

- Bootstrap is used for layout and controls.
- The app also includes custom styling in `src/App.css` and `src/index.css` for the overall look.

## Project structure

```bash
src/
  App.jsx
  App.css
  index.css
  main.jsx
  components/
    TaskInput.jsx
    TaskItem.jsx
    TaskList.jsx
```

# Section 3 - API Integrations

A simple React app built with Vite and Bootstrap to display the first 10 posts from JSONPlaceholder.

## Features

- Fetches posts from https://jsonplaceholder.typicode.com/posts
- Shows the first 10 post titles
- Displays a loading spinner while the request is in progress
- Shows an error message if the request fails or returns a non-OK status
- Highlights titles longer than 30 characters
- Filters the visible titles as the user types in the search box
- Clicking a title displays that post's body

## How it works

- `App.jsx` manages the main state:
  - `posts` stores the fetched posts
  - `selectedPostId` tracks the currently selected post
  - `searchTerm` stores the filter text
  - `loading` and `error` handle fetch UI states
- `useEffect` runs once on mount and fetches the posts from the API.
- `useMemo` filters the posts based on the current search term without making an extra request.
- `PostList.jsx` renders the filtered titles and highlights long ones.
- `PostDetail.jsx` shows the selected post body.
- Bootstrap is used for layout and styling.

## Run the project

1. Install dependencies:

   ```bash
   npm install
   ```

2. Start the development server:

   ```bash
   npm run dev
   ```

3. Open the local URL shown in the terminal, usually:
   ```bash
   http://localhost:5173/
   ```

# Section 4 - WordPress PHP code to register custom post type - Case Studies

This code is used to register a custom post type for case studies and adds a shortcode to display featured entries on the frontend.

## Features

### 1. Custom Post Type

The code includes to register a custom post type named `case_study` with the following settings:

- Label: Case Studies
- Publicly accessible: yes
- Available in the REST API: yes
- Supports: title, editor, and thumbnail
- Archive enabled: yes
- Archive slug: `case-studies`

This is created with `register_post_type('case_study', ...)` and hooked into `init`.

### 2. Shortcode

The plugin adds the shortcode:

```php
[featured_case_studies]
```

This shortcode runs a `WP_Query` for the `case_study` post type and displays up to 3 published case studies, ordered by date descending. Each result shows the title and excerpt inside a `featured-case-studies` container.

If no case studies are found, it returns:

```html
<p>No case studies found.</p>
```

### 3. Archive Title Filter

A filter is added to `the_title` to modify the archive title for case study pages:

```php
if (is_post_type_archive('case_study')) {
    $title = 'Case Study: ' . $title;
}
```

This prepends `Case Study:` to the archive page title when viewing the custom post type archive.

## Usage

1. Add new case studies from the WordPress admin.
2. Insert the shortcode `[featured_case_studies]` into any page or post.
3. Visit the archive page for the case studies to see the modified archive title.

## Explaination for fourth question

Plugin layouts should be overridden from the active theme rather than modifying the plugin directly, so plugin updates do not overwrite customizations. I would use the WordPress template hierarchy to create the most specific template supported by the plugin/theme, such as a custom template in the theme that takes precedence over the plugin's default layout.
