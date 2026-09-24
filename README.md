# 📝 Notes & Todo Academic Platform (Laravel)

A clean, reliable, and human-friendly Notes and Task management platform crafted specifically for academic demonstration, exams, and viva presentations.

---

## 🚀 Quick Start (Running the Application)

1. Make sure you are inside the project folder:
   ```bash
   cd notes-app
   ```
2. Start the Laravel development server:
   ```bash
   php artisan serve
   ```
3. Open your web browser and navigate to:
   ```text
   http://127.0.0.1:8000
   ```

---

## 💡 What Makes This Academic-Friendly?

1. **Pure MVC Architecture**:
   - **Model**: `app/Models/Note.php` with `$fillable` mass-assignment security and boolean type casting.
   - **Views**: Clean Blade templates in `resources/views/` (`layouts/app.blade.php`, `notes/index.blade.php`, `notes/create.blade.php`, `notes/edit.blade.php`).
   - **Controller**: `app/Http/Controllers/NoteController.php` with standard RESTful methods (`index`, `create`, `store`, `edit`, `update`, `destroy`, and `toggleComplete`).

2. **Zero Third-party Bloat**:
   - Pure CSS and responsive design.
   - No complex npm/Vite build steps needed at demo time.
   - Built-in SQLite database (`database/database.sqlite`), requiring zero MySQL/XAMPP setup.

3. **Core Academic Features**:
   - **Full CRUD**: Create, Read, Update, and Delete notes.
   - **Task Completion Toggle**: Mark items as Pending or Done with dynamic stats.
   - **Search & Filter**: Search by keyword in title/content and filter by status (All, Pending, Completed).
   - **Security**: Form validation with inline feedback, CSRF token protection (`@csrf`), and HTTP method spoofing (`@method('PUT')`, `@method('DELETE')`).

---

## 📚 Viva & Presentation Preparation
We have prepared a dedicated guide with questions, architecture diagrams, and ready-to-answer explanations:
👉 **[Read the Full Viva & Presentation Guide](ACADEMIC_VIVA_GUIDE.md)**
