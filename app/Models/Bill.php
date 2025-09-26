<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    protected $fillable = [
        'flat_id',
        'category_id',
        'title',
        'description',
        'amount',
        'due_date',
        'status',
        'carried_forward_to',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function flat(): BelongsTo
    {
        return $this->belongsTo(Flat::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BillCategory::class, 'category_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
