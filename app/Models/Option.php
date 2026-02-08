<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'option_text',
        'option_value',
        'order_no',
    ];

    protected $casts = [
        'option_value' => 'decimal:4',
    ];

    /**
     * Get the question that owns the option.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the answers for the option.
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
