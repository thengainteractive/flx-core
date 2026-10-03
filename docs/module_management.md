# Module Management

FLX is built entirely around an isolated, plug-and-play module architecture (`nwidart/laravel-modules` combined with Filament Plugins). This allows you to manage massive enterprise features as independent packages.

## Managing Existing Modules

### From the Admin Dashboard UI
FLX ships with a visual module manager to toggle features without touching code.
1. Log into `/admin`.
2. In the navigation sidebar, click **Settings -> Modules**.
3. You will see a list of installed modules with status badges.
4. Click **Enable / Disable** to toggle a feature.
   - Disabling a module hides its navigation, pages, and routes instantly.
   - **Data Safety**: Disabling a module *does not* delete its database records.
5. Click **Migrate** to run any fresh database schema updates for that specific module.

### From the Command Line (CLI)
You can optionally manage modules using our custom CLI commands:
```bash
# List all installed modules & statuses
php artisan flx:module-list

# Enable/Disable a module
php artisan flx:module-enable Blog
php artisan flx:module-disable Blog

# Run migrations for all enabled modules
php artisan flx:module-migrate

# Run migrations for a specific module
php artisan flx:module-migrate Catalog --force

# Rollback migrations for a module (drops tables)
php artisan flx:module-migrate-rollback Blog
```

## Creating a New Custom Module

When building new features, always isolate them into a Module rather than cluttering the Core `app/` directory.

### Step 1: Scaffold the Module
Use the generator command to instantly create the scaffolding:
```bash
php artisan flx:make-module Invoices --description="Client billing and invoices module"
```
This generates:
- `Modules/Invoices/module.json` (configured for Filament)
- `Modules/Invoices/app/InvoicesPlugin.php` (Filament plugin ready)
- Skeletons for Routes, Views, and config.

### Step 2: Create Models & Migrations
```bash
php artisan module:make-model Invoice Invoices -m
```
Edit your migration file in `Modules/Invoices/database/migrations/` and run:
```bash
php artisan module:migrate Invoices --force
```

### Step 3: Seed Module Permissions
If your module requires access control, seed its permissions inside `Modules/Invoices/database/seeders/InvoicesDatabaseSeeder.php` so administrators can build custom roles. (See `roles_and_permissions.md` for details).
```bash
php artisan module:seed Invoices
```

### Step 4: Create Filament Resources
Inside `Modules/Invoices/app/Filament/Resources/`:
- Create your Filament resources, forms, tables, and pages.
- Ensure your `InvoicesPlugin.php` registers these resources via the `$panel->resources([...])` method.

Your resource will **automatically appear in Filament** whenever the module is active.

## Sharing Modules Across Projects
Every module inside the `Modules/` directory is 100% self-contained. 
To share a feature between Client A and Client B:
1. Copy the `Modules/FeatureName` folder from Project A.
2. Paste it into Project B's `Modules/` directory.
3. Go to Project B's Admin Dashboard and click **Enable**.
4. The admin panel instantly registers the navigation items, forms, tables, and routes.

## Deploying a Module to a Remote Server

When deploying a new or updated module to a remote server (e.g., staging or production), follow this generic checklist to ensure the module is fully registered, its database schema is updated, and its permissions are visible in the system.

Replace `<ModuleName>` with your actual module's name (e.g., `Venue`, `Blog`, `Catalog`).

1. **Pull Your Code**
   Ensure your latest code, including the `Modules/<ModuleName>` directory, is pulled to the server.

2. **Dump Autoloads / Install Dependencies**
   Since the core uses `wikimedia/composer-merge-plugin`, the server's autoloader needs to discover the new module's files (like Seeders and Policies):
   ```bash
   composer dump-autoload
   # Or: composer install --no-dev --optimize-autoloader
   ```

3. **Enable the Module**
   Sometimes the `modules_statuses.json` file is ignored in version control, so the server might not know the module is active:
   ```bash
   php artisan module:enable <ModuleName>
   ```

4. **Run Migrations**
   Execute the module's migrations to create or update its database tables:
   ```bash
   php artisan module:migrate <ModuleName> --force
   ```
   *(Note: The `--force` flag is required if your server environment is set to production).*

5. **Seed Permissions and Data**
   If the module has its own permissions or required default data, seed them:
   ```bash
   php artisan module:seed <ModuleName> --force
   ```
   *(Alternatively, if `module:seed` fails to locate the seeder due to caching issues, you can explicitly call the seeder class: `php artisan db:seed --class="Modules\<ModuleName>\Database\Seeders\<ModuleName>DatabaseSeeder" --force`)*

6. **Clear Caches**
   Crucially, you must clear the application caches so the frontend (Filament) and authorization packages (Spatie) pick up the new database rows and routes.
   ```bash
   php artisan permission:cache-reset
   php artisan optimize:clear
   ```
