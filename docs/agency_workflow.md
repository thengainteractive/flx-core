# Agency Git Workflow

This document outlines the standard operating procedure for spinning up, developing, and maintaining multiple client projects using our Modular Monolith architecture.

## Architecture Philosophy
Our system is split into two layers:
1. **The Core Wrapper (`app/`)**: The shared global infrastructure, authentication, Filament core, and role management. (Maintained in the Upstream Repo)
2. **The Features (`Modules/`)**: Discrete, client-specific or shared business logic (e.g., `Venue`, `Blog`).

## 1. Setting Up a New Client Project

We never start from scratch. We clone the Core as our "Upstream".

### Step 1: Clone the Core
Clone the base Core repository to act as the skeleton for the client's app.
```bash
git clone git@github.com:your-agency/agency-cms-core.git client-project-name
cd client-project-name
```

### Step 2: Detach and Re-link Repositories
We push code to the client's private repository, but retain the ability to pull future Core updates.
```bash
# Rename the core remote to 'upstream'
git remote rename origin upstream

# Add the client's new private repository as 'origin'
git remote add origin git@github.com:client-name/their-private-repo.git
```

## 2. Working with Modules (Git Tracking)

The `Modules/` directory is where all business logic lives.
**Crucial Rule:** The Core repository's `.gitignore` ignores all modules by default. You must explicitly tell Git to track specific modules in the client's repository.

### Importing or Creating a Module
If you bring over an existing module or generate a new one (e.g., `CustomCrm`), open the root `.gitignore` file and add an exception so it tracks it:

```text
/Modules/*
!/Modules/.gitkeep
!/Modules/CustomCrm/
```
This guarantees that when you push to the client's repository, their custom modules are saved, but the core repository remains module-agnostic.

## 3. Deployment and Maintenance

### Initial Deployment
Push the entire project (Core + the client's explicitly tracked Modules) to their private repository.
```bash
git add .
git commit -m "Initial setup with Core and CustomCrm module"
git push -u origin main
```

### Updating the Core Later
When you fix a bug or add a global feature to the Core Wrapper (`agency-cms-core`), you can push that update to all client sites painlessly:

1. Navigate to the client's local project.
2. Pull the updates from the upstream core repository:
   ```bash
   git pull upstream main
   ```
3. Run migrations if necessary (`php artisan migrate`).
4. Commit and push to the client's origin to deploy the Core updates. Because of the `.gitignore` setup, this process cleanly merges the core updates without overwriting or interfering with their custom modules!
