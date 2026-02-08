<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'subsection_id',
        'name',
        'description',
        'order_no',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the subsection that owns the item.
     */
    public function subsection()
    {
        return $this->belongsTo(Subsection::class);
    }

    /**
     * Get the questions for the item.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Get the answers for the item.
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Get the calculation results for the item.
     */
    public function calculationResults()
    {
        return $this->hasMany(CalculationResult::class);
    }
}
