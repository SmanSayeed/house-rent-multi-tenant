# Multi-Tenant Flat & Bill Management System: Detailed SRS, Feature Planning, and Database Planning

This document provides a comprehensive Software Requirements Specification (SRS), feature planning, and database planning for the Multi-Tenant Flat & Bill Management System. The system is designed using a **column-based multi-tenant approach** in a single database, with data isolation enforced via a `tenant_id` column in relevant tables. This ensures that data for different house owners (tenants in the multi-tenant sense) remains segregated through query scopes and middleware.

The system will be developed using **Laravel 12** (assuming forward compatibility from Laravel 11 if not yet released; adjust as needed). Key required packages include:
- **Laravel Sanctum**: For API authentication if exposing endpoints (optional but recommended for future-proofing; use `composer require laravel/sanctum` and publish config/migration).
- **Laravel UI** or **Breeze/Jetstream**: For scaffolding authentication and basic UI (e.g., `composer require laravel/breeze --dev` for Blade + Tailwind).
- **Tailwind CSS**: For minimal UI styling (install via npm: `npm install -D tailwindcss postcss autoprefixer` and configure).
- **Laravel Mail**: Built-in, but for testing, use Mailtrap or similar; queue emails with Horizon or built-in queue driver (e.g., `composer require laravel/horizon` if needed).
- **Other dependencies**: Composer packages like `doctrine/dbal` for schema changes if required, and `barryvdh/laravel-debugbar` for development debugging.

No additional AI tools are assumed; this is a manual implementation. Focus on clean, maintainable code with optimized queries and email notifications.

## Software Requirements Specification (SRS)

### 1. Introduction
- **Purpose**: This system manages flats, tenants, and bills in a multi-tenant environment where house owners manage their buildings independently, admins oversee everything, and tenants have read-only access. It supports bill creation, dues tracking, payments, and email notifications.
- **Scope**: Web-based application with role-based dashboards. Multi-tenancy via column isolation (no separate databases or subdomains). Includes CRUD operations, data isolation, and notifications.
- **Definitions**:
  - **Tenant (Multi-Tenancy)**: Refers to house owners as isolated "tenants" in the system.
  - **Tenant (User Role)**: End-users renting flats.
  - **Dues**: Accumulated unpaid amounts from previous bills.
- **References**: Laravel 12 documentation, MySQL/PostgreSQL docs.
- **Overview**: System handles user roles, flat/bill management, and ensures data security via isolation.

### 2. Overall Description
- **Product Perspective**: A simple, scalable management tool for apartment buildings, replacing manual spreadsheets with automated dues and notifications.
- **Product Functions**:
  - User authentication and role-based access.
  - Admin: Manage house owners, tenants, assignments.
  - House Owner: Manage flats, bill categories, bills (with dues), payments.
  - Tenant: View bills (read-only).
  - Email notifications for bill events.
- **User Classes**:
  - Admin: Full access.
  - House Owner: Isolated to their building.
  - Tenant: Limited to assigned flats.
- **Operating Environment**: PHP 8.2+, Laravel 12, MySQL 8+/PostgreSQL, Apache/Nginx, Browser (Chrome/Firefox).
- **Design Constraints**: Use Eloquent ORM, Blade templates, RESTful controllers. No JavaScript frameworks unless needed (e.g., Alpine.js for interactivity).
- **Assumptions**: Users have email access; no mobile app (web-responsive via Tailwind).

### 3. Specific Requirements
- **External Interface Requirements**:
  - UI: Forms, tables, dashboards using Blade + Tailwind.
  - Emails: SMTP integration (e.g., via .env).
  - API: Optional via Sanctum for future mobile integration.
- **Functional Requirements**:
  - See Feature Planning section below for detailed flows.
- **Non-Functional Requirements**:
  - **Performance**: Queries optimized with indexes; pagination for lists (e.g., 10-20 items/page).
  - **Security**: Data isolation via scopes; hashed passwords; CSRF protection.
  - **Reliability**: Queued emails/jobs to avoid failures.
  - **Scalability**: Single DB handles up to 1000s of records; cache for frequent reads.
  - **Usability**: Intuitive forms with validation; error messages.
- **Database Requirements**: See Database Planning section.
- **Other Requirements**: Logging via Laravel Log; backups via Spatie package if extended.

## Feature Planning

### 1. Overall System Architecture
- **Multi-Tenant Implementation (Column-Based)**:
  - Add `tenant_id` (linked to house owner's user ID) in tables for isolation.
  - **Middleware**: Create `TenantMiddleware` in Laravel to set tenant context based on auth user (e.g., `app/Http/Middleware/TenantMiddleware.php`).
  - **Global Scopes**: In models, use `boot` method: `static::addGlobalScope('tenant', function ($query) { $query->where('tenant_id', auth()->user()->tenant_id ?? null); });`. Admins can bypass via `withoutGlobalScope`.
  - **Authentication**: Laravel Auth with web guard; Sanctum for API tokens if needed (`php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`).
  - **Why Column-Based?**: Easy setup, no subdomain routing issues in dev.
- **Modules Overview**:
  - Authentication: Register (admin-only), login, password reset.
  - Dashboards: Role-specific (e.g., AdminDashboard Livewire component).
  - Notifications: Emails via Laravel Mailer (queueable: implement `ShouldQueue`).
  - UI: Blade views with Tailwind; use Livewire for dynamic elements if desired (`composer require livewire/livewire`).
- **Performance & Optimization**:
  - Eager loading: e.g., `Bill::with(['flat', 'category'])->get()`.
  - Caching: Use `Cache::remember` for bill categories.
  - Services: e.g., `app/Services/BillService.php` for dues logic.
  - Queues: `php artisan queue:work` for emails.
- **Documentation (README.md)**:
  - Installation: `composer create-project laravel/laravel .`, add packages, `.env` config, `php artisan migrate --seed`.
  - Multi-Tenancy: "Column-based isolation with global scopes."
  - Testing: `php artisan test`.

### 2. User Roles & Permissions Features
- **Admin Features**:
  - Manage House Owners: CRUD forms; auto-create building on store.
  - Manage Tenants: CRUD; soft deletes.
  - Assign Tenants: Form to link tenant to building/flat; email notification.
  - View All: Unscoped dashboards.
- **House Owner Features**:
  - Manage Flats: CRUD scoped to their building.
  - Manage Bill Categories: CRUD per building.
  - Manage Bills: Create with auto-dues; update status; record payments.
  - Notifications: Triggered on bill events.
- **Tenant Features**: View assigned bills; email receipts.
- **Permissions**: Use Laravel Gates/Policies (e.g., `php artisan make:policy UserPolicy`).

### 3. Functional Requirements Implementation
- **Isolation**: Middleware + scopes; unit tests for access control.
- **Bills**: Dues via SQL sum; monthly bills grouped by date.
- **Emails**: Events like `BillCreated` with listeners (`php artisan make:event BillCreated`).

### 4. Technical Implementation Notes
- **Structure**:
  - Models: In `app/Models`.
  - Controllers: Namespaced (e.g., `app/Http/Controllers/Admin/HouseOwnerController.php`).
  - Routes: Grouped with middleware (`Route::middleware(['auth', 'tenant'])->group(...)`).
- **Testing**: PHPUnit for units (e.g., dues calculation).

## Database Planning

Using MySQL (compatible with PostgreSQL). Single schema. Use Laravel Migrations for setup.

### 1. Tables and Columns

| Table Name | Description | Columns |
|------------|-------------|---------|
| **users** | All users with roles. | - id (bigint unsigned auto_increment PK)<br>- name (varchar(255))<br>- email (varchar(255) unique)<br>- password (varchar(255))<br>- role (enum('admin', 'house_owner', 'tenant'))<br>- contact (varchar(255) nullable)<br>- remember_token (varchar(100) nullable)<br>- created_at (timestamp nullable)<br>- updated_at (timestamp nullable) |
| **buildings** | One per house owner. | - id (bigint unsigned auto_increment PK)<br>- name (varchar(255))<br>- address (text nullable)<br>- owner_id (bigint unsigned FK to users.id)<br>- tenant_id (bigint unsigned for isolation)<br>- created_at (timestamp nullable)<br>- updated_at (timestamp nullable) |
| **flats** | Flats in buildings. | - id (bigint unsigned auto_increment PK)<br>- building_id (bigint unsigned FK to buildings.id)<br>- flat_number (varchar(255))<br>- owner_name (varchar(255) nullable)<br>- owner_contact (varchar(255) nullable)<br>- tenant_id (bigint unsigned for isolation)<br>- created_at (timestamp nullable)<br>- updated_at (timestamp nullable)<br>- deleted_at (timestamp nullable) (soft deletes) |
| **tenant_assignments** | Tenant assignments. | - id (bigint unsigned auto_increment PK)<br>- tenant_id (bigint unsigned FK to users.id)<br>- building_id (bigint unsigned FK to buildings.id)<br>- flat_id (bigint unsigned FK to flats.id nullable)<br>- assigned_at (timestamp)<br>- created_at (timestamp nullable)<br>- updated_at (timestamp nullable) |
| **bill_categories** | Categories per building. | - id (bigint unsigned auto_increment PK)<br>- building_id (bigint unsigned FK to buildings.id)<br>- name (varchar(255))<br>- tenant_id (bigint unsigned for isolation)<br>- created_at (timestamp nullable)<br>- updated_at (timestamp nullable) |
| **bills** | Bills for flats. | - id (bigint unsigned auto_increment PK)<br>- flat_id (bigint unsigned FK to flats.id)<br>- month (date)<br>- category_id (bigint unsigned FK to bill_categories.id)<br>- amount (decimal(10,2))<br>- due_amount (decimal(10,2) default 0)<br>- status (enum('unpaid', 'paid'))<br>- notes (text nullable)<br>- paid_at (timestamp nullable)<br>- tenant_id (bigint unsigned for isolation)<br>- created_at (timestamp nullable)<br>- updated_at (timestamp nullable) |
| **payments** | Payment records. | - id (bigint unsigned auto_increment PK)<br>- bill_id (bigint unsigned FK to bills.id)<br>- paid_amount (decimal(10,2))<br>- payment_date (timestamp)<br>- payment_month (int)<br>- payment_year (int)<br>- status (enum('pending', 'paid') default 'pending')<br>- tenant_id (bigint unsigned for isolation)<br>- created_at (timestamp nullable)<br>- updated_at (timestamp nullable) |

### 2. Relationships
- Defined via Eloquent: e.g., `User::hasOne(Building::class, 'owner_id')`.
- Cascade deletes where safe (e.g., building delete cascades flats).

### 3. Indexes and Optimizations
- Indexes: Foreign keys indexed automatically; add composites like `(flat_id, status)` on bills for dues.
- Optimizations: Use `SUM` for dues; eager load relations.

### 4. Migrations and Seeders
- Order: Users > Buildings > Flats > etc.
- Seeders: Sample data as described.
- Generate SQL: `php artisan schema:dump`.

This SRS and plan is ready for implementation in Laravel 12. Start with `php artisan make:migration` for each table.
