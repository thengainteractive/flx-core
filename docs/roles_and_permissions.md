# Roles & Permissions (Authorization)

This document outlines how User Roles and Permissions are handled within the application, specifically detailing the integration between the Core system and domain Modules.

## Architecture & Stack
- **Package**: `spatie/laravel-permission`
- **Management UI**: Filament Admin Panel
- **Strategy**: **Permission-First Approach** (Loose Coupling)

---

## Core Concepts

### 1. Permissions are Static (Code/Seeders)
Permissions represent specific actions (e.g., `create_venue`, `edit_venue_location`).
- **Rule**: Permissions should be hardcoded and seeded into the database by the module that owns them.
- **Rule**: Modules should *never* check for a specific role name (e.g., `if user is venue_manager`). They must always check for permissions (e.g., `if user can edit_venue`).

### 2. Roles are Dynamic (Dashboard)
Roles are simply collections of Permissions (e.g., `Venue Manager`, `CRM Operator`).
- **Rule**: Custom roles are created and managed by administrators directly in the Filament Dashboard.
- **Rule**: You should not hardcode specific business roles in the codebase unless they are strictly system-level roles (see below).

---

## System Defaults

The core application seeds two default roles upon installation:
1. `Super Admin`
2. `User`

These roles are protected by an `is_system` boolean column in the `roles` table. 
- **Protection**: The `RolePolicy` explicitly prevents system roles from being renamed or deleted through the Filament UI.
- **Super Admin Privileges**: By default, `Super Admin` bypasses most granular checks and has explicit rights to manage other users, roles, and core module activation.

---

## Creating a New Module with Permissions

When scaffolding a new module that requires access control, follow this workflow:

### Step 1: Define & Seed Permissions
Open the module's database seeder (e.g., `Modules/Venue/database/seeders/VenueDatabaseSeeder.php`) and register your granular permissions.

```php
use Spatie\Permission\Models\Permission;

public function run(): void
{
    // Flush the cache to prevent errors
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    $permissions = [
        'view_venue', 'create_venue', 'edit_venue', 'delete_venue'
    ];

    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }
}
```
*Run `php artisan module:seed <ModuleName>` to push these to the database.*

### Step 2: Enforce via Policies
Create standard Laravel Policies within the module (e.g., `Modules/Venue/app/Policies/VenuePolicy.php`). Filament will automatically discover these policies based on the model name.

```php
namespace Modules\Venue\Policies;

use App\Models\User;
use Modules\Venue\Models\Venue;

class VenuePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasPermissionTo('view_venue');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasPermissionTo('create_venue');
    }
    
    // ... update, delete, etc.
}
```

### Step 3: Assign via Dashboard
Once seeded, your new permissions will automatically appear as checkboxes in the **Roles** resource inside the Filament Admin panel.
1. The Super Admin creates a new Role (e.g., "Venue Manager").
2. The Super Admin checks the boxes for `view_venue` and `create_venue`.
3. The Super Admin assigns the "Venue Manager" role to a User.
4. Filament automatically reads the policy and grants the User access to the Venue pages and actions.
