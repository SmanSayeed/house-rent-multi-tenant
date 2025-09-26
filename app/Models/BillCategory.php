<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'icon',
        'color',
    ];

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class, 'category_id');
    }
}
