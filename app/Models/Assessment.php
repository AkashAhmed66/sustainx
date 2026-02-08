<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'factory_id',
        'year',
        'period',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the factory that owns the assessment.
     */
    public function factory()
    {
        return $this->belongsTo(Factory::class);
    }

    /**
     * Get the answers for the assessment.
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Get the calculation results for the assessment.
     */
    public function calculationResults()
    {
        return $this->hasMany(CalculationResult::class);
    }
}
