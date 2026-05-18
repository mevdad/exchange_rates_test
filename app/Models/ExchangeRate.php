<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExchangeRate extends Model
{
    use HasFactory;
    protected $fillable = [
        'from_currency_id',
        'to_currency_id',
        'rate',
        'date',
    ];

    protected $casts = [
        'rate' => 'decimal:8',
        'date' => 'date',
    ];

    /**
     * Get the source currency
     */
    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    /**
     * Get the target currency
     */
    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }

    /**
     * Scope to get rates for a specific date range
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope to get rates for a specific currency pair
     */
    public function scopeForCurrencyPair($query, $fromId, $toId)
    {
        return $query->where('from_currency_id', $fromId)
                     ->where('to_currency_id', $toId);
    }
}
