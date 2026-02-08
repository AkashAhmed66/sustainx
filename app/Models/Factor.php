<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factor extends Model
{
    use HasFactory;

    protected $fillable = [
        'equation_id',
        'sn',
        'operation',
        'factor_value',
        'country_id',
    ];

    protected $casts = [
        'factor_value' => 'decimal:4',
    ];

    /**
     * Get the equation that owns the factor.
     */
    public function equation()
    {
        return $this->belongsTo(Equation::class);
    }

    /**
     * Get the country for the factor (if country-specific).
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
