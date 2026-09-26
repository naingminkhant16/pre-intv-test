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
