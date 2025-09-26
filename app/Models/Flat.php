<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flat extends Model
{
    protected $fillable = [
        'building_id',
        'flat_number',
        'floor',
        'rent_amount',
        'description',
        'status',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function tenantAssignments(): HasMany
    {
        return $this->hasMany(TenantAssignment::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
}
