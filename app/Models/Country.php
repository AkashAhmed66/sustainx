<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'iso_code',
    ];

    /**
     * Get the factories for the country.
     */
    public function factories()
    {
        return $this->hasMany(Factory::class);
    }

    /**
     * Get the factors for the country.
     */
    public function factors()
    {
        return $this->hasMany(Factor::class);
    }
}
