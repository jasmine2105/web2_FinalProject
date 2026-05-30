# PHP MVC Framework — Task Manager MVP

A lightweight MVC framework built from scratch in PHP 8.3, with a fully functional Task Manager application running on top of it.

---

## How to Run

**Requirements:** PHP 8.3+ and Composer. No MySQL, no XAMPP — the app uses SQLite which is built into PHP.

```bash
# 1. Install dependencies
composer install

# 2. Start the built-in PHP server
php -S localhost:8000 -t public
```

Open **http://localhost:8000** in your browser. The SQLite database and all tables are created automatically on first load.

To switch to MySQL: set `'default' => 'mysql'` in `config/database.php` and fill in your credentials.

---

## Project Structure

```
my-mvc-framework/
├── app/                        # Application layer (MVP)
│   ├── Controllers/
│   │   ├── ProjectController.php
│   │   └── TaskController.php
│   ├── Middleware/
│   │   └── TrimStrings.php
│   ├── Models/
│   │   ├── Project.php
│   │   └── Task.php
│   └── Views/
│       ├── errors/404.php
│       ├── projects/ (create, edit, index, show)
│       ├── tasks/    (create, edit, index)
│       └── layout.php
├── core/                       # Framework layer
│   ├── Application.php
│   ├── Container/
│   │   └── Container.php
│   ├── Database/
│   │   ├── Connection.php
│   │   ├── Model.php
│   │   └── QueryBuilder.php
│   ├── Http/
│   │   ├── Request.php
│   │   ├── Response.php
│   │   └── Router.php
│   └── View/
│       └── Engine.php
├── config/
│   ├── app.php
│   └── database.php
├── public/
│   └── index.php               # Front controller — only file with require
├── routes/
│   └── web.php
├── composer.json
└── README.md
```

---

## Route List

| Method | URI                   | Controller Action        | Description              |
|--------|-----------------------|--------------------------|--------------------------|
| GET    | /                     | ProjectController@index  | Home — projects list     |
| GET    | /projects             | ProjectController@index  | List all projects        |
| GET    | /projects/create      | ProjectController@create | New project form         |
| POST   | /projects             | ProjectController@store  | Save new project         |
| GET    | /projects/{id}        | ProjectController@show   | View project + its tasks |
| GET    | /projects/{id}/edit   | ProjectController@edit   | Edit project form        |
| POST   | /projects/{id}/update | ProjectController@update | Save project changes     |
| POST   | /projects/{id}/delete | ProjectController@delete | Delete project           |
| GET    | /tasks                | TaskController@index     | List all tasks           |
| GET    | /tasks/create         | TaskController@create    | New task form            |
| POST   | /tasks                | TaskController@store     | Save new task            |
| GET    | /tasks/{id}/edit      | TaskController@edit      | Edit task form           |
| POST   | /tasks/{id}/update    | TaskController@update    | Save task changes        |
| POST   | /tasks/{id}/delete    | TaskController@delete    | Delete task              |

**14 routes total** (minimum required: 5)

---

## MVP Application

**Task Manager** — manage projects and the tasks that belong to them.

- **Projects**: Create, view, edit, and delete projects (name, description, start date, end date).
- **Tasks**: Create, edit, and delete tasks (title, due date, status, assigned project).
- **Status values**: `pending`, `in_progress`, `completed`.
- **Validation**: Server-side validation on all forms with inline error messages and old-value preservation.
- **Middleware**: `TrimStrings` (`app/Middleware/TrimStrings.php`) trims whitespace from all POST fields before any controller sees the data.
- **Database**: SQLite via PDO — zero configuration, no server required.

---

## SOLID Design Justification

### S — Single Responsibility Principle

Every class has exactly one reason to change:

- `Core\Http\Router` (`core/Http/Router.php`) — registers and resolves routes only. It never dispatches controllers or renders views.
- `Core\Http\Request` (`core/Http/Request.php`) — wraps HTTP input superglobals only. It never touches the database or produces output.
- `Core\Http\Response` (`core/Http/Response.php`) — holds content, status code, and headers only. It never reads input or queries data.
- `Core\Database\QueryBuilder` (`core/Database/QueryBuilder.php`) — builds and executes SQL only. It never maps domain objects or renders output.
- `Core\View\Engine` (`core/View/Engine.php`) — renders PHP view templates only. It never fetches data or processes input.
- `App\Controllers\ProjectController` and `TaskController` — call models and pass data to views. They never write raw SQL or generate raw HTML strings.

### O — Open / Closed Principle

The framework is open for extension but closed for modification:

- `Core\Database\Model` (`core/Database/Model.php`) is an **abstract** base class. New model types (`Project`, `Task`) are added by extending it — `Model.php` itself is never modified. A future `User` model would extend it the same way.
- `Core\Http\Router` (`core/Http/Router.php`) — new routes are registered in `routes/web.php` without ever touching `Router.php`. The router does not need to know about routes in advance.
- `Core\Application` (`core/Application.php`) — new middleware is added via `$app->use(MiddlewareClass::class)` in `public/index.php` without modifying `Application.php`. The pipeline wraps itself automatically.

### L — Liskov Substitution Principle

`App\Models\Project` and `App\Models\Task` both extend `Core\Database\Model`. They are fully substitutable wherever a `Model` is expected:

- Both expose the same interface inherited from `Model`: `find()`, `all()`, `save()`, `update()`, `delete()`.
- Both add methods without removing or weakening any inherited contract.
- The DI container (`core/Container/Container.php`) resolves and injects either model into a controller constructor interchangeably — no calling code breaks regardless of which concrete model is resolved.

### I — Interface Segregation Principle

No class is forced to depend on methods it does not use:

- `Core\Database\Model` (`core/Database/Model.php`) exposes only the minimal CRUD surface: `find()`, `all()`, `save()`, `update()`, `delete()`. No unrelated utility methods are mixed in.
- `Core\Http\Router` (`core/Http/Router.php`) exposes only three public methods: `get()`, `post()`, and `resolve()`. HTTP verb registration is separate from resolution — consumers that only dispatch never see registration internals.
- The middleware contract used by `App\Middleware\TrimStrings` is a single method: `handle(Request $request, callable $next): Response`. Middleware classes are never forced to implement unrelated lifecycle hooks.

### D — Dependency Inversion Principle

High-level modules depend on abstractions, not on concrete instantiation:

- `Core\Application` (`core/Application.php`) never instantiates controllers, models, or services directly. It delegates all construction to `Core\Container\Container` — the container is the abstraction that decouples the application kernel from infrastructure details.
- `Core\Container\Container::resolve(string $abstract)` (`core/Container/Container.php`) accepts any class name string. Callers depend on the container interface, not on `new ClassName()` calls scattered throughout the code.
- Database infrastructure is injected as a **PDO singleton** bound in `public/index.php`. `Core\Database\Connection::create(string $driver, array $config)` (`core/Database/Connection.php`) accepts a driver name and config array — the application never hard-codes a database engine. Switching from SQLite to MySQL requires one config change (`config/database.php`), zero code changes.
- Controllers receive their dependencies (`Task`, `Project`, `Engine`) through constructor injection resolved by the container — they never call `new Task()` or `new Project()` themselves.

---

## Framework Design Decisions

- **Front controller**: All requests route through `public/index.php`, the only file with `require`.
- **PSR-4 autoloading**: `Core\` maps to `core/`, `App\` maps to `app/`. No manual `require` for class files anywhere except the entry point.
- **DI Container**: Reflection-based auto-wiring (`core/Container/Container.php`) resolves constructor dependencies recursively. Supports singletons and string-keyed bindings.
- **Middleware pipeline**: Middleware wraps the dispatch closure in a reverse chain. New middleware added with `$app->use()` in `public/index.php`.
- **View engine**: Templates are plain PHP files. `extract()` injects data; `ob_start()`/`ob_get_clean()` captures output (`core/View/Engine.php`).
- **SQLite by default**: No server required. `core/Database/Connection.php` supports both `sqlite` and `mysql` drivers via a `match` expression on the driver name from `config/database.php`.
