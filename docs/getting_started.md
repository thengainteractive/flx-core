# Getting Started & Installation

This guide covers how to set up the FLX Modular Platform on your local machine or server. FLX uses a lean core featuring authentication and user management, allowing you to selectively enable features via modules.

## Prerequisites
- **PHP**: 8.2 or higher
- **Composer**: 2.x
- **Database**: SQLite, MySQL, or PostgreSQL
- **Node/NPM**: For building frontend assets (Vite)

## Step-by-Step Installation

### 1. Clone & Install Dependencies
First, clone the repository and install the PHP dependencies:
```bash
git clone <repository-url> flx-project
cd flx-project

composer install
```

### 2. Environment Configuration
Copy the example environment file and generate your application key:
```bash
cp .env.example .env
php artisan key:generate
```
Open your `.env` file and configure your `DB_*` database credentials.

### 3. Run Core Migrations
Migrate the core application tables (Users, Roles, Permissions, etc.):
```bash
php artisan migrate
```

### 4. Create an Admin User
Because FLX handles authorization strictly via the database, you must create an initial admin user to access the backend.
*(If you are setting this up manually, you can use the tinker command or our custom command if available. However, our Roles & Permissions seeder handles this automatically if seeded.)*

**Run the Core Seeder**:
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```
*This will create the `Super Admin` role and attach it to your first registered user, or you can register one manually and attach the role.*

### 5. Run Module Migrations (If any)
If your project comes pre-packaged with modules in the `Modules/` directory, ensure their databases are migrated:
```bash
php artisan flx:module-migrate --force
```

### 6. Compile Assets & Serve
Install your NPM dependencies, compile assets, and boot the server:
```bash
npm install
npm run dev

php artisan serve
```

## Accessing the Dashboard
You can now access the Filament Admin panel by visiting:
- **URL**: `http://127.0.0.1:8000/admin`
- **Login**: Use the credentials of the admin user you just created.

From here, you can navigate to the **User Management** section to configure roles, or **Settings -> Modules** to visually manage your enabled features.
