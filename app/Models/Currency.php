<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get exchange rates where this currency is the source
     */
    public function exchangeRatesFrom(): HasMany
    {
        return $this->hasMany(ExchangeRate::class, 'from_currency_id');
    }

    /**
     * Get exchange rates where this currency is the target
     */
    public function exchangeRatesTo(): HasMany
    {
        return $this->hasMany(ExchangeRate::class, 'to_currency_id')
            ->where('date', '>=', now()->subDays(2)->toDateString());
    }

    /**
     * Get only active currencies
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
