<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'question_text',
        'question_type_id',
        'unit',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the item that owns the question.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the question type that owns the question.
     */
    public function questionType()
    {
        return $this->belongsTo(QuestionType::class);
    }

    /**
     * Get the options for the question (MCQ only).
     */
    public function options()
    {
        return $this->hasMany(Option::class)->orderBy('order_no');
    }

    /**
     * Get the equation for the question (if exists).
     */
    public function equation()
    {
        return $this->hasOne(Equation::class);
    }

    /**
     * Get the answers for the question.
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Get the calculation results for the question.
     */
    public function calculationResults()
    {
        return $this->hasMany(CalculationResult::class);
    }
}
