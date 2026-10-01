# AI Agent Guidelines (Project Architecture)

This document provides context and rules for AI coding agents working on this project. Read this before modifying the codebase or generating new features.

## 🏗 Stack & Architecture
- **Framework**: Laravel 12
- **Admin Panel**: Filament 5
- **Architecture Pattern**: Modular Monolith
- **Module Manager**: `nwidart/laravel-modules` (v12)

## 📁 Directory Structure
The application code is split between the Core and Domain Modules:
- `app/`: Contains strictly Core infrastructure (e.g., base Providers, `ModuleRegistry`, global Middleware, shared base Models).
- `Modules/`: Contains isolated domain-specific logic (e.g., `Blog`, `Catalog`, `Page`). Each module operates like a mini-Laravel application.

## ⚙️ Core Mechanisms & Rules

### 1. Dependency Management & Autoloading
We use `wikimedia/composer-merge-plugin`. This allows Composer to dynamically merge `composer.json` files from all modules into the root context during installation and autoload dumping.
- **Rule**: DO NOT add module-specific dependencies or PSR-4 autoload rules to the root `composer.json`.
- **Rule**: Put module dependencies and autoloading definitions inside `Modules/<ModuleName>/composer.json`.

### 2. Filament Integration
The core `AdminPanelProvider` (`app/Providers/Filament/AdminPanelProvider.php`) is designed to be agnostic of the modules. It dynamically discovers and registers Filament Plugins for any enabled module.
- **Rule**: DO NOT hardcode module resources or pages in `AdminPanelProvider`.
- **Rule**: When adding Filament resources to a module, create a Plugin class in that module (e.g., `Modules\Blog\BlogPlugin`).
- **Rule**: Register the plugin by referencing it in the module's `module.json` file:
  ```json
  "filament": {
      "plugin": "Modules\\Blog\\BlogPlugin"
  }
  ```

### 3. Module Status & Registry
- Module states (enabled/disabled) are tracked by `nwidart/laravel-modules`, commonly via `modules_statuses.json`.
- A custom wrapper exists at `app/Core/ModuleRegistry.php` to fetch metadata, toggle states, and check if specific modules are enabled. Use this registry when cross-checking module activation from the core.

### 4. Roles & Permissions (Authorization)
- We use a **Permission-First Approach** using `spatie/laravel-permission`.
- Do not hardcode specific role names in modules (unless checking for `Super Admin`).
- Modules must seed their own granular permissions.
- See `docs/roles_and_permissions.md` for complete implementation details and workflows.

### 5. Development Workflow
- When asked to create a new "feature", evaluate whether it belongs in an existing Module, requires a new Module, or is a Core utility. Err on the side of encapsulating business logic inside Modules.
- Use `php artisan module:make <Name>` to scaffold new modules.
- Ensure that the module's Plugin class implements `Filament\Contracts\Plugin` and uses the `$panel->resources([...])` method to register its own resources.
