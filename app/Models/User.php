<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'contact',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is house owner
     */
    public function isHouseOwner(): bool
    {
        return $this->role === 'house_owner';
    }

    /**
     * Check if user is tenant
     */
    public function isTenant(): bool
    {
        return $this->role === 'tenant';
    }

    /**
     * Get tenant ID for multi-tenant isolation
     */
    public function getTenantId(): ?int
    {
        if ($this->isAdmin()) {
            return null; // Admin can access all data
        }

        if ($this->isHouseOwner()) {
            return $this->id; // House owner's tenant_id is their own ID
        }

        if ($this->isTenant()) {
            // For tenants, we need to get the house owner's ID from tenant_assignments
            // This will be implemented when we create the tenant_assignments relationship
            return null;
        }

        return null;
    }

    /**
     * Scope for admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope for house owner users
     */
    public function scopeHouseOwners($query)
    {
        return $query->where('role', 'house_owner');
    }

    /**
     * Scope for tenant users
     */
    public function scopeTenants($query)
    {
        return $query->where('role', 'tenant');
    }

    /**
     * Get buildings owned by this user (for house owners)
     */
    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class, 'owner_id');
    }

    /**
     * Get tenant assignments (for tenants)
     */
    public function tenantAssignments(): HasMany
    {
        return $this->hasMany(TenantAssignment::class, 'tenant_id');
    }

    /**
     * Get the tenant's assigned building
     */
    public function assignedBuilding(): HasOneThrough
    {
        return $this->hasOneThrough(
            Building::class,
            TenantAssignment::class,
            'tenant_id', // Foreign key on tenant_assignments table
            'id', // Foreign key on buildings table
            'id', // Local key on users table
            'building_id' // Local key on tenant_assignments table
        );
    }

    /**
     * Create API token for the user
     */
    public function createApiToken(string $name = 'api-token'): string
    {
        return $this->createToken($name)->plainTextToken;
    }

    /**
     * Get all API tokens for the user
     */
    public function getApiTokens()
    {
        return $this->tokens;
    }

    /**
     * Revoke all API tokens for the user
     */
    public function revokeAllTokens(): void
    {
        $this->tokens()->delete();
    }

    /**
     * Revoke current token
     */
    public function revokeCurrentToken(): void
    {
        // Revoke all tokens for simplicity
        $this->tokens()->delete();
    }
}
