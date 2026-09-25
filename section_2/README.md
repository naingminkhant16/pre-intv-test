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
