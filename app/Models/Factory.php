<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'factory_type_id',
        'country_id',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the factory type that owns the factory.
     */
    public function factoryType()
    {
        return $this->belongsTo(FactoryType::class);
    }

    /**
     * Get the country that owns the factory.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * The users that belong to the factory.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the assessments for the factory.
     */
    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
}
