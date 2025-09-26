<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'description',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function flats(): HasMany
    {
        return $this->hasMany(Flat::class);
    }

    public function tenantAssignments(): HasMany
    {
        return $this->hasMany(TenantAssignment::class);
    }
}
