<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equation extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'name',
    ];

    /**
     * Get the question that owns the equation.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the factors for the equation.
     */
    public function factors()
    {
        return $this->hasMany(Factor::class)->orderBy('sn');
    }
}
