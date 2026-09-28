# FLX - Modular Laravel & Filament Admin Platform

> A reusable, modular application foundation built on **Laravel 12** and **Filament 5**.  
> Start any client project with a clean core (Authentication + User management), and effortlessly plug-and-play feature modules like Blog, Product Catalog, Menus, Invoicing, or custom business tools without touching the core code.

---

## 🌟 Key Highlights

- **Lean Core**: Ships with only authentication, users, and the module engine. Zero clutter for apps that don't need it.
- **Visual Module Manager**: Enable or disable modules directly inside the Filament Admin Dashboard with real-time feedback.
- **Safe & Non-Destructive**: Disabling a module removes its navigation, pages, and routes from the panel, but **preserves all database records**.
- **Self-Contained Modules**: Each module carries its own:
  - Database Migrations & Seeders
  - Eloquent Models & Policies
  - Filament Resources, Pages, Widgets & Actions
  - Web & API Routes and Controllers
  - Blade views & configuration
- **Instant Module Generator**: Scaffold production-ready modules with one command:
  ```bash
  php artisan flx:make-module {Name}
  ```
- **Zero-Config Autoloading**: Drop any module into `Modules/` and its classes are immediately recognized by the system.

---

## 🏗️ Architecture Overview

```text
flx/
├── app/
│   ├── Filament/
│   │   ├── Pages/
│   │   │   └── ManageModules.php     # Visual module toggle screen
│   │   └── Resources/
│   │       └── Users/                # Core User resource
│   └── Providers/
│       ├── AppServiceProvider.php    # Universal module autoloader
│       └── Filament/
│           └── AdminPanelProvider.php # Dynamic module plugin registry
├── modules_statuses.json             # Key-value state (Blog: true, Catalog: false)
└── Modules/                          # Modular feature packages
    ├── Blog/
    │   ├── app/
    │   │   ├── BlogPlugin.php        # Filament Plugin contract
    │   │   ├── Filament/
    │   │   │   └── Resources/        # Posts & Categories resources
    │   │   ├── Models/
    │   │   └── Providers/
    │   ├── database/migrations/
    │   ├── routes/ (web.php, api.php)
    │   └── module.json
    └── Catalog/
        ├── app/
        │   ├── CatalogPlugin.php
        │   ├── Filament/Resources/   # Products resource
        │   └── Models/
        ├── database/migrations/
        └── module.json
```

---

## 🚀 Quick Start Guide

### 1. Requirements
- PHP 8.2+
- Composer 2.x
- SQLite, MySQL, or PostgreSQL

### 2. Setup Application
```bash
# Clone or enter directory
cd flx

# Install dependencies
composer install

# Environment setup
cp .env.example .env
php artisan key:generate

# Run core migrations
php artisan migrate

# Create your admin user
php artisan flx:make-user --name="Admin User" --email="admin@example.com" --password="password"

# Run module migrations
php artisan flx:module-migrate --force

# Start development server
php artisan serve
```

### 3. Access Admin Panel
- **URL**: `http://127.0.0.1:8000/admin`
- **Email**: `admin@example.com`
- **Password**: `password`

---

## 🎛️ Managing Modules

### From the Admin Dashboard UI
1. Log into `/admin`.
2. In the navigation sidebar, click **Settings -> Modules**.
3. You will see a list of installed modules with status badges.
4. Click **Enable / Disable** to toggle a feature.
   - When enabling, pending migrations run automatically.
5. Click **Migrate** to run any fresh database schema updates for that specific module.

### From the Command Line (CLI)

```bash
# List all installed modules & statuses
php artisan flx:module-list

# Enable a module
php artisan flx:module-enable Blog

# Disable a module
php artisan flx:module-disable Blog

# Run migrations for all enabled modules
php artisan flx:module-migrate

# Run migrations for a specific module
php artisan flx:module-migrate Catalog --force

# Rollback migrations for a module (drops tables)
php artisan flx:module-migrate-rollback Blog
```

---

## 🛠️ Creating a New Module

To build a new feature (e.g. `Invoices`, `Menu`, `Portfolio`):

### Step 1: Scaffold the Module
```bash
php artisan flx:make-module Invoices --description="Client billing and invoices module"
```

This generates:
- `Modules/Invoices/module.json` (configured for Filament)
- `Modules/Invoices/app/InvoicesPlugin.php` (Filament plugin ready)
- `Modules/Invoices/app/Filament/Resources/`
- Web & API route skeletons

### Step 2: Create Model & Migration
```bash
php artisan module:make-model Invoice Invoices -m
```

Edit your migration file in `Modules/Invoices/database/migrations/` and run:
```bash
php artisan module:migrate Invoices --force
```

### Step 3: Create Filament Resource
Inside `Modules/Invoices/app/Filament/Resources/Invoices/`:
- Create `InvoiceResource.php`
- Create `InvoiceForm.php` & `InvoicesTable.php`
- Create page classes (`ListInvoices`, `CreateInvoice`, `EditInvoice`)

Your resource will **automatically appear in Filament** whenever the module is active.

---

## 📦 Sharing & Moving Modules Across Projects

Each module inside `Modules/{Name}` is 100% self-contained:
1. **Copy & Paste**: Simply copy the `Modules/{Name}` folder from Project A into Project B's `Modules/` directory.
2. **Enable & Migrate**:
   - In Dashboard: Click **Enable** on the new module.
   - Or CLI: `php artisan flx:module-enable {Name} && php artisan flx:module-migrate {Name} --force`
3. That's it! The admin panel in Project B will instantly register the navigation items, forms, tables, and routes.

---

## 🛡️ Database & Data Safety FAQ

> **Q: Will my database tables or records be deleted if I disable a module?**  
> **A: No.** Disabling a module only deactivates its UI screens, routes, and background listeners. The database tables and all user data stay completely untouched.

> **Q: How do I completely erase a module's data?**  
> **A:** If you explicitly want to drop a module's database tables, run:  
> `php artisan flx:module-migrate-rollback {ModuleName}`

---

## 📄 License
Open-source software licensed under the [MIT license](LICENSE).
