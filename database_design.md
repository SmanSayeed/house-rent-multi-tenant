# Database Design - Multi-Tenant Flat & Bill Management System

## Overview

This document outlines the complete database design for the Multi-Tenant Flat & Bill Management System. The system uses a **column-based multi-tenant approach** where data isolation is enforced through a `tenant_id` column in relevant tables, ensuring that different house owners' data remains completely segregated.

## Multi-Tenancy Strategy

### Column-Based Isolation
- **Single Database**: All tenants share one database instance
- **Tenant ID Column**: Each table includes a `tenant_id` column for data isolation
- **Global Scopes**: Eloquent models use global scopes to automatically filter data by tenant
- **Middleware**: `TenantMiddleware` ensures proper tenant context based on authenticated user

### Benefits
- **Cost Effective**: Single database reduces infrastructure costs
- **Easy Maintenance**: Centralized database management
- **Scalable**: Can handle thousands of records efficiently
- **Simple Setup**: No complex subdomain or database-per-tenant routing

## Database Schema

### 1. Users Table
**Purpose**: Stores all system users (admins, house owners, tenants)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'house_owner', 'tenant') NOT NULL,
    contact VARCHAR(255) NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Indexes**:
- `PRIMARY KEY (id)`
- `UNIQUE KEY users_email_unique (email)`
- `KEY users_role_index (role)`

**Multi-Tenant Notes**:
- Admins don't have tenant_id (can access all data)
- House owners and tenants will have tenant_id in related tables

### 2. Buildings Table
**Purpose**: One building per house owner, contains building information

```sql
CREATE TABLE buildings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT NULL,
    owner_id BIGINT UNSIGNED NOT NULL,
    tenant_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX buildings_owner_id_index (owner_id),
    INDEX buildings_tenant_id_index (tenant_id)
);
```

**Relationships**:
- `owner_id` → `users.id` (House owner who owns this building)
- `tenant_id` → `users.id` (For multi-tenant isolation)

### 3. Flats Table
**Purpose**: Individual flats within buildings

```sql
CREATE TABLE flats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    building_id BIGINT UNSIGNED NOT NULL,
    flat_number VARCHAR(255) NOT NULL,
    owner_name VARCHAR(255) NULL,
    owner_contact VARCHAR(255) NULL,
    tenant_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE CASCADE,
    INDEX flats_building_id_index (building_id),
    INDEX flats_tenant_id_index (tenant_id),
    INDEX flats_deleted_at_index (deleted_at)
);
```

**Features**:
- **Soft Deletes**: `deleted_at` column for soft deletion
- **Owner Information**: Optional fields for flat owner details
- **Multi-Tenant**: Isolated by `tenant_id`

### 4. Tenant Assignments Table
**Purpose**: Links tenants to specific buildings and flats

```sql
CREATE TABLE tenant_assignments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    building_id BIGINT UNSIGNED NOT NULL,
    flat_id BIGINT UNSIGNED NULL,
    assigned_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE CASCADE,
    FOREIGN KEY (flat_id) REFERENCES flats(id) ON DELETE SET NULL,
    INDEX tenant_assignments_tenant_id_index (tenant_id),
    INDEX tenant_assignments_building_id_index (building_id),
    INDEX tenant_assignments_flat_id_index (flat_id)
);
```

**Business Rules**:
- A tenant can be assigned to a building without specific flat
- A tenant can be assigned to a specific flat within a building
- `flat_id` is nullable for building-level assignments

### 5. Bill Categories Table
**Purpose**: Customizable bill types per building (rent, maintenance, utilities, etc.)

```sql
CREATE TABLE bill_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    building_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    tenant_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE CASCADE,
    INDEX bill_categories_building_id_index (building_id),
    INDEX bill_categories_tenant_id_index (tenant_id),
    UNIQUE KEY bill_categories_building_name_unique (building_id, name)
);
```

**Features**:
- **Building-Specific**: Each building can have its own categories
- **Unique Constraint**: Prevents duplicate category names per building
- **Multi-Tenant**: Isolated by `tenant_id`

### 6. Bills Table
**Purpose**: Monthly bills for flats with dues tracking

```sql
CREATE TABLE bills (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    flat_id BIGINT UNSIGNED NOT NULL,
    month DATE NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    due_amount DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('unpaid', 'paid') DEFAULT 'unpaid',
    notes TEXT NULL,
    paid_at TIMESTAMP NULL,
    tenant_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (flat_id) REFERENCES flats(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES bill_categories(id) ON DELETE CASCADE,
    INDEX bills_flat_id_index (flat_id),
    INDEX bills_category_id_index (category_id),
    INDEX bills_tenant_id_index (tenant_id),
    INDEX bills_month_index (month),
    INDEX bills_status_index (status),
    INDEX bills_flat_status_index (flat_id, status),
    INDEX bills_paid_at_index (paid_at)
);
```

**Key Features**:
- **Dues Tracking**: `due_amount` field for accumulated unpaid amounts
- **Status Management**: Track paid/unpaid status
- **Payment Timestamp**: Record when payment was made
- **Composite Index**: `(flat_id, status)` for efficient dues calculation

### 7. Payments Table
**Purpose**: Payment records linked to bills

```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bill_id BIGINT UNSIGNED NOT NULL,
    paid_amount DECIMAL(10,2) NOT NULL,
    payment_date TIMESTAMP NOT NULL,
    payment_month INT NOT NULL,
    payment_year INT NOT NULL,
    status ENUM('pending', 'paid') DEFAULT 'pending',
    tenant_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (bill_id) REFERENCES bills(id) ON DELETE CASCADE,
    INDEX payments_bill_id_index (bill_id),
    INDEX payments_tenant_id_index (tenant_id),
    INDEX payments_payment_date_index (payment_date),
    INDEX payments_payment_month_index (payment_month),
    INDEX payments_payment_year_index (payment_year),
    INDEX payments_status_index (status),
    INDEX payments_month_year_index (payment_month, payment_year),
    INDEX payments_month_status_index (payment_month, status),
    INDEX payments_year_status_index (payment_year, status)
);
```

**Features**:
- **Payment Tracking**: Record individual payments
- **Partial Payments**: Support for multiple payments per bill
- **Payment Status**: Track payment processing status (pending/paid)
- **Payment Month**: Track which month the payment is for (1-12)
- **Payment Year**: Track which year the payment is for
- **Audit Trail**: Timestamps for payment history

## Relationships Diagram

```
users (1) ──→ (1) buildings
  │                │
  │                │ (1)
  │                └──→ (n) flats
  │                     │
  │                     │ (1)
  │                     └──→ (n) bills
  │                          │
  │                          │ (1)
  │                          └──→ (n) payments
  │
  │ (1) ──→ (n) tenant_assignments ──→ (1) buildings
  │
  └──→ (n) bill_categories (per building)
```

## Multi-Tenant Data Isolation

### Global Scope Implementation
```php
// In each model's boot method
static::addGlobalScope('tenant', function ($query) {
    $query->where('tenant_id', auth()->user()->tenant_id ?? null);
});
```

### Tenant ID Assignment
- **House Owners**: `tenant_id` = their own user ID
- **Tenants**: `tenant_id` = their assigned house owner's user ID
- **Admins**: No tenant_id restrictions (can access all data)

### Middleware Implementation
```php
// TenantMiddleware
public function handle($request, Closure $next)
{
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            // Set tenant context for data isolation
            $this->setTenantContext($user);
        }
    }
    
    return $next($request);
}
```

## Indexes and Performance Optimization

### Primary Indexes
- All primary keys are auto-incrementing BIGINT UNSIGNED
- Foreign key columns are automatically indexed

### Composite Indexes
- `bills(flat_id, status)` - For efficient dues calculation
- `bill_categories(building_id, name)` - Unique constraint + performance

### Query Optimization
- **Eager Loading**: Use `with()` to prevent N+1 queries
- **Pagination**: Implement for large datasets (10-20 items per page)
- **Caching**: Cache frequently accessed data like bill categories

### Example Optimized Queries
```php
// Calculate dues for a flat
$dues = Bill::where('flat_id', $flatId)
    ->where('status', 'unpaid')
    ->sum('amount');

// Get bills with relationships
$bills = Bill::with(['flat', 'category'])
    ->where('tenant_id', $tenantId)
    ->paginate(20);
```

## Data Integrity and Constraints

### Foreign Key Constraints
- **Cascade Deletes**: Buildings → Flats → Bills → Payments
- **Set Null**: Tenant assignments when flat is deleted
- **Restrict**: Prevent deletion of referenced records

### Business Rules
1. **Unique Constraints**:
   - Email addresses must be unique
   - Bill category names must be unique per building
   - One building per house owner

2. **Data Validation**:
   - Amounts must be positive decimals
   - Dates must be valid timestamps
   - Status values must be from predefined enums

3. **Soft Deletes**:
   - Flats use soft deletes to preserve historical data
   - Other tables use hard deletes for data cleanup

## Migration Strategy

### Migration Order
1. `users` - Base user table
2. `buildings` - Depends on users
3. `flats` - Depends on buildings
4. `tenant_assignments` - Depends on users and buildings
5. `bill_categories` - Depends on buildings
6. `bills` - Depends on flats and bill_categories
7. `payments` - Depends on bills

### Sample Data (Seeders)
```php
// Create admin user
User::create([
    'name' => 'System Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin'
]);

// Create house owner
$houseOwner = User::create([
    'name' => 'John Smith',
    'email' => 'john@example.com',
    'password' => Hash::make('password'),
    'role' => 'house_owner'
]);

// Create building
$building = Building::create([
    'name' => 'Sunset Apartments',
    'address' => '123 Main Street, City',
    'owner_id' => $houseOwner->id,
    'tenant_id' => $houseOwner->id
]);
```

## Security Considerations

### Data Isolation
- **Global Scopes**: Prevent cross-tenant data access
- **Middleware**: Enforce tenant context on all requests
- **Policies**: Role-based access control

### Database Security
- **Prepared Statements**: All queries use Eloquent ORM
- **Input Validation**: Laravel validation rules
- **CSRF Protection**: Built-in Laravel CSRF tokens

### Audit Trail
- **Timestamps**: All tables have created_at/updated_at
- **Soft Deletes**: Preserve historical data
- **Payment History**: Complete payment audit trail

## Scalability Considerations

### Current Capacity
- **Records**: Designed for thousands of records per tenant
- **Concurrent Users**: Supports multiple house owners and tenants
- **Performance**: Optimized queries with proper indexing

### Future Scaling
- **Database Partitioning**: Can partition by tenant_id if needed
- **Read Replicas**: Can add read replicas for reporting
- **Caching**: Redis/Memcached for frequently accessed data

## Backup and Recovery

### Backup Strategy
- **Daily Backups**: Full database backups
- **Transaction Logs**: Point-in-time recovery
- **Tenant-Specific Backups**: Individual tenant data export

### Recovery Procedures
- **Full Restore**: Complete database restoration
- **Tenant Restore**: Restore specific tenant data
- **Data Export**: Export tenant data for migration

## Monitoring and Maintenance

### Performance Monitoring
- **Query Performance**: Monitor slow queries
- **Index Usage**: Track index effectiveness
- **Connection Pooling**: Monitor database connections

### Maintenance Tasks
- **Index Optimization**: Regular index analysis
- **Data Cleanup**: Archive old payment records
- **Statistics Update**: Keep table statistics current

---

This database design provides a solid foundation for the Multi-Tenant Flat & Bill Management System, ensuring data integrity, security, and scalability while maintaining the simplicity of a single-database approach.
