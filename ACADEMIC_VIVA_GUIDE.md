# 🎓 Academic Notes & Todo Platform — Viva & Presentation Guide

## 1. Project Overview & Objective
This project is an academic **Unified Productivity Workspace** featuring a split view:
- **Left Side**: Hierarchical **Tasks & Nested Subtasks** (with live completion counters, quick-add input inspired by the clean Timesheet/Todo UI, and recursive child tasks).
- **Right Side**: Digital **Notebooks** for lecture notes, revisions, and study cards with soft pastel color coding.
- **Central Theme**: Modern Forest Emerald (`#1b4332`), Slate, and Cream design — styled with pure vanilla CSS (zero external npm/Vite dependency).
- **Zero Database Hassle**: Built with file-based **SQLite** (`database/database.sqlite`), allowing instant demonstration without MySQL or phpMyAdmin.

---

## 2. Model - View - Controller (MVC) Architecture

Examiner Question: *"Explain the MVC flow and relationships in this project."*

```
                       HTTP Request (Browser)
                                 │
                                 ▼
                       routes/web.php
                                 │
                 ┌───────────────┴───────────────┐
                 ▼                               ▼
       TaskController.php               NoteController.php
         (Tasks & Subtasks)             (Notebooks & Board)
                 │                               │
                 ▼                               ▼
          Task.php (Model)                Note.php (Model)
                 │                               │
                 └───────────────┬───────────────┘
                                 ▼
                     database/database.sqlite
                                 │
                                 ▼
                   resources/views/dashboard.blade.php
                                 │
                                 ▼
                       Rendered Clean Webpage
```

### Database Models & Self-Referencing Relationship
In `app/Models/Task.php`, we implemented a **Self-Referential Eloquent Relationship** for subtasks:
```php
// Parent task
public function parent() {
    return $this->belongsTo(Task::class, 'parent_id');
}

// Subtasks
public function subtasks() {
    return $this->hasMany(Task::class, 'parent_id')->latest();
}
```
**Viva Explanation**: `parent_id` is a foreign key on the `tasks` table referencing `id` of the same table (`tasks`). Top-level tasks have `parent_id = NULL`, while subtasks store the ID of their parent task. When deleting a parent task, foreign key cascade deletes the subtasks automatically (`cascadeOnDelete()`).

---

## 3. Route Map & Controllers

| Method | URI | Controller Action | Purpose |
|---|---|---|---|
| `GET` | `/` | `NoteController@index` | Loads the central split workspace |
| `POST` | `/tasks` | `TaskController@store` | Stores a new task or subtask (via `parent_id`) |
| `PATCH`| `/tasks/{task}/toggle` | `TaskController@toggle` | Marks task or subtask as done/pending |
| `DELETE`| `/tasks/{task}` | `TaskController@destroy`| Deletes task (and cascading subtasks) |
| `POST` | `/notes` | `NoteController@store` | Saves a new notebook card |
| `DELETE`| `/notes/{note}` | `NoteController@destroy`| Deletes a notebook card |

---

## 4. Key Academic Questions for Viva

### Q1: How did you solve the N+1 Query Problem with Subtasks?
> **Answer**: In `NoteController@index`, we use **Eager Loading**:
> ```php
> Task::whereNull('parent_id')->with('subtasks')->get();
> ```
> Without `with('subtasks')`, Laravel would execute 1 SQL query for tasks + 1 separate SQL query for *each* task's subtasks (N+1 queries). With eager loading, Laravel fetches everything in just 2 optimized queries using `WHERE parent_id IN (...)`.

### Q2: What security measures are implemented?
> 1. **CSRF Tokens (`@csrf`)**: Every state-changing form (POST, PATCH, DELETE) includes an encrypted CSRF session token to defend against Cross-Site Request Forgery.
> 2. **Mass Assignment Protection**: Models use `$fillable` arrays to whitelist fields that users can update, blocking malicious request tampering.
> 3. **Input Validation**: Controllers use `$request->validate()` to ensure required fields, strings, and lengths are safe before hitting the database.

### Q3: Why use SQLite for an academic project?
> **Answer**: SQLite is a self-contained, serverless database engine stored in a single file (`database.sqlite`). It avoids dependency on external services like Apache/MySQL, makes the repository completely portable, and behaves identically with Laravel's Eloquent ORM.

---

## 5. Live Demonstration Commands
1. Start the server:
   ```bash
   php artisan serve
   ```
2. Open in browser:
   ```text
   http://127.0.0.1:8000
   ```
3. To re-seed fresh sample data anytime:
   ```bash
   php artisan migrate:fresh --seed
   ```
