<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'question_id',
        'item_id',
        'numeric_value',
        'text_value',
        'option_id',
    ];

    protected $casts = [
        'numeric_value' => 'decimal:4',
    ];

    /**
     * Get the assessment that owns the answer.
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the question that owns the answer.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the item that owns the answer.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the option that owns the answer (for MCQ).
     */
    public function option()
    {
        return $this->belongsTo(Option::class);
    }
}
