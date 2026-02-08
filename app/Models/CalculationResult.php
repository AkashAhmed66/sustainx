<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculationResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'question_id',
        'item_id',
        'final_value',
    ];

    protected $casts = [
        'final_value' => 'decimal:4',
    ];

    /**
     * Get the assessment that owns the calculation result.
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the question that owns the calculation result.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the item that owns the calculation result.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
