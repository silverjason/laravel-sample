# Modular Laravel Sample

This repository demonstrates a modular Laravel approach where each domain lives in `app/Modules`.

The main showcase is the `Projects` module, which includes:
- API routing and module service provider bootstrapping
- form requests with policy-based authorization
- enum-backed model state
- a domain action outside CRUD (`CompleteProject`)
- a module-local factory
- an async job and CLI command example
- feature tests under `tests/Modules/Projects`

`Tasks` is included as a smaller companion module to show the same pattern across a related domain.

## Module Layout

- `app/Modules/Projects`
- `app/Modules/Tasks`

Each module owns its own:
- `Actions`
- `Enums`
- `Http`
- `Models`
- `Policies`
- `database`
- `routes`

## Notes

- Authentication is applied at the module route level.
- Module policies are discovered by Laravel's default policy guessing.
- Factories are kept inside the module and wired with explicit `newFactory()` methods.
- Tests live under `tests/Modules`.

## Running

```bash
php artisan test
```

If you want to explore the sample, start with:
- `app/Modules/Projects/Http/Controllers/ProjectController.php`
- `app/Modules/Projects/Actions/CompleteProject.php`
- `app/Modules/Projects/Models/Project.php`
- `app/Modules/Tasks/Http/Controllers/TaskController.php`
