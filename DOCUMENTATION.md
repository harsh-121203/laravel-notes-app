# Note & Task Management System - Documentation

## 1. Introduction
This project is a modern, web-based productivity application built with Laravel 11/12 and vanilla JavaScript. It combines task management and note-taking into a single, cohesive interface. The application is designed to emulate the feel of a desktop application (like Notion or Linear) through the use of a Single Page Application (SPA) architecture, despite being built on a traditional multi-page MVC framework.

## 2. Architectural Paradigm
The application strictly follows the **Model-View-Controller (MVC)** architectural pattern, but extends it with **AJAX-driven DOM Replacement** to achieve SPA-like behavior.

### 2.1 Model (Data Layer)
- **Entities**: The core entities are `Task` and `Note`. 
- **Relationships**: Tasks can have hierarchical relationships (parent-child subtasks). A `Task` can also have a one-to-one relationship with a `Note` (for task-specific documentation).
- **Persistence**: Data is stored using an SQLite relational database, accessed via Laravel's Eloquent ORM, ensuring data abstraction and active record pattern implementation.

### 2.2 Controller (Business Logic Layer)
- **Routing**: `TaskController` and `NoteController` handle incoming HTTP requests.
- **Unified Dashboard**: Instead of separate pages for tasks and notes, a central `NoteController@index` (or Dashboard Controller) aggregates data from both models based on the selected date. This single payload is passed to the view, ensuring strong cohesion.
- **API Endpoints**: The controllers are designed to respond to both standard HTTP requests and `XMLHttpRequest` (AJAX) calls, returning JSON payloads for seamless background updates.

### 2.3 View (Presentation Layer)
- **Modular Blade Templates**: The user interface is decomposed into modular, reusable components using Laravel Blade (`partials/calendar.blade.php`, `partials/tasks/task-list.blade.php`, etc.). This promotes high cohesion and low coupling in the UI layer.
- **Three-Column Grid**: The visual architecture utilizes modern CSS Grid to create a responsive, three-column layout (Sidebar Calendar, Center Tasks, Right Notebook), maximizing screen real estate and cognitive flow.

## 3. Core Academic Concepts & Optimizations Implemented

### 3.1 SPA Navigation via DOM Replacement (Hydration)
Instead of relying on a heavy JavaScript framework (like React or Vue) to manage state, this application implements a custom **DOM Replacement Strategy**. 
- **The Flow**: When a user clicks a calendar date, JavaScript (`workspace.js`) intercepts the click, prevents the default browser reload, and sends an asynchronous `fetch` request.
- **Parsing**: The server returns the fully rendered HTML for the new state. The client-side script parses this HTML using `DOMParser`, extracts the `#workspaceRoot` container, and injects its `innerHTML` directly into the current DOM.
- **Result**: This creates an instantaneous state transition without the jarring visual flash of a full page reload, dramatically improving the user experience while maintaining the simplicity of server-side rendering.

### 3.2 Asynchronous Form Submissions (Optimistic UI updates)
All forms (creating tasks, checking off tasks, deleting notes) are intercepted by JavaScript.
- Data is serialized using `FormData` and sent to the controller via POST requests.
- Upon receiving a successful JSON response, the system triggers the `navigateWorkspace` function to silently refresh the DOM, guaranteeing that the UI perfectly mirrors the database state (Source of Truth) without full round-trips.

### 3.3 Dynamic CSS Variables & Theming
The application supports runtime theming. 
- Instead of hardcoding colors, the CSS relies on **CSS Custom Properties (Variables)**.
- A custom JavaScript function calculates theme contrasts (e.g., determining if the background is dark or light) and updates variables instantly via `document.documentElement.style.setProperty()`. 
- State is persisted in `localStorage` and applied via an inline `<script>` in the `<head>` of the document to prevent Flash of Unstyled Content (FOUC) during initial page load.

## 4. System Flow
1. **Bootstrapping**: The user accesses the root URL. Laravel routes the request to the dashboard controller.
2. **Hydration**: The controller determines the selected date (defaulting to today). It queries the database for tasks and notes matching that `created_at` or `task_date`.
3. **Rendering**: The Blade engine compiles the three-column layout and returns the HTML.
4. **Interaction**: The user interacts with the UI (e.g., clicks a new date). `workspace.js` fires an AJAX request, retrieves the new HTML, swaps the DOM, and updates the browser URL via the HTML5 History API (`history.pushState`), maintaining proper navigation flow.
