<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'order_no',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the subsections for the section.
     */
    public function subsections()
    {
        return $this->hasMany(Subsection::class)->orderBy('order_no');
    }
}
