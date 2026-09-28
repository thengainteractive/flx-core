# Agency Workflow & Architecture Guide

This document outlines the standard operating procedure for spinning up, developing, and maintaining client projects using our Modular Monolith architecture.

## Architecture Philosophy

Our system is built on two distinct layers:
1. **The Core Wrapper (`app/`)**: Handles global infrastructure, authentication, user roles, and the base Filament Admin Panel setup.
2. **The Features (`Modules/`)**: Discrete, plug-and-play business logic (e.g., `Venue`, `Blog`, `Crm`). 

By separating these, we can rapidly deploy custom combinations of features to different clients while maintaining a single, updatable Core.

---

## 1. Setting Up a New Client Project

When a new client signs on, we do not start from scratch, nor do we copy-paste a ZIP file. We clone the Core as an "Upstream" repository.

### Step 1: Clone the Core
Clone the base Core repository to your local machine to act as the skeleton for the client's app.

```bash
# Clone the Core repo into a new client folder
git clone git@github.com:your-agency/agency-cms-core.git client-project-name
cd client-project-name
```

### Step 2: Detach and Re-link Repositories
We want to push code to the client's private repository, but retain the ability to pull future Core updates.

```bash
# Rename the core remote to 'upstream'
git remote rename origin upstream

# Add the client's new private repository as 'origin'
git remote add origin git@github.com:client-name/their-private-repo.git
```

### Step 3: Local Environment Setup
Set up the standard Laravel environment for the client.

```bash
cp .env.example .env
composer install
php artisan key:generate
```

Configure your database in the `.env` file, then run the Core migrations:

```bash
php artisan migrate
```

### Step 4: Create the First User
With the Core migrated, create the initial admin user to access the Filament dashboard.

```bash
php artisan flx:make-user
```
*(Follow the prompts to enter the name, email, and password for the admin).*

---

## 2. Working with Modules

The `Modules/` directory is where all client-specific and shared business logic lives. 
**Crucial Rule:** The Core repository's `.gitignore` ignores all modules. You must explicitly tell Git to track modules in the client's repository.

### Importing Existing Shared Modules
If the client paid for an existing feature (like the `Blog` or `Venue` module):

1. Copy the `Blog` folder from your module library into `Modules/Blog/`.
2. Open the root `.gitignore` file and add an exception so it tracks this specific module:
   ```text
   /Modules/*
   !/Modules/.gitkeep
   !/Modules/Blog/
   ```
3. Run the module's migrations:
   ```bash
   php artisan flx:module-migrate Blog
   ```
4. The Filament `AdminPanelProvider` will automatically discover the module and add it to the dashboard.

### Creating New Custom Modules
If the client needs a brand new, highly custom feature (e.g., `CustomCrm`):

1. Generate the module using the standard command:
   ```bash
   php artisan flx:make-module CustomCrm
   ```
2. Update `.gitignore` to track it:
   ```text
   !/Modules/CustomCrm/
   ```
3. Build out your Models, Filament Resources, and Migrations directly inside `Modules/CustomCrm/`. 
4. Ensure your new module registers its Filament resources by creating a Plugin class (e.g., `CustomCrmPlugin`) and referencing it in `Modules/CustomCrm/module.json`.

---

## 3. Deployment and Maintenance

### Initial Deployment
Push the entire project (Core + the client's specific Modules) to their private repository.

```bash
git add .
git commit -m "Initial setup with Core and Blog module"
git push -u origin main
```

### Updating the Core Later
When you fix a bug or add a global feature to the Core Wrapper (`agency-cms-core`):

1. Navigate to the client's local project.
2. Pull the updates from the upstream repository:
   ```bash
   git pull upstream main
   ```
3. Run migrations if necessary (`php artisan migrate`).
4. Commit and push to the client's origin to deploy the Core updates without affecting their custom modules.
