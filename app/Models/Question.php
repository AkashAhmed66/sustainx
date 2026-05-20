<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'sl_no',
        'item_id',
        'subsection_id',
        'parent_question_id',
        'child_order_no',
        'question_text',
        'question_type_id',
        'is_main_question',
        'depends_on_question_id',
        'depends_on_option_id',
        'input_unit',
        'output_unit',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'sl_no' => 'string',
        'child_order_no' => 'integer',
        'is_main_question' => 'boolean',
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
     * Get the subsection that owns the question.
     */
    public function subsection()
    {
        return $this->belongsTo(Subsection::class);
    }

    /**
     * Get the question type that owns the question.
     */
    public function questionType()
    {
        return $this->belongsTo(QuestionType::class);
    }

    /**
     * Get the mother/parent question for child rows.
     */
    public function parentQuestion()
    {
        return $this->belongsTo(Question::class, 'parent_question_id');
    }

    /**
     * Get all child questions under a mother question.
     */
    public function childQuestions()
    {
        return $this->hasMany(Question::class, 'parent_question_id')
            ->orderBy('child_order_no')
            ->orderBy('id');
    }

    /**
     * Get the parent question that controls visibility of this question.
     */
    public function dependsOnQuestion()
    {
        return $this->belongsTo(Question::class, 'depends_on_question_id');
    }

    /**
     * Get the parent option that controls visibility of this question.
     */
    public function dependsOnOption()
    {
        return $this->belongsTo(Option::class, 'depends_on_option_id');
    }

    /**
     * Get questions that depend on this question.
     */
    public function dependentQuestions()
    {
        return $this->hasMany(Question::class, 'depends_on_question_id');
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
     * Get supporting documents uploaded for this question entity.
     */
    public function supportingDocuments()
    {
        return $this->hasMany(SupportingDocument::class)->orderByDesc('created_at');
    }

    /**
     * Get the calculation results for the question.
     */
    public function calculationResults()
    {
        return $this->hasMany(CalculationResult::class);
    }

}
